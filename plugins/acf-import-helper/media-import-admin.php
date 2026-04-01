<?php
/**
 * CSV-Guided Media Import Tool
 * 
 * Uses the original CSV export to map posts to their exact media files
 * No more guessing - we know exactly which file belongs to which post!
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CSV_Guided_Media_Import {
    
    private $staging_url;
    private $staging_auth;
    private $log_file;
    private $csv_data = [];
    
    public function __construct($staging_url, $staging_user = null, $staging_pass = null) {
        $this->staging_url = rtrim($staging_url, '/');
        $this->staging_auth = null;
        $this->log_file = WP_CONTENT_DIR . '/csv-guided-import.log';
        
        if ($staging_user && $staging_pass) {
            $this->staging_auth = [
                'username' => $staging_user,
                'password' => $staging_pass
            ];
        }
    }
    
    /**
     * Handle uploaded CSV file
     */
    public function handle_csv_upload($uploaded_file) {
        if (!isset($uploaded_file['tmp_name']) || !is_uploaded_file($uploaded_file['tmp_name'])) {
            return ['success' => false, 'error' => 'No file uploaded or upload failed'];
        }
        
        // Validate file type
        $file_info = pathinfo($uploaded_file['name']);
        if (strtolower($file_info['extension']) !== 'csv') {
            return ['success' => false, 'error' => 'Please upload a CSV file'];
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = WP_CONTENT_DIR . '/csv-imports/';
        if (!file_exists($upload_dir)) {
            wp_mkdir_p($upload_dir);
        }
        
        // Generate unique filename
        $filename = 'import-' . date('Y-m-d-H-i-s') . '-' . sanitize_file_name($file_info['filename']) . '.csv';
        $target_file = $upload_dir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
            return ['success' => false, 'error' => 'Failed to save uploaded file'];
        }
        
        $this->log("CSV file uploaded successfully: " . $target_file);
        
        return [
            'success' => true,
            'file_path' => $target_file,
            'filename' => $filename
        ];
    }
    
    /**
     * Load and parse the CSV file
     */
    public function load_csv_mapping($csv_file_path) {
        if (!file_exists($csv_file_path)) {
            return ['success' => false, 'error' => 'CSV file not found: ' . $csv_file_path];
        }
        
        $this->log("Loading CSV mapping from: " . $csv_file_path);
        
        $csv_data = [];
        if (($handle = fopen($csv_file_path, 'r')) !== FALSE) {
            $headers = fgetcsv($handle); // Read header row
            $this->log("CSV headers: " . implode(', ', $headers));
            
            $row_count = 0;
            while (($data = fgetcsv($handle)) !== FALSE && $row_count < 1000) { // Limit to 1000 rows for safety
                if (count($data) >= 6) {
                    $csv_data[] = [
                        'post_id' => intval($data[0]),
                        'post_title' => $data[1],
                        'post_date' => $data[2],
                        'attachment_id' => intval($data[3]),
                        'attachment_title' => $data[4],
                        'attachment_url' => $data[5]
                    ];
                }
                $row_count++;
            }
            fclose($handle);
        }
        
        $this->csv_data = $csv_data;
        $this->log("Loaded " . count($csv_data) . " CSV mapping records");
        
        return [
            'success' => true,
            'total_records' => count($csv_data),
            'sample_records' => array_slice($csv_data, 0, 5)
        ];
    }
    
    /**
     * FIXED: Analyze current posts against CSV data - MATCH BY TITLE instead of ID
     */
    public function analyze_posts_vs_csv() {
        if (empty($this->csv_data)) {
            return ['success' => false, 'error' => 'No CSV data loaded'];
        }
        
        global $wpdb;
        
        // Get current posts - check palmshield_resources specifically
        $current_posts = $wpdb->get_results("
            SELECT p.ID, p.post_title, p.post_type, pm.meta_value as current_media_id
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'resource_document'
            WHERE p.post_type = 'palmshield_resources'
            AND p.post_status = 'publish'
            ORDER BY p.ID ASC
        ");
        
        $this->log("Found " . count($current_posts) . " current palmshield_resources posts");
        $this->log("CSV contains " . count($this->csv_data) . " records");
        
        $analysis = [
            'total_current_posts' => count($current_posts),
            'total_csv_records' => count($this->csv_data),
            'posts_found_by_title' => 0,
            'posts_needing_import' => 0,
            'posts_already_have_media' => 0,
            'csv_records_no_title_match' => 0,
            'import_plan' => []
        ];
        
        // Create lookup by TITLE (normalized) - this is the key fix
        $current_posts_by_title = [];
        foreach ($current_posts as $post) {
            $normalized_title = $this->normalize_title($post->post_title);
            $current_posts_by_title[$normalized_title] = $post;
        }
        
        $this->log("Created title lookup for " . count($current_posts_by_title) . " posts");
        
        // Process EACH CSV record - match by TITLE not ID
        foreach ($this->csv_data as $csv_record) {
            $csv_title = $this->normalize_title($csv_record['post_title']);
            
            if (isset($current_posts_by_title[$csv_title])) {
                $post = $current_posts_by_title[$csv_title];
                $analysis['posts_found_by_title']++;
                
                $this->log("Matched CSV title '{$csv_record['post_title']}' with post ID {$post->ID}");
                
                // Check if post already has valid media
                $has_valid_media = false;
                if (!empty($post->current_media_id)) {
                    $media_exists = get_post($post->current_media_id);
                    if ($media_exists && $media_exists->post_type === 'attachment') {
                        $has_valid_media = true;
                        $this->log("Post {$post->ID} already has valid media (ID: " . $post->current_media_id . ")");
                    }
                }
                
                if ($has_valid_media) {
                    $analysis['posts_already_have_media']++;
                } else {
                    $analysis['posts_needing_import']++;
                    
                    // Add this specific CSV record to import plan with CURRENT post ID
                    $analysis['import_plan'][] = [
                        'post_id' => $post->ID,  // Use CURRENT post ID, not CSV post ID
                        'post_title' => $post->post_title,
                        'post_type' => $post->post_type,
                        'current_media_id' => $post->current_media_id,
                        'csv_post_id' => $csv_record['post_id'], // Keep original for reference
                        'target_attachment_id' => $csv_record['attachment_id'],
                        'target_attachment_title' => $csv_record['attachment_title'],
                        'target_attachment_url' => $csv_record['attachment_url'],
                        'import_url' => $this->build_import_url($csv_record['attachment_url'])
                    ];
                }
            } else {
                $analysis['csv_records_no_title_match']++;
                $this->log("No title match found for CSV record: '{$csv_record['post_title']}'");
            }
        }
        
        $this->log("Analysis Results:");
        $this->log("- Total CSV records: " . $analysis['total_csv_records']);
        $this->log("- CSV records matched by title: " . $analysis['posts_found_by_title']);
        $this->log("- CSV records needing import: " . count($analysis['import_plan']));
        $this->log("- Posts already with media: " . $analysis['posts_already_have_media']);
        $this->log("- CSV records with no title match: " . $analysis['csv_records_no_title_match']);
        
        return $analysis;
    }
    
    /**
     * NEW: Normalize titles for matching
     */
    private function normalize_title($title) {
        // Remove extra spaces, convert to lowercase, remove special characters
        $normalized = strtolower(trim($title));
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return $normalized;
    }
    
    /**
     * NEW: Add this method to help diagnose title matching issues
     */
    public function diagnose_title_matching() {
        if (empty($this->csv_data)) {
            return ['success' => false, 'error' => 'No CSV data loaded'];
        }
        
        global $wpdb;
        
        // Get current posts
        $current_posts = $wpdb->get_results("
            SELECT p.ID, p.post_title
            FROM {$wpdb->posts} p
            WHERE p.post_type = 'palmshield_resources'
            AND p.post_status = 'publish'
            ORDER BY p.post_title ASC
        ");
        
        $this->log("=== TITLE MATCHING DIAGNOSTIC ===");
        $this->log("Current posts: " . count($current_posts));
        $this->log("CSV records: " . count($this->csv_data));
        
        // Show first 10 current post titles
        $this->log("\n--- CURRENT POST TITLES (first 10) ---");
        for ($i = 0; $i < min(10, count($current_posts)); $i++) {
            $this->log("ID {$current_posts[$i]->ID}: '{$current_posts[$i]->post_title}'");
        }
        
        // Show first 10 CSV titles
        $this->log("\n--- CSV TITLES (first 10) ---");
        for ($i = 0; $i < min(10, count($this->csv_data)); $i++) {
            $csv_record = $this->csv_data[$i];
            $this->log("CSV ID {$csv_record['post_id']}: '{$csv_record['post_title']}'");
        }
        
        // Test a few matches
        $this->log("\n--- TESTING MATCHES ---");
        $current_titles = [];
        foreach ($current_posts as $post) {
            $current_titles[$this->normalize_title($post->post_title)] = $post->ID;
        }
        
        $matches = 0;
        $no_matches = [];
        
        foreach (array_slice($this->csv_data, 0, 20) as $csv_record) { // Test first 20
            $normalized_csv_title = $this->normalize_title($csv_record['post_title']);
            
            if (isset($current_titles[$normalized_csv_title])) {
                $matches++;
                $this->log("✅ MATCH: '{$csv_record['post_title']}' -> Post ID {$current_titles[$normalized_csv_title]}");
            } else {
                $no_matches[] = $csv_record['post_title'];
                $this->log("❌ NO MATCH: '{$csv_record['post_title']}' (normalized: '$normalized_csv_title')");
            }
        }
        
        $this->log("\n--- SUMMARY ---");
        $this->log("Matches found in first 20: $matches");
        $this->log("No matches: " . count($no_matches));
        
        if (!empty($no_matches)) {
            $this->log("\nTitles that didn't match:");
            foreach ($no_matches as $title) {
                $this->log("- '$title'");
            }
        }
        
        return [
            'success' => true,
            'matches' => $matches,
            'no_matches' => count($no_matches),
            'total_tested' => min(20, count($this->csv_data))
        ];
    }
    
    /**
     * Build the full import URL from the CSV attachment URL
     */
    private function build_import_url($attachment_url) {
        // Handle different URL formats
        if (strpos($attachment_url, 'http') === 0) {
            // Already a full URL
            return $attachment_url;
        } elseif (strpos($attachment_url, '/') === 0) {
            // Absolute path - add staging domain
            return $this->staging_url . $attachment_url;
        } else {
            // Relative path - assume it's in uploads
            return $this->staging_url . '/wp-content/uploads/' . $attachment_url;
        }
    }
    
    /**
     * FIXED: Import media for a specific post using specific CSV record
     */
    public function import_media_for_post_with_csv_record($post_id, $csv_record) {
        $post_id = intval($post_id);
        
        $this->log("Importing media for post $post_id using specific CSV record");
        $this->log("Target file: " . $csv_record['attachment_title']);
        $this->log("Original attachment ID: " . $csv_record['attachment_id']);
        
        // Build the import URL
        $import_url = $this->build_import_url($csv_record['attachment_url']);
        $this->log("Import URL: " . $import_url);
        
        // Check if we already have an attachment with this original filename
        $existing_attachment = $this->find_existing_attachment_by_filename($csv_record['attachment_title']);
        if ($existing_attachment) {
            $this->log("Found existing attachment with same filename (ID: " . $existing_attachment->ID . ")");
            
            // Update the ACF field to use existing attachment
            $acf_success = update_field('resource_document', $existing_attachment->ID, $post_id);
            
            return [
                'success' => true,
                'new_media_id' => $existing_attachment->ID,
                'reused_existing' => true,
                'file_url' => $import_url,
                'title' => $csv_record['attachment_title'],
                'acf_updated' => $acf_success
            ];
        }
        
        // Download and import the file
        $import_result = $this->download_and_import_file(
            $import_url,
            $csv_record['attachment_title']
        );
        
        if ($import_result['success']) {
            // Update the ACF field
            $acf_success = update_field('resource_document', $import_result['new_media_id'], $post_id);
            $import_result['acf_updated'] = $acf_success;
            
            $this->log("✅ Successfully imported and linked media for post $post_id");
        } else {
            $this->log("❌ Failed to import media for post $post_id: " . $import_result['error']);
        }
        
        return $import_result;
    }
    
    /**
     * OLD method - kept for backward compatibility
     */
    public function import_media_for_post($post_id) {
        $post_id = intval($post_id);
        
        // Find CSV record for this post
        $csv_record = null;
        foreach ($this->csv_data as $record) {
            if ($record['post_id'] === $post_id) {
                $csv_record = $record;
                break;
            }
        }
        
        if (!$csv_record) {
            return [
                'success' => false,
                'error' => 'No CSV data found for post ID: ' . $post_id
            ];
        }
        
        return $this->import_media_for_post_with_csv_record($post_id, $csv_record);
    }
    
    /**
     * NEW: Find existing attachment by filename to avoid duplicates
     */
    private function find_existing_attachment_by_filename($filename) {
        global $wpdb;
        
        // Clean the filename (remove extension for search)
        $clean_filename = pathinfo($filename, PATHINFO_FILENAME);
        
        // Search for existing attachments with similar titles
        $existing = $wpdb->get_row($wpdb->prepare("
            SELECT ID, post_title 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND (post_title = %s OR post_title LIKE %s)
            LIMIT 1
        ", $clean_filename, $clean_filename . '%'));
        
        if ($existing) {
            return $existing;
        }
        
        return false;
    }
    
    /**
     * Download and import file
     */
    private function download_and_import_file($file_url, $title) {
        $this->log("Downloading: " . $file_url);
        
        // Set timeout
        set_time_limit(120);
        
        // Download file
        $response = $this->make_request($file_url, 'GET', 60);
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return [
                'success' => false,
                'error' => 'Download failed: HTTP ' . wp_remote_retrieve_response_code($response)
            ];
        }
        
        $file_content = wp_remote_retrieve_body($response);
        if (empty($file_content)) {
            return ['success' => false, 'error' => 'Empty file downloaded'];
        }
        
        // Create temp file
        $temp_file = wp_tempnam(basename($file_url));
        if (!file_put_contents($temp_file, $file_content)) {
            return ['success' => false, 'error' => 'Could not write temp file'];
        }
        
        // Import to WordPress
        $attachment_id = $this->import_to_wordpress($temp_file, $file_url, $title);
        
        // Clean up
        @unlink($temp_file);
        
        if (is_wp_error($attachment_id) || !$attachment_id) {
            return ['success' => false, 'error' => 'WordPress import failed'];
        }
        
        return [
            'success' => true,
            'new_media_id' => $attachment_id,
            'file_url' => $file_url,
            'title' => $title
        ];
    }
    
    /**
     * FIXED: Import to WordPress - prevent duplicate filename issues
     */
    private function import_to_wordpress($temp_file, $original_url, $title) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        // Get clean filename from title, not URL
        $file_extension = pathinfo($original_url, PATHINFO_EXTENSION);
        $clean_title = sanitize_file_name($title);
        
        // If title doesn't have extension, add it
        if (!pathinfo($clean_title, PATHINFO_EXTENSION) && $file_extension) {
            $clean_title .= '.' . $file_extension;
        }
        
        // Disable thumbnail generation for PDFs to prevent timeouts
        if (strtolower($file_extension) === 'pdf') {
            add_filter('wp_image_editors', '__return_empty_array');
            add_filter('intermediate_image_sizes_advanced', '__return_empty_array');
        }
        
        $file_type = wp_check_filetype($clean_title);
        $file_array = [
            'name' => $clean_title,  // Use clean title instead of basename(original_url)
            'tmp_name' => $temp_file,
            'type' => $file_type['type'],
            'size' => filesize($temp_file)
        ];
        
        $this->log("Creating WordPress attachment with filename: " . $clean_title);
        
        $attachment_id = media_handle_sideload($file_array, 0);
        
        // Re-enable image processing
        if (strtolower($file_extension) === 'pdf') {
            remove_filter('wp_image_editors', '__return_empty_array');
            remove_filter('intermediate_image_sizes_advanced', '__return_empty_array');
        }
        
        if (!is_wp_error($attachment_id)) {
            // Update attachment title to match original
            wp_update_post([
                'ID' => $attachment_id,
                'post_title' => $title
            ]);
            
            $this->log("✅ WordPress attachment created with ID: $attachment_id");
        } else {
            $this->log("❌ WordPress attachment creation failed: " . $attachment_id->get_error_message());
        }
        
        return $attachment_id;
    }
    
    /**
     * Make authenticated request
     */
    private function make_request($url, $method = 'GET', $timeout = 30) {
        $args = [
            'method' => $method,
            'timeout' => $timeout,
            'sslverify' => false,
            'redirection' => 5
        ];
        
        if ($this->staging_auth) {
            $args['headers']['Authorization'] = 'Basic ' . base64_encode(
                $this->staging_auth['username'] . ':' . $this->staging_auth['password']
            );
        }
        
        return wp_remote_request($url, $args);
    }
    
    /**
     * Log message
     */
    private function log($message) {
        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = "[$timestamp] $message" . PHP_EOL;
        
        error_log($log_entry);
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// FIXED AJAX handler for single post processing
add_action('wp_ajax_csv_import_single_post', 'handle_csv_import_single_post_fixed');

function handle_csv_import_single_post_fixed() {
    if (!wp_verify_nonce($_POST['nonce'], 'csv_import_nonce')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    $post_id = intval($_POST['post_id']);
    $staging_url = sanitize_text_field($_POST['staging_url']);
    $staging_user = sanitize_text_field($_POST['staging_user']);
    $staging_pass = sanitize_text_field($_POST['staging_pass']);
    $csv_file = sanitize_text_field($_POST['csv_file']);
    
    // NEW: Get the specific CSV record data
    $csv_record = json_decode(stripslashes($_POST['csv_record']), true);
    
    if (!$csv_record) {
        wp_send_json(['success' => false, 'error' => 'Invalid CSV record data']);
        return;
    }
    
    $importer = new CSV_Guided_Media_Import($staging_url, $staging_user, $staging_pass);
    
    // Load CSV data
    $csv_result = $importer->load_csv_mapping($csv_file);
    if (!$csv_result['success']) {
        wp_send_json(['success' => false, 'error' => $csv_result['error']]);
        return;
    }
    
    // Import using the specific CSV record
    $result = $importer->import_media_for_post_with_csv_record($post_id, $csv_record);
    
    wp_send_json($result);
}

// Admin page
add_action('admin_menu', function() {
    add_management_page(
        'CSV-Guided Media Import',
        'CSV-Guided Media Import',
        'manage_options',
        'csv-guided-media-import',
        'csv_guided_media_import_admin_page'
    );
});

function csv_guided_media_import_admin_page() {
    
    if (isset($_POST['analyze_csv']) && wp_verify_nonce($_POST['_wpnonce'], 'csv_import')) {
        $staging_url = sanitize_text_field($_POST['staging_url']);
        $staging_user = sanitize_text_field($_POST['staging_user']);
        $staging_pass = sanitize_text_field($_POST['staging_pass']);
        
        $importer = new CSV_Guided_Media_Import($staging_url, $staging_user, $staging_pass);
        
        // Handle file upload or manual path
        $csv_file_path = '';
        
        if (isset($_FILES['csv_upload']) && $_FILES['csv_upload']['error'] === UPLOAD_ERR_OK) {
            // Handle uploaded file
            $upload_result = $importer->handle_csv_upload($_FILES['csv_upload']);
            if (!$upload_result['success']) {
                echo '<div class="wrap">';
                echo '<h1>❌ File Upload Error</h1>';
                echo '<div class="notice notice-error"><p>' . esc_html($upload_result['error']) . '</p></div>';
                echo '<p><a href="' . admin_url('tools.php?page=csv-guided-media-import') . '" class="button">← Back</a></p>';
                echo '</div>';
                return;
            }
            $csv_file_path = $upload_result['file_path'];
            
        } elseif (!empty($_POST['csv_file_path'])) {
            // Handle manual file path
            $csv_file_path = sanitize_text_field($_POST['csv_file_path']);
            
        } else {
            echo '<div class="wrap">';
            echo '<h1>❌ No CSV File Provided</h1>';
            echo '<div class="notice notice-error"><p>Please either upload a CSV file or provide a file path.</p></div>';
            echo '<p><a href="' . admin_url('tools.php?page=csv-guided-media-import') . '" class="button">← Back</a></p>';
            echo '</div>';
            return;
        }
        
        // Load CSV
        $csv_result = $importer->load_csv_mapping($csv_file_path);
        if (!$csv_result['success']) {
            echo '<div class="wrap">';
            echo '<h1>❌ CSV Loading Error</h1>';
            echo '<div class="notice notice-error"><p>' . esc_html($csv_result['error']) . '</p></div>';
            echo '<p><a href="' . admin_url('tools.php?page=csv-guided-media-import') . '" class="button">← Back</a></p>';
            echo '</div>';
            return;
        }
        
        // Show CSV preview
        echo '<div class="wrap">';
        echo '<h1>📄 CSV File Loaded Successfully</h1>';
        echo '<div class="notice notice-success">';
        echo '<p><strong>✅ CSV loaded:</strong> ' . $csv_result['total_records'] . ' records found</p>';
        echo '</div>';
        
        if (!empty($csv_result['sample_records'])) {
            echo '<h3>📋 Sample CSV Data (First 5 Records)</h3>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Post ID</th><th>Post Title</th><th>Attachment ID</th><th>Attachment Title</th><th>Attachment URL</th></tr></thead>';
            echo '<tbody>';
            foreach ($csv_result['sample_records'] as $record) {
                echo '<tr>';
                echo '<td>' . $record['post_id'] . '</td>';
                echo '<td>' . esc_html($record['post_title']) . '</td>';
                echo '<td>' . $record['attachment_id'] . '</td>';
                echo '<td>' . esc_html($record['attachment_title']) . '</td>';
                echo '<td><small>' . esc_html($record['attachment_url']) . '</small></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        
        // Continue with analysis
        $analysis = $importer->analyze_posts_vs_csv();
        
        echo '<h2>📊 Analysis Results</h2>';
        echo '<div class="notice notice-info">';
        echo '<h3>Summary</h3>';
        echo '<p><strong>Total CSV records:</strong> ' . $analysis['total_csv_records'] . '</p>';
        echo '<p><strong>Total current posts:</strong> ' . $analysis['total_current_posts'] . '</p>';
        echo '<p><strong>CSV records matched by title:</strong> ' . $analysis['posts_found_by_title'] . '</p>';
        echo '<p><strong>Posts already have media:</strong> ' . $analysis['posts_already_have_media'] . '</p>';
        echo '<p><strong>Records needing import:</strong> ' . count($analysis['import_plan']) . '</p>';
        echo '<p><strong>CSV records with no title match:</strong> ' . $analysis['csv_records_no_title_match'] . '</p>';
        echo '</div>';
        
        if ($analysis['csv_records_no_title_match'] > 0) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>⚠️ Title Matching Issues:</strong> ' . $analysis['csv_records_no_title_match'] . ' CSV records could not be matched to existing posts by title. This usually means:</p>';
            echo '<ul>';
            echo '<li>Post titles were changed during import</li>';
            echo '<li>Posts were not imported yet</li>';
            echo '<li>Title formatting differences</li>';
            echo '</ul>';
            echo '<p>Check the log file for details on which titles failed to match.</p>';
            echo '</div>';
        }
        
        if (!empty($analysis['import_plan'])) {
            echo '<h3>📋 Import Plan (' . count($analysis['import_plan']) . ' records)</h3>';
            
            // Show batch import interface
            echo '<div id="batch-progress" style="display: none;">';
            echo '<p><strong>Processing...</strong></p>';
            echo '<div style="background: #f0f0f0; border: 1px solid #ccc; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0;">';
            echo '<div id="progress-bar" style="background: #4CAF50; height: 100%; width: 0%; transition: width 0.3s;"></div>';
            echo '</div>';
            echo '<p id="progress-text">Preparing...</p>';
            echo '</div>';
            
            echo '<div id="batch-results" style="display: none;">';
            echo '<h3>Import Results</h3>';
            echo '<div id="results-summary"></div>';
            echo '</div>';
            
            echo '<button id="start-csv-import" class="button button-primary button-large" style="margin-bottom: 20px;">';
            echo 'Start CSV-Guided Import (' . count($analysis['import_plan']) . ' records)';
            echo '</button>';
            
            echo '<input type="hidden" id="csv-file-path" value="' . esc_attr($csv_file_path) . '">';
            echo '<input type="hidden" id="staging-url" value="' . esc_attr($staging_url) . '">';
            echo '<input type="hidden" id="staging-user" value="' . esc_attr($staging_user) . '">';
            echo '<input type="hidden" id="staging-pass" value="' . esc_attr($staging_pass) . '">';
            
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Post</th><th>Current Media</th><th>Target File</th><th>Import URL</th><th>Status</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($analysis['import_plan'] as $plan) {
                echo '<tr id="post-row-' . $plan['post_id'] . '-' . $plan['target_attachment_id'] . '">';
                echo '<td><strong>' . esc_html($plan['post_title']) . '</strong><br>ID: ' . $plan['post_id'] . '<br><small>CSV ID: ' . $plan['csv_post_id'] . '</small></td>';
                echo '<td>' . ($plan['current_media_id'] ? 'ID: ' . $plan['current_media_id'] . ' (invalid)' : 'None') . '</td>';
                echo '<td><strong>' . esc_html($plan['target_attachment_title']) . '</strong><br><small>Original ID: ' . $plan['target_attachment_id'] . '</small></td>';
                echo '<td><a href="' . esc_url($plan['import_url']) . '" target="_blank">' . esc_html(basename($plan['import_url'])) . '</a></td>';
                echo '<td><span class="status-pending">Pending</span></td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            
            // JavaScript for batch processing
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startButton = document.getElementById('start-csv-import');
                const progressDiv = document.getElementById('batch-progress');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                const resultsDiv = document.getElementById('batch-results');
                const resultsSummary = document.getElementById('results-summary');
                
                const importPlan = <?php echo json_encode($analysis['import_plan']); ?>;
                let currentIndex = 0;
                let successCount = 0;
                let failCount = 0;
                let reuseCount = 0;
                
                console.log('Import plan loaded:', importPlan.length, 'records to process');
                
                if (startButton) {
                    startButton.addEventListener('click', function() {
                        startButton.style.display = 'none';
                        progressDiv.style.display = 'block';
                        processNextRecord();
                    });
                }
                
                function processNextRecord() {
                    if (currentIndex >= importPlan.length) {
                        showResults();
                        return;
                    }
                    
                    const record = importPlan[currentIndex];
                    const progress = Math.round((currentIndex / importPlan.length) * 100);
                    
                    progressBar.style.width = progress + '%';
                    progressText.textContent = `Processing ${currentIndex + 1} of ${importPlan.length}: ${record.post_title}`;
                    
                    console.log('Processing record:', record);
                    
                    // Update row status
                    const row = document.getElementById('post-row-' + record.post_id + '-' + record.target_attachment_id);
                    if (row) {
                        const statusCell = row.querySelector('td:last-child span');
                        if (statusCell) {
                            statusCell.textContent = 'Processing...';
                            statusCell.className = 'status-processing';
                        }
                    }
                    
                    // FIXED: Send the complete CSV record data
                    const formData = new FormData();
                    formData.append('action', 'csv_import_single_post');
                    formData.append('nonce', '<?php echo wp_create_nonce('csv_import_nonce'); ?>');
                    formData.append('post_id', record.post_id);
                    formData.append('staging_url', document.getElementById('staging-url').value);
                    formData.append('staging_user', document.getElementById('staging-user').value);
                    formData.append('staging_pass', document.getElementById('staging-pass').value);
                    formData.append('csv_file', document.getElementById('csv-file-path').value);
                    
                    // NEW: Send the specific CSV record data
                    formData.append('csv_record', JSON.stringify({
                        post_id: record.csv_post_id, // Use original CSV post_id
                        attachment_id: record.target_attachment_id,
                        attachment_title: record.target_attachment_title,
                        attachment_url: record.target_attachment_url
                    }));
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Import result:', data);
                        
                        if (row) {
                            const statusCell = row.querySelector('td:last-child span');
                            if (statusCell) {
                                if (data.success) {
                                    if (data.reused_existing) {
                                        statusCell.textContent = `♻️ Reused existing (Media ID: ${data.new_media_id})`;
                                        statusCell.className = 'status-reused';
                                        reuseCount++;
                                    } else {  
                                        statusCell.textContent = `✅ Success (Media ID: ${data.new_media_id})`;
                                        statusCell.className = 'status-success';
                                    }
                                    successCount++;
                                } else {
                                    statusCell.textContent = `❌ Failed: ${data.error}`;
                                    statusCell.className = 'status-error';
                                    failCount++;
                                }
                            }
                        }
                        
                        currentIndex++;
                        setTimeout(() => processNextRecord(), 1000);
                    })
                    .catch(error => {
                        console.error('Import error:', error);
                        
                        if (row) {
                            const statusCell = row.querySelector('td:last-child span');
                            if (statusCell) {
                                statusCell.textContent = `❌ Error: ${error.message}`;
                                statusCell.className = 'status-error';
                            }
                        }
                        
                        failCount++;
                        currentIndex++;
                        setTimeout(() => processNextRecord(), 1000);
                    });
                }
                
                function showResults() {
                    progressBar.style.width = '100%';
                    progressText.textContent = 'Complete!';
                    
                    resultsSummary.innerHTML = `
                        <div class="notice notice-success">
                            <p><strong>CSV-guided import completed!</strong></p>
                            <p>✅ New imports: ${successCount - reuseCount} | ♻️ Reused existing: ${reuseCount} | ❌ Failed: ${failCount} | Total: ${importPlan.length}</p>
                            <p><strong>Total successful: ${successCount} out of ${importPlan.length}</strong></p>
                        </div>
                    `;
                    
                    resultsDiv.style.display = 'block';
                }
            });
            </script>
            
            <style>
            .status-pending { color: #666; }
            .status-processing { color: #0073aa; font-weight: bold; }
            .status-success { color: #46b450; font-weight: bold; }
            .status-reused { color: #00a0d2; font-weight: bold; }
            .status-error { color: #dc3232; font-weight: bold; }
            </style>
            <?php
            
        } else {
            echo '<div class="notice notice-success">';
            echo '<p>✅ All posts already have valid media files! No imports needed.</p>';
            echo '</div>';
        }
        
        echo '</div>';
        return;
    }
    
    // Main form with file upload
    ?>
    <div class="wrap">
        <h1>📄 CSV-Guided Media Import</h1>
        <p>Import media files using the exact mapping from your original CSV export data.</p>
        
        <div class="notice notice-info">
            <p><strong>🎯 Perfect Accuracy:</strong> This tool uses your CSV data to know exactly which media file belongs to each post. No more guessing!</p>
            <p><strong>🔄 Title Matching:</strong> Posts are matched by title, not ID, so it works even if your posts have new IDs.</p>
        </div>
        
        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field('csv_import'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="csv_upload">Upload CSV File</label>
                    </th>
                    <td>
                        <input type="file" id="csv_upload" name="csv_upload" accept=".csv" class="regular-text">
                        <p class="description">📁 Upload your CSV file directly (recommended method)</p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: center; padding: 20px; color: #666;">
                        <strong>— OR —</strong>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="csv_file_path">Manual File Path</label>
                    </th>
                    <td>
                        <input type="text" id="csv_file_path" name="csv_file_path" 
                               placeholder="/path/to/sql 1.csv" 
                               class="regular-text">
                        <p class="description">🖥️ Full server path to your CSV file (alternative method)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="staging_url">Staging Site URL</label>
                    </th>
                    <td>
                        <input type="url" id="staging_url" name="staging_url" 
                               value="https://honest-number.flywheelstaging.com" 
                               class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="staging_user">Staging Username</label>
                    </th>
                    <td>
                        <input type="text" id="staging_user" name="staging_user" 
                               value="flywheel" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="staging_pass">Staging Password</label>
                    </th>
                    <td>
                        <input type="password" id="staging_pass" name="staging_pass" 
                               value="fierce-brick" class="regular-text" required>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="analyze_csv" class="button button-primary" value="📊 Upload & Analyze CSV">
            </p>
        </form>
        
        <div class="card">
            <h2>📋 How This Works</h2>
            <ol>
                <li><strong>Upload CSV:</strong> Upload your CSV file directly through the browser</li>
                <li><strong>Parse Data:</strong> Reads your CSV with the original post → media mapping</li>
                <li><strong>Match by Title:</strong> Matches CSV posts to current posts by title (not ID)</li>
                <li><strong>Plan Import:</strong> Shows exactly which files will be imported for which posts</li>
                <li><strong>Precise Import:</strong> Uses exact URLs from CSV - no guessing!</li>
            </ol>
            
            <h3>Expected CSV Format:</h3>
            <table class="wp-list-table widefat fixed striped" style="margin-top: 10px;">
                <thead><tr><th>post_id</th><th>post_title</th><th>post_date</th><th>attachment_id</th><th>attachment_title</th><th>attachment_url</th></tr></thead>
                <tbody>
                    <tr><td>447</td><td>PalmSHIELD Brochure</td><td>2020-01-15</td><td>451</td><td>palmshield-brochure22.pdf</td><td>/wp-content/uploads/2020/01/palmshield-brochure22.pdf</td></tr>
                </tbody>
            </table>
            
            <div class="notice notice-success inline" style="margin-top: 15px;">
                <p><strong>✅ New Title Matching Features:</strong></p>
                <ul>
                    <li>Matches posts by title instead of post ID</li>
                    <li>Works even if your posts have new IDs after import</li>
                    <li>Handles minor title formatting differences</li>
                    <li>Shows both current and original post IDs for reference</li>
                </ul>
            </div>
        </div>
    </div>
    
    <style>
    .card {
        background: white;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        padding: 20px;
        margin: 20px 0;
    }
    .notice.inline {
        display: block;
        margin: 5px 0 15px;
        padding: 1px 12px;
    }
    </style>
    <?php
}
?>