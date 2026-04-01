<?php
/**
 * Plugin Name: Update Product File URLs
 * Description: Updates all product and variation file URLs from http to https.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function update_product_file_urls() {
    global $wpdb;

    // Fetch product_files and variation_files meta entries
    $results = $wpdb->get_results("
        SELECT meta_id, meta_value FROM {$wpdb->postmeta} 
        WHERE meta_key IN ('product_files', 'variation_files')
    ");

    foreach ($results as $row) {
        $meta_id = $row->meta_id;
        $meta_value = maybe_unserialize($row->meta_value);

        if (is_array($meta_value)) {
            $updated = false;

            foreach ($meta_value as &$file) {
                if (isset($file['url']) && strpos($file['url'], 'http://') === 0) {
                    $file['url'] = str_replace('http://', 'https://', $file['url']);
                    $updated = true;
                }
            }

            if ($updated) {
                // Re-serialize and update the database
                $updated_value = maybe_serialize($meta_value);
                $wpdb->update($wpdb->postmeta, ['meta_value' => $updated_value], ['meta_id' => $meta_id]);
            }
        }
    }

    echo "URLs updated successfully!";
}

add_action('admin_init', 'update_product_file_urls');
