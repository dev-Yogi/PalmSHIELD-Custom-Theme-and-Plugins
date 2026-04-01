<?php
/*
Plugin Name: Highlight Quotes Plugin
Description: Provides Highlight Quote and Highlight Box Gutenberg blocks using ACF.
Version: 1.0
Author: Vanessa
*/

// Suppress any PHP notices/warnings that could break JSON responses
if (defined('REST_REQUEST') && REST_REQUEST) {
    @ini_set('display_errors', 0);
    @error_reporting(0);
}

// Check if ACF is active
if (!function_exists('acf_add_local_field_group')) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Highlight Quotes Plugin requires Advanced Custom Fields PRO to be installed and activated.</p></div>';
    });
    return;
}

// Add ACF JSON load point
add_filter('acf/settings/load_json', function($paths) {
    $paths[] = __DIR__ . '/acf-json';
    return $paths;
});

// Register blocks
add_action('init', function() {
    register_block_type(__DIR__ . '/blocks/highlight-quote');
    register_block_type(__DIR__ . '/blocks/highlight-box');
});

// Clean output buffer before REST API responses to prevent JSON corruption
add_action('rest_api_init', function() {
    ob_start();
});

add_filter('rest_pre_echo_response', function($result) {
    // Clean any unexpected output
    if (ob_get_length()) {
        ob_end_clean();
    }
    return $result;
});