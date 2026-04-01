<?php
/**
 * Plugin Name: ACF Timeline Block
 * Description: A Gutenberg block for displaying a vertical timeline using ACF Pro.
 * Version: 1.0
 * Author: Vanessa K
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Register the block on ACF init
add_action('acf/init', 'acf_register_timeline_block');
function acf_register_timeline_block() {
    if( function_exists('acf_register_block_type') ) {
        acf_register_block_type(array(
            'name'              => 'acf/timeline',
            'title'             => __('ACF Timeline Block'),
            'description'       => __('A custom timeline block.'),
            'render_template'   => plugin_dir_path(__FILE__) . 'template-parts/timeline.php',
            'category'          => 'formatting',
            'icon'              => 'schedule',
            'keywords'          => array( 'timeline', 'history', 'events' ),
            'mode'              => 'preview',
            'supports'          => array('align' => false),
            'enqueue_style'     => plugin_dir_url(__FILE__) . 'assets/css/timeline.css',
        ));
    }
}

// //Optionally: Include ACF JSON for field groups
// add_filter('acf/settings/load_json', 'acf_json_load_point');
// function acf_json_load_point( $paths ) {
//     $paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';
//     return $paths;
// }
