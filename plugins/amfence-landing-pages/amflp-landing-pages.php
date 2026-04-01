<?php

/*
 * Plugin Name:       Landing Pages - American Fence Company
 * Description:       Creates pages with randomized content for localized display
 * Version:           1.0.0
 * Author:            Alex Steadman for American Fence Company
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

//define('DISALLOW_FILE_EDIT', true);
require plugin_dir_path(__FILE__) . 'includes/classes/class-amflp-controller.php';
require plugin_dir_path(__FILE__) . 'includes/classes/class-amflp-loader.php';
require plugin_dir_path(__FILE__) . 'includes/admin/admin-settings.php';
require plugin_dir_path(__FILE__) . 'includes/admin/data-import.php';

register_activation_hook(__FILE__, 'amflp_activation');
register_deactivation_hook((__FILE__), 'amflp_deactivation');

add_action('wp_enqueue_scripts', 'amflp_enqueue_styles');

function amflp_enqueue_styles() {
    wp_enqueue_style('amflp-custom-styles', plugin_dir_url(__FILE__) . 'includes/css/amflp.css', array(), '1.2.3');
}

function amflp_page_template( $template ){
    if (get_post_type() == 'city') {
        $template = dirname(__FILE__) . '/single-city.php';
    }
    if (is_post_type_archive('city')) {
        $new_template = plugin_dir_path(__FILE__) . 'archive-city.php';
        if (file_exists($new_template))
            return $new_template;
    }
    if (is_post_type_archive('content')) {
        $new_template = plugin_dir_path(__FILE__) . 'archive-content.php';
        if (file_exists($new_template))
            return $new_template;
    }
    return $template;
}
add_action( 'template_include', 'amflp_page_template' );

function amflp_activation() {
    amflp_register_post_types();
    flush_rewrite_rules();
}

function amflp_deactivation() {
    unregister_post_type('city');
    unregister_post_type('content');
    flush_rewrite_rules();
}

function amflp_register_taxonomy() {
    $taxLabels = [
        'name' => 'Sections',
        'singular_name' => 'Section',
        'all_items' => 'All Sections',
        'edit_item' => 'Edit Section',
        'add_new_item' => 'Add Section',
        'new_item_name' => 'New Section Name',
        'menu_name' => 'Sections'
    ];
    
    $taxArgs = [
        'labels' => $taxLabels,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_admin_column' => true,
    ];
    
    register_taxonomy('content_sections', ['content'], $taxArgs);
}
add_action('init', 'amflp_register_taxonomy');

function amflp_register_post_types() {
    $cityArgs = amflp_get_cpt_args('City', 'Cities', ['title', 'editor'], ['post_tag']);
    $contentArgs = amflp_get_cpt_args('Content', 'Content', ['title', 'editor', 'post-formats'], ['content_sections']);
    register_post_type('city', $cityArgs);
    register_post_type('content', $contentArgs);
    
    amflp_register_meta('city', '_content_ids', 'array');
    foreach (['_city', '_state', '_phone'] as $key) { amflp_register_meta('city', $key, 'string'); }
    
    flush_rewrite_rules();
}
add_action('init', 'amflp_register_post_types');

function amflp_get_cpt_args($singular, $plural, array $supports, array $taxonomies ) {
    $labels = [
        'name' => $plural,
        'singular_name' => $singular
    ];
    $args = [
        'label'               => $plural,
        'labels'              => $labels,
        'description'         => '',
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_rest'        => true,
        'has_archive'         => true,
        'show_in_menu'        => true,
        'supports'            => $supports,
        'taxonomies'          => $taxonomies 
    ];
    
    return $args;
}

/**
 * Register a custom meta key for a specific post type.
 *
 * @param string $post_type  The post type to register the meta key for.
 * @param string $key        The meta key name.
 * @param string $type       The data type of the meta value.
 * @param mixed  $default    The default value for the meta key.
 */
function amflp_register_meta(string $post_type, string $key, string $type, $default = 'default') {
    if (!post_type_exists($post_type)) {
        return;
    }
    
    $args = [
        'type'    => $type,
        'default' => $default,
        'single'  => true,
    ];
    register_meta($post_type, $key, $args);
}

function amflp_add_meta_box() {
    add_meta_box(
            'amflp_meta_box',          
            'City Data',           
            'amflp_meta_box_content',      
            'city',
            'side',
            'default'
    );
}

function amflp_meta_box_content() {
    $registered_keys = get_registered_meta_keys('city');
    $keys = array_keys($registered_keys);
    $keys = array_diff($keys, ['_content_ids']);
    wp_nonce_field('amflp_meta_nonce', 'amflp_meta_nonce');
    
    foreach ($keys as $key) {
        $meta = get_post_meta(get_the_ID(), $key, true);
        $display_key = ucwords(str_replace('_', ' ', $key));
        ?>
        <div>
            <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($display_key); ?></label>
            <input type="text" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($meta); ?>">
        </div>
        <?php
    }
}
add_action( 'add_meta_boxes', 'amflp_add_meta_box' );

function amflp_save_meta_data() {
    if (!isset($_POST['amflp_meta_nonce']) || !wp_verify_nonce($_POST['amflp_meta_nonce'], 'amflp_meta_nonce') || !current_user_can('edit_post', get_the_ID())) {
        return;
    }

    $keys = array_keys(array_diff(get_registered_meta_keys('city'), ['_content_ids']));

    foreach ($keys as $key) {
        $value = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : '';
        update_post_meta(get_the_ID(), $key, $value);
    }
}
add_action('save_post', 'amflp_save_meta_data');

// Add a button to the City admin list to delete all cities
add_action('admin_notices', function() {
    if (get_current_screen()->post_type === 'city') {
        ?>
        <div class="notice notice-warning">
            <form method="post" style="display:inline;">
                <?php wp_nonce_field('delete_all_cities_action', 'delete_all_cities_nonce'); ?>
                <input type="submit" name="delete_all_cities" class="button button-danger" value="Delete ALL Cities">
            </form>
            <span style="color:red; font-weight:bold;"> This will delete ALL city posts. Use with caution!</span>
        </div>
        <?php
    }
});

add_action('admin_init', function() {
    if (
        isset($_POST['delete_all_cities']) &&
        check_admin_referer('delete_all_cities_action', 'delete_all_cities_nonce')
    ) {
        $cities = get_posts([
            'post_type' => 'city',
            'numberposts' => -1,
            'post_status' => 'any'
        ]);
        foreach ($cities as $city) {
            wp_delete_post($city->ID, true);
        }
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>All city posts have been deleted.</p></div>';
        });
    }
});


