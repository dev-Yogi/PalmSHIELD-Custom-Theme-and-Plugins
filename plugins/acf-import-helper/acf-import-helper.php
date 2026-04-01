<?php

/**
 * Plugin Name: ACF Import Helper
 * Description: Custom WP-CLI commands for importing ACF files
 * Version: 1.0
 */

// Add logging to confirm plugin is loaded
error_log('[Plugin loaded] ' . __FILE__);

if (!defined('WP_CLI')) {
    define('WP_CLI', false);
}

if (!class_exists('WP_CLI')) {
    class WP_CLI
    {
        public static function line($msg) {}
        public static function error($msg) {}
        public static function success($msg) {}
        public static function warning($msg) {}
        public static function add_command($name, $class) {}
    }
}

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('acf-import', 'ACF_Import_CLI');
}

// Include admin page - FIXED: Use correct filename
require_once(plugin_dir_path(__FILE__) . 'media-import-admin.php');

// Add action to confirm plugin is loaded
add_action('init', function() {
    error_log('[ACF Import Helper] Plugin initialized');
});

class ACF_Import_CLI
{

    /**
     * Import missing attachments from old site and create ID mapping
     * 
     * ## OPTIONS
     * 
     * --old-site-url=<url>
     * : URL of the old site to download attachments from
     * 
     * [--staging-username=<username>]
     * : Flywheel staging site username (HTTP Basic Auth)
     * 
     * [--staging-password=<password>]
     * : Flywheel staging site password (HTTP Basic Auth)
     * 
     * [--wp-username=<username>]
     * : WordPress admin username
     * 
     * [--wp-password=<password>]
     * : WordPress admin password
     * 
     * [--batch-size=<number>]
     * : Number of attachments to process per batch (default: 10)
     * 
     * [--dry-run]
     * : Run without actually importing files
     * 
     * [--mapping-file=<file>]
     * : File to save the old->new ID mapping (default: id-mapping.json)
     * 
     * [--timeout=<seconds>]
     * : Request timeout in seconds (default: 15)
     * 
     * [--max-concurrent=<number>]
     * : Maximum concurrent downloads (default: 3)
     * 
     * ## EXAMPLES
     * 
     * wp acf-import import_attachments \
     *--old-site-url=https://flywheel:fierce-brick@honest-number.flywheelstaging.com \
     *--user=VanessaK \
     *--wp-password=AmFence_2024 \
     *--dry-run
     * 
     *     # For Flywheel staging with both auth layers
     *     wp acf-import import_attachments --old-site-url=https://staging.site.com --staging-username=flywheel_user --staging-password=flywheel_pass --wp-username=admin --wp-password=admin_pass
     * 
     *     # For regular WordPress site (no staging auth)
     *     wp acf-import import_attachments --old-site-url=https://old-site.com --wp-username=admin --wp-password=admin_pass
     * 
     *     # Without any authentication (public site)
     *     wp acf-import import_attachments --old-site-url=https://old-site.com
     */
    public function import_attachments($args, $assoc_args)
    {
        $old_site_url = isset($assoc_args['old-site-url']) ? $assoc_args['old-site-url'] : null;
        $staging_username = isset($assoc_args['staging-username']) ? $assoc_args['staging-username'] : null;
        $staging_password = isset($assoc_args['staging-password']) ? $assoc_args['staging-password'] : null;
        $wp_username = isset($assoc_args['wp-username']) ? $assoc_args['wp-username'] : null;
        $wp_password = isset($assoc_args['wp-password']) ? $assoc_args['wp-password'] : null;
        $batch_size = isset($assoc_args['batch-size']) ? (int)$assoc_args['batch-size'] : 10;
        $dry_run = isset($assoc_args['dry-run']);
        $mapping_file = isset($assoc_args['mapping-file']) ? $assoc_args['mapping-file'] : 'id-mapping.json';
        $timeout = isset($assoc_args['timeout']) ? (int)$assoc_args['timeout'] : 15;
        $max_concurrent = isset($assoc_args['max-concurrent']) ? (int)$assoc_args['max-concurrent'] : 3;

        if (!$old_site_url) {
            WP_CLI::error("Please provide --old-site-url parameter");
            return;
        }

        WP_CLI::line("Starting attachment import from: $old_site_url");
        WP_CLI::line("Batch size: $batch_size, Timeout: {$timeout}s, Max concurrent: $max_concurrent");

        // Set up authentication configuration
        $auth_config = $this->setup_authentication_config($staging_username, $staging_password, $wp_username, $wp_password);

        if ($dry_run) {
            WP_CLI::line("DRY RUN MODE - No files will be imported");
        }

        // Test connectivity first
        if (!$this->test_site_connectivity($old_site_url, $auth_config, $timeout)) {
            WP_CLI::error("Cannot connect to the old site. Please check your authentication credentials.");
            return;
        }

        // Get WordPress authentication cookies if needed
        $auth_cookies = null;
        if ($wp_username && $wp_password) {
            $auth_cookies = $this->get_auth_cookies_with_staging_auth($old_site_url, $wp_username, $wp_password, $auth_config, $timeout);
            if (!$auth_cookies) {
                WP_CLI::warning("Failed to authenticate with WordPress admin. Trying without WordPress authentication...");
            }
        }

        // Get all missing attachment IDs
        $missing_attachments = $this->get_missing_attachment_ids();

        if (empty($missing_attachments)) {
            WP_CLI::success("No missing attachments found!");
            return;
        }

        WP_CLI::line("Found " . count($missing_attachments) . " missing attachments to import");

        // Process in batches with concurrent downloads
        $batches = array_chunk($missing_attachments, $batch_size);
        $batch_count = count($batches);
        $id_mapping = [];

        foreach ($batches as $batch_index => $batch) {
            $current_batch = $batch_index + 1;
            WP_CLI::line("\nProcessing batch $current_batch of $batch_count...");

            // Process batch with concurrent downloads
            $batch_results = $this->process_batch_concurrent(
                $batch,
                $old_site_url,
                $auth_config,
                $auth_cookies,
                $dry_run,
                $timeout,
                $max_concurrent
            );

            // Add successful imports to mapping
            foreach ($batch_results as $old_id => $new_id) {
                if ($new_id) {
                    $id_mapping[$old_id] = $new_id;
                    WP_CLI::line("  ✓ Mapped old ID $old_id to new ID $new_id");
                } else {
                    WP_CLI::line("  ✗ Failed to import attachment ID $old_id");
                }
            }

            // Brief pause between batches to avoid overwhelming the server
            if ($current_batch < $batch_count) {
                sleep(0.5);
            }
        }

        // Save mapping to file
        if (!empty($id_mapping)) {
            $mapping_json = json_encode($id_mapping, JSON_PRETTY_PRINT);
            file_put_contents($mapping_file, $mapping_json);
            WP_CLI::success("ID mapping saved to: $mapping_file");
            WP_CLI::line("Mapping contains " . count($id_mapping) . " entries");
        }

        WP_CLI::success("Completed attachment import!");
    }

    private function process_batch_concurrent($batch, $old_site_url, $auth_config, $auth_cookies, $dry_run, $timeout, $max_concurrent)
    {
        $results = [];
        $chunks = array_chunk($batch, $max_concurrent);

        foreach ($chunks as $chunk) {
            $processes = [];
            
            // Start concurrent processes
            foreach ($chunk as $attachment_id) {
                $processes[$attachment_id] = $this->import_single_attachment_async(
                    $attachment_id,
                    $old_site_url,
                    $auth_config,
                    $auth_cookies,
                    $dry_run,
                    $timeout
                );
            }

            // Wait for all processes to complete
            foreach ($processes as $attachment_id => $process) {
                $results[$attachment_id] = $process;
            }
        }

        return $results;
    }

    private function import_single_attachment_async($old_attachment_id, $old_site_url, $auth_config, $auth_cookies, $dry_run, $timeout)
    {
        WP_CLI::line("  Processing attachment ID: $old_attachment_id");

        if ($dry_run) {
            WP_CLI::line("    [DRY RUN] Would import attachment ID $old_attachment_id");
            return $old_attachment_id; // Return same ID for dry run
        }

        try {
            // Get attachment info from old site with optimized timeout
            $attachment_info = $this->get_attachment_info_with_dual_auth($old_attachment_id, $old_site_url, $auth_config, $auth_cookies, $timeout);

            if (!$attachment_info) {
                WP_CLI::line("    ✗ Could not get attachment info from old site");
                return false;
            }

            WP_CLI::line("    Found attachment: " . $attachment_info['title']);

            // Download and import the file with optimized settings
            $new_attachment_id = $this->import_file_from_url_with_auth_optimized($attachment_info['url'], $attachment_info['title'], $auth_config, $timeout);

            if ($new_attachment_id) {
                // Update attachment metadata
                wp_update_post([
                    'ID' => $new_attachment_id,
                    'post_title' => $attachment_info['title'],
                    'post_content' => $attachment_info['description'] ?? '',
                    'post_excerpt' => $attachment_info['caption'] ?? ''
                ]);

                return $new_attachment_id;
            } else {
                WP_CLI::line("    ✗ Failed to import file");
                return false;
            }
        } catch (Exception $e) {
            WP_CLI::line("    ✗ Error: " . $e->getMessage());
            return false;
        }
    }

    private function get_missing_attachment_ids()
    {
        global $wpdb;

        // FIXED: Check both post types
        $referenced_attachments = $wpdb->get_col("
            SELECT DISTINCT pm.meta_value 
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE pm.meta_key = 'resource_document' 
            AND pm.meta_value REGEXP '^[0-9]+$'
            AND p.post_type IN ('resources', 'palmshield_resources')
            AND p.post_status = 'publish'
        ");

        if (empty($referenced_attachments)) {
            return [];
        }

        // Get existing attachment IDs
        $existing_attachments = $wpdb->get_col("
            SELECT ID 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND ID IN (" . implode(',', $referenced_attachments) . ")
        ");

        // Find missing ones
        $missing = array_diff($referenced_attachments, $existing_attachments);

        return array_values($missing);
    }

    private function setup_authentication_config($staging_username, $staging_password, $wp_username, $wp_password)
    {
        $config = [
            'has_staging_auth' => false,
            'has_wp_auth' => false,
            'staging_username' => $staging_username,
            'staging_password' => $staging_password,
            'wp_username' => $wp_username,
            'wp_password' => $wp_password
        ];

        if ($staging_username && $staging_password) {
            $config['has_staging_auth'] = true;
            WP_CLI::line("Staging authentication configured");
        }

        if ($wp_username && $wp_password) {
            $config['has_wp_auth'] = true;
            WP_CLI::line("WordPress authentication configured");
        }

        return $config;
    }

    private function test_site_connectivity($site_url, $auth_config, $timeout = 15)
    {
        WP_CLI::line("Testing connectivity to: $site_url");

        $args = [
            'timeout' => $timeout,
            'sslverify' => false
        ];
        
        if ($auth_config['has_staging_auth']) {
            $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
        }

        $response = wp_remote_get($site_url, $args);

        if (is_wp_error($response)) {
            WP_CLI::error("Connection failed: " . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        WP_CLI::line("Response code: $response_code");

        if ($response_code === 200) {
            WP_CLI::success("Site is accessible");
            return true;
        } else {
            WP_CLI::error("Site returned error code: $response_code");
            return false;
        }
    }

    private function get_auth_cookies_with_staging_auth($site_url, $wp_username, $wp_password, $auth_config, $timeout = 15)
    {
        WP_CLI::line("    Attempting WordPress authentication...");

        $login_url = trailingslashit($site_url) . 'wp-login.php';

        $args = [
            'body' => [
                'log' => $wp_username,
                'pwd' => $wp_password,
                'wp-submit' => 'Log In',
                'redirect_to' => trailingslashit($site_url) . 'wp-admin/',
                'testcookie' => '1'
            ],
            'cookies' => [],
            'timeout' => $timeout,
            'sslverify' => false
        ];

        // Add staging authentication if needed
        if ($auth_config['has_staging_auth']) {
            $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
        }

        $response = wp_remote_post($login_url, $args);

        if (is_wp_error($response)) {
            WP_CLI::line("    Authentication failed: " . $response->get_error_message());
            return null;
        }

        $cookies = wp_remote_retrieve_cookies($response);
        if (empty($cookies)) {
            WP_CLI::line("    No authentication cookies received");
            return null;
        }

        WP_CLI::line("    Authentication successful");
        return $cookies;
    }

    private function get_attachment_info_with_dual_auth($attachment_id, $old_site_url, $auth_config, $auth_cookies = null, $timeout = 15)
    {
        // Try to get attachment info via REST API
        $api_url = trailingslashit($old_site_url) . 'wp-json/wp/v2/media/' . $attachment_id;

        WP_CLI::line("    Trying REST API: $api_url");

        $args = [
            'timeout' => $timeout,
            'sslverify' => false
        ];
        
        if ($auth_config['has_staging_auth']) {
            $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
        }
        if ($auth_cookies) {
            $args['cookies'] = $auth_cookies;
        }

        $response = wp_remote_get($api_url, $args);

        if (is_wp_error($response)) {
            WP_CLI::line("    REST API failed: " . $response->get_error_message());
            return $this->get_attachment_info_alternative_with_auth($attachment_id, $old_site_url, $auth_config, $timeout);
        }

        $response_code = wp_remote_retrieve_response_code($response);
        WP_CLI::line("    REST API response code: $response_code");

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || isset($data['code'])) {
            WP_CLI::line("    REST API returned error: " . print_r($data, true));
            return $this->get_attachment_info_alternative_with_auth($attachment_id, $old_site_url, $auth_config, $timeout);
        }

        WP_CLI::line("    REST API success - found attachment");

        return [
            'title' => $data['title']['rendered'] ?? basename($data['source_url']),
            'url' => $data['source_url'],
            'description' => $data['description']['rendered'] ?? '',
            'caption' => $data['caption']['rendered'] ?? ''
        ];
    }

    private function get_attachment_info_alternative_with_auth($attachment_id, $old_site_url, $auth_config, $timeout = 15)
    {
        WP_CLI::line("    Trying alternative URL patterns...");

        // Common file extensions to try
        $extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'zip', 'rar', 'txt', 'rtf'];

        $args = [
            'timeout' => $timeout,
            'sslverify' => false
        ];
        
        if ($auth_config['has_staging_auth']) {
            $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
        }

        foreach ($extensions as $ext) {
            $url = trailingslashit($old_site_url) . 'wp-content/uploads/' . $attachment_id . '.' . $ext;
            WP_CLI::line("    Checking: $url");

            $response = wp_remote_head($url, $args);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                WP_CLI::line("    Found file at: $url");
                return [
                    'title' => basename($url),
                    'url' => $url,
                    'description' => '',
                    'caption' => ''
                ];
            }
        }

        WP_CLI::line("    No files found with any URL pattern");
        return false;
    }

    private function import_file_from_url_with_auth_optimized($file_url, $title, $auth_config, $timeout = 15)
    {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        WP_CLI::line("    Downloading: $file_url");

        $args = [
            'timeout' => $timeout,
            'sslverify' => false
        ];
        
        if ($auth_config['has_staging_auth']) {
            $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
        }

        // Use wp_remote_get with optimized settings for better performance
        $response = wp_remote_get($file_url, $args);

        if (is_wp_error($response)) {
            WP_CLI::line("    Download error: " . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            WP_CLI::line("    Download failed with response code: $response_code");
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            WP_CLI::line("    Download failed - empty response body");
            return false;
        }

        // Create temp file manually for better performance
        $tmp = wp_tempnam(basename($file_url));
        if (file_put_contents($tmp, $body) === false) {
            WP_CLI::line("    Failed to write temp file");
            return false;
        }

        WP_CLI::line("    Downloaded to temp file: $tmp");

        // Set up file array
        $file_array = [
            'name' => basename($file_url),
            'tmp_name' => $tmp
        ];

        WP_CLI::line("    Creating attachment for file: " . $file_array['name']);

        // Import as attachment
        $attachment_id = media_handle_sideload($file_array, 0); // Use 0 as parent post

        // Clean up temp file
        @unlink($tmp);

        if (is_wp_error($attachment_id)) {
            WP_CLI::line("    Attachment creation error: " . $attachment_id->get_error_message());
            return false;
        }

        return $attachment_id;
    }

    /**
     * Test connection to old site with authentication
     * 
     * ## OPTIONS
     * 
     * --old-site-url=<url>
     * : URL of the old site to test
     * 
     * [--staging-username=<username>]
     * : Flywheel staging site username
     * 
     * [--staging-password=<password>]
     * : Flywheel staging site password
     * 
     * [--wp-username=<username>]
     * : WordPress admin username
     * 
     * [--wp-password=<password>]
     * : WordPress admin password
     * 
     * ## EXAMPLES
     * 
     *     wp acf-import test_connection --old-site-url=https://staging.site.com --staging-username=flywheel_user --staging-password=flywheel_pass
     *     wp acf-import test_connection --old-site-url=https://staging.site.com --staging-username=flywheel_user --staging-password=flywheel_pass --wp-username=admin --wp-password=admin_pass
     */
    public function test_connection($args, $assoc_args)
    {
        $old_site_url = isset($assoc_args['old-site-url']) ? $assoc_args['old-site-url'] : null;
        $staging_username = isset($assoc_args['staging-username']) ? $assoc_args['staging-username'] : null;
        $staging_password = isset($assoc_args['staging-password']) ? $assoc_args['staging-password'] : null;
        $wp_username = isset($assoc_args['wp-username']) ? $assoc_args['wp-username'] : null;
        $wp_password = isset($assoc_args['wp-password']) ? $assoc_args['wp-password'] : null;

        if (!$old_site_url) {
            WP_CLI::error("Please provide --old-site-url parameter");
            return;
        }

        WP_CLI::line("Testing connection to: $old_site_url");

        // Set up authentication configuration
        $auth_config = $this->setup_authentication_config($staging_username, $staging_password, $wp_username, $wp_password);

        // Test basic connectivity
        if (!$this->test_site_connectivity($old_site_url, $auth_config)) {
            WP_CLI::error("Basic connectivity test failed");
            return;
        }

        // Test WordPress authentication if credentials provided
        if ($wp_username && $wp_password) {
            $auth_cookies = $this->get_auth_cookies_with_staging_auth($old_site_url, $wp_username, $wp_password, $auth_config);
            if ($auth_cookies) {
                WP_CLI::success("WordPress authentication successful");
            } else {
                WP_CLI::warning("WordPress authentication failed");
            }
        }

        WP_CLI::success("Connection test completed");
    }

    /**
     * Update media references using mapping file
     * 
     * ## OPTIONS
     * 
     * --mapping-file=<file>
     * : Path to the mapping file (default: media-id-mapping.json)
     * 
     * [--dry-run]
     * : Show what would be updated without making changes
     * 
     * ## EXAMPLES
     * 
     *     wp acf-import update_media_references --mapping-file=media-id-mapping.json
     *     wp acf-import update_media_references --mapping-file=media-id-mapping.json --dry-run
     */
    public function update_media_references($args, $assoc_args)
    {
        $mapping_file = isset($assoc_args['mapping-file']) ? $assoc_args['mapping-file'] : 'media-id-mapping.json';
        $dry_run = isset($assoc_args['dry-run']);

        if (!file_exists($mapping_file)) {
            WP_CLI::error("Mapping file not found: $mapping_file");
            return;
        }

        $mapping_content = file_get_contents($mapping_file);
        if (empty($mapping_content)) {
            WP_CLI::error("Mapping file is empty: $mapping_file");
            return;
        }

        $mapping = json_decode($mapping_content, true);
        if (!$mapping) {
            WP_CLI::error("Invalid JSON in mapping file: $mapping_file");
            return;
        }

        WP_CLI::line("Found " . count($mapping) . " mappings to process");

        if ($dry_run) {
            WP_CLI::line("DRY RUN MODE - No changes will be made");
        }

        global $wpdb;
        $success_count = 0;
        $error_count = 0;

        foreach ($mapping as $old_id => $new_id) {
            WP_CLI::line("Updating ID $old_id -> $new_id... ");

            if ($dry_run) {
                WP_CLI::line("  [DRY RUN] Would update meta_value from '$old_id' to '$new_id'");
                $success_count++;
                continue;
            }

            $result = $wpdb->update(
                $wpdb->postmeta,
                ['meta_value' => $new_id],
                [
                    'meta_key' => 'resource_document',
                    'meta_value' => $old_id
                ]
            );

            if ($result !== false) {
                WP_CLI::line("  ✓ SUCCESS");
                $success_count++;
            } else {
                WP_CLI::line("  ✗ ERROR: " . $wpdb->last_error);
                $error_count++;
            }
        }

        WP_CLI::line("\n=== UPDATE SUMMARY ===");
        WP_CLI::line("Successful updates: $success_count");
        WP_CLI::line("Failed updates: $error_count");
        WP_CLI::line("Total processed: " . count($mapping));

        if ($error_count == 0) {
            WP_CLI::success("All media ID updates completed successfully!");
        } else {
            WP_CLI::warning("Some updates failed. Please check the errors above.");
        }
    }

    /**
     * Test performance of import process
     * 
     * ## OPTIONS
     * 
     * --old-site-url=<url>
     * : URL of the old site to test
     * 
     * [--staging-username=<username>]
     * : Flywheel staging site username
     * 
     * [--staging-password=<password>]
     * : Flywheel staging site password
     * 
     * [--wp-username=<username>]
     * : WordPress admin username
     * 
     * [--wp-password=<password>]
     * : WordPress admin password
     * 
     * [--test-count=<number>]
     * : Number of test requests to make (default: 5)
     * 
     * ## EXAMPLES
     * 
     *     wp acf-import performance_test --old-site-url=https://staging.site.com --staging-username=flywheel_user --staging-password=flywheel_pass
     */
    public function performance_test($args, $assoc_args)
    {
        $old_site_url = isset($assoc_args['old-site-url']) ? $assoc_args['old-site-url'] : null;
        $staging_username = isset($assoc_args['staging-username']) ? $assoc_args['staging-username'] : null;
        $staging_password = isset($assoc_args['staging-password']) ? $assoc_args['staging-password'] : null;
        $wp_username = isset($assoc_args['wp-username']) ? $assoc_args['wp-username'] : null;
        $wp_password = isset($assoc_args['wp-password']) ? $assoc_args['wp-password'] : null;
        $test_count = isset($assoc_args['test-count']) ? (int)$assoc_args['test-count'] : 5;

        if (!$old_site_url) {
            WP_CLI::error("Please provide --old-site-url parameter");
            return;
        }

        WP_CLI::line("Starting performance test for: $old_site_url");
        WP_CLI::line("Test count: $test_count");

        // Set up authentication configuration
        $auth_config = $this->setup_authentication_config($staging_username, $staging_password, $wp_username, $wp_password);

        // Test basic connectivity
        $start_time = microtime(true);
        if (!$this->test_site_connectivity($old_site_url, $auth_config, 10)) {
            WP_CLI::error("Basic connectivity test failed");
            return;
        }
        $connectivity_time = microtime(true) - $start_time;
        WP_CLI::line("Connectivity test took: " . round($connectivity_time, 2) . "s");

        // Test REST API performance
        $api_times = [];
        $api_url = trailingslashit($old_site_url) . 'wp-json/wp/v2/media/1';
        
        WP_CLI::line("\nTesting REST API performance...");
        
        for ($i = 0; $i < $test_count; $i++) {
            $start_time = microtime(true);
            
            $args = [
                'timeout' => 10,
                'sslverify' => false
            ];
            
            if ($auth_config['has_staging_auth']) {
                $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
            }
            
            $response = wp_remote_get($api_url, $args);
            $response_time = microtime(true) - $start_time;
            $api_times[] = $response_time;
            
            if (is_wp_error($response)) {
                WP_CLI::line("  Test $i: ERROR - " . $response->get_error_message());
            } else {
                $response_code = wp_remote_retrieve_response_code($response);
                WP_CLI::line("  Test $i: HTTP $response_code - " . round($response_time, 3) . "s");
            }
        }

        // Calculate statistics
        $avg_api_time = array_sum($api_times) / count($api_times);
        $min_api_time = min($api_times);
        $max_api_time = max($api_times);

        WP_CLI::line("\n=== PERFORMANCE SUMMARY ===");
        WP_CLI::line("Connectivity test: " . round($connectivity_time, 2) . "s");
        WP_CLI::line("REST API average: " . round($avg_api_time, 3) . "s");
        WP_CLI::line("REST API min: " . round($min_api_time, 3) . "s");
        WP_CLI::line("REST API max: " . round($max_api_time, 3) . "s");
        
        // Estimate import time
        $missing_attachments = $this->get_missing_attachment_ids();
        $estimated_time = count($missing_attachments) * $avg_api_time * 2; // Factor of 2 for download time
        
        WP_CLI::line("\nEstimated import time for " . count($missing_attachments) . " files: " . round($estimated_time / 60, 1) . " minutes");
        
        if ($avg_api_time > 2) {
            WP_CLI::warning("Slow response times detected. Consider using --timeout=30 for better reliability.");
        } else {
            WP_CLI::success("Performance looks good!");
        }
    }

    /**
     * Import resource documents from old site and update ACF field references
     * 
     * ## OPTIONS
     * 
     * --old-site-url=<url>
     * : URL of the old site to download files from
     * 
     * [--staging-username=<username>]
     * : Flywheel staging site username (HTTP Basic Auth)
     * 
     * [--staging-password=<password>]
     * : Flywheel staging site password (HTTP Basic Auth)
     * 
     * [--wp-username=<username>]
     * : WordPress admin username
     * 
     * [--wp-password=<password>]
     * : WordPress admin password
     * 
     * [--batch-size=<number>]
     * : Number of resources to process per batch (default: 10)
     * 
     * [--dry-run]
     * : Run without actually importing files
     * 
     * [--timeout=<seconds>]
     * : Request timeout in seconds (default: 15)
     * 
     * [--max-concurrent=<number>]
     * : Maximum concurrent downloads (default: 3)
     * 
     * ## EXAMPLES
     * 
     * wp acf-import import_resource_documents \
     * --old-site-url=https://flywheel:fierce-brick@honest-number.flywheelstaging.com \
     * --dry-run
     * 
     *     # Import resource documents with authentication
     *     wp acf-import import_resource_documents --old-site-url=https://staging.site.com --staging-username=flywheel_user --staging-password=flywheel_pass
     */
    public function import_resource_documents($args, $assoc_args)
    {
        $old_site_url = isset($assoc_args['old-site-url']) ? $assoc_args['old-site-url'] : null;
        $staging_username = isset($assoc_args['staging-username']) ? $assoc_args['staging-username'] : null;
        $staging_password = isset($assoc_args['staging-password']) ? $assoc_args['staging-password'] : null;
        $wp_username = isset($assoc_args['wp-username']) ? $assoc_args['wp-username'] : null;
        $wp_password = isset($assoc_args['wp-password']) ? $assoc_args['wp-password'] : null;
        $batch_size = isset($assoc_args['batch-size']) ? (int)$assoc_args['batch-size'] : 10;
        $dry_run = isset($assoc_args['dry-run']);
        $timeout = isset($assoc_args['timeout']) ? (int)$assoc_args['timeout'] : 15;
        $max_concurrent = isset($assoc_args['max-concurrent']) ? (int)$assoc_args['max-concurrent'] : 3;

        if (!$old_site_url) {
            WP_CLI::error("Please provide --old-site-url parameter");
            return;
        }

        WP_CLI::line("Starting resource document import from: $old_site_url");
        WP_CLI::line("Batch size: $batch_size, Timeout: {$timeout}s, Max concurrent: $max_concurrent");

        // Set up authentication configuration
        $auth_config = $this->setup_authentication_config($staging_username, $staging_password, $wp_username, $wp_password);

        if ($dry_run) {
            WP_CLI::line("DRY RUN MODE - No files will be imported");
        }

        // Test connectivity first
        if (!$this->test_site_connectivity($old_site_url, $auth_config, $timeout)) {
            WP_CLI::error("Cannot connect to the old site. Please check your authentication credentials.");
            return;
        }

        // Get WordPress authentication cookies if needed
        $auth_cookies = null;
        if ($wp_username && $wp_password) {
            $auth_cookies = $this->get_auth_cookies_with_staging_auth($old_site_url, $wp_username, $wp_password, $auth_config, $timeout);
            if (!$auth_cookies) {
                WP_CLI::warning("Failed to authenticate with WordPress admin. Trying without WordPress authentication...");
            }
        }

        // Get all resources with resource_document field
        $resources = $this->get_resources_with_document_field();

        if (empty($resources)) {
            WP_CLI::success("No resources with document fields found!");
            return;
        }

        WP_CLI::line("Found " . count($resources) . " resources with document fields to process");

        // Process in batches
        $batches = array_chunk($resources, $batch_size);
        $batch_count = count($batches);
        $success_count = 0;
        $error_count = 0;

        foreach ($batches as $batch_index => $batch) {
            $current_batch = $batch_index + 1;
            WP_CLI::line("\nProcessing batch $current_batch of $batch_count...");

            foreach ($batch as $resource) {
                $result = $this->process_resource_document(
                    $resource,
                    $old_site_url,
                    $auth_config,
                    $auth_cookies,
                    $dry_run,
                    $timeout
                );

                if ($result) {
                    $success_count++;
                    WP_CLI::line("  ✓ Processed resource: " . $resource->post_title);
                } else {
                    $error_count++;
                    WP_CLI::line("  ✗ Failed to process resource: " . $resource->post_title);
                }
            }

            // Brief pause between batches
            if ($current_batch < $batch_count) {
                sleep(0.5);
            }
        }

        WP_CLI::line("\n=== IMPORT SUMMARY ===");
        WP_CLI::line("Successfully processed: $success_count");
        WP_CLI::line("Failed: $error_count");
        WP_CLI::line("Total resources: " . count($resources));

        if ($success_count > 0) {
            WP_CLI::success("Resource document import completed!");
        } else {
            WP_CLI::warning("No resources were successfully processed.");
        }
    }

    private function get_resources_with_document_field()
    {
        global $wpdb;

        // FIXED: Get all resources that have a resource_document field value from both post types
        $resources = $wpdb->get_results("
            SELECT p.*, pm.meta_value as resource_document
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type IN ('resources', 'palmshield_resources')
            AND pm.meta_key = 'resource_document'
            AND pm.meta_value != ''
            AND p.post_status = 'publish'
            ORDER BY p.ID ASC
        ");

        return $resources;
    }

    private function process_resource_document($resource, $old_site_url, $auth_config, $auth_cookies, $dry_run, $timeout)
    {
        WP_CLI::line("  Processing resource: " . $resource->post_title . " (ID: " . $resource->ID . ")");

        // Get the current resource_document value
        $current_value = get_post_meta($resource->ID, 'resource_document', true);
        
        WP_CLI::line("    Current value: " . $current_value);

        // If it's already a numeric ID, check if the attachment exists
        if (is_numeric($current_value)) {
            $attachment = get_post($current_value);
            if ($attachment && $attachment->post_type === 'attachment') {
                WP_CLI::line("    ✓ Attachment already exists (ID: $current_value)");
                return true;
            } else {
                WP_CLI::line("    ⚠️ Invalid attachment ID: $current_value");
            }
        }

        // If it's a URL or file path, try to import it
        if (!empty($current_value) && !is_numeric($current_value)) {
            $file_url = $this->get_file_url_from_value($current_value, $old_site_url, $auth_config);
            
            if ($file_url) {
                WP_CLI::line("    File URL: " . $file_url);
                
                if ($dry_run) {
                    WP_CLI::line("    [DRY RUN] Would import file from: " . $file_url);
                    return true;
                }

                // Import the file
                $new_attachment_id = $this->import_file_from_url_with_auth_optimized($file_url, $resource->post_title, $auth_config, $timeout);

                if ($new_attachment_id) {
                    // Update the ACF field
                    update_field('resource_document', $new_attachment_id, $resource->ID);
                    WP_CLI::line("    ✓ Successfully imported and attached file (New ID: $new_attachment_id)");
                    return true;
                } else {
                    WP_CLI::line("    ✗ Failed to import file");
                    return false;
                }
            } else {
                WP_CLI::line("    ✗ Could not determine file URL from value: " . $current_value);
                return false;
            }
        }

        WP_CLI::line("    ⚠️ No valid file reference found");
        return false;
    }

    private function get_file_url_from_value($value, $old_site_url, $auth_config)
    {
        // If it's already a full URL, return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // If it's a relative path, construct the full URL
        if (strpos($value, '/') === 0 || strpos($value, 'wp-content/') === 0) {
            return trailingslashit($old_site_url) . ltrim($value, '/');
        }

        // If it's just a filename, try common upload paths
        $filename = basename($value);
        $possible_paths = [
            'wp-content/uploads/' . $filename,
            'wp-content/uploads/' . date('Y') . '/' . date('m') . '/' . $filename,
            'uploads/' . $filename
        ];

        foreach ($possible_paths as $path) {
            $url = trailingslashit($old_site_url) . $path;
            WP_CLI::line("    Trying URL: " . $url);
            
            // Test if the URL is accessible
            $args = [
                'timeout' => 5,
                'sslverify' => false
            ];
            
            if ($auth_config['has_staging_auth']) {
                $args['auth'] = [$auth_config['staging_username'], $auth_config['staging_password']];
            }
            
            $response = wp_remote_head($url, $args);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                return $url;
            }
        }

        return false;
    }
}