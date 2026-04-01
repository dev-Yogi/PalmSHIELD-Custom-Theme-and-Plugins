<?php
/**
 * Plugin Name:       Product Infill Options Block
 * Description:       Two-tier tab system for displaying product categories with orientation tabs, infill options, and color galleries. Perfect for PalmSHIELD products.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           3.0.0
 * Author:            PalmSHIELD Marketing
 * Text Domain:       product-infill-options-block
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the block
 */
function product_infill_options_block_init() {
    register_block_type(__DIR__);
}
add_action('init', 'product_infill_options_block_init');

/**
 * Include ACF field registration
 */
require_once __DIR__ . '/acf-fields-registration.php';
