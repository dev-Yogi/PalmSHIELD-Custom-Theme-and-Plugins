<?php
/*
Plugin Name: Product Filter Widget
Description: A widget that filters products based on tags relevant to the current page.
Version: 1.0
Author: Vanessa K.
*/
function enqueue_product_filter_scripts() {
    // Only enqueue on the frontend
    if (!is_admin()) {
        wp_enqueue_script(
            'product-filter-ajax',
            plugin_dir_url(__FILE__) . 'js/product-filter.js',
            ['jquery'],
            '1.0',
            true
        );

        wp_localize_script('product-filter-ajax', 'productFilterAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_product_filter_scripts');


class Custom_Product_Filter_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'custom_product_filter_widget',
            __('Product Filter By Attributes', 'palmshield-plus'),
            ['description' => __('Filter products by attributes.', 'palmshield-plus')]
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        $relevant_attributes = $this->get_relevant_product_attributes();
        $this->display_filter_form($relevant_attributes);
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        // Optional: Widget admin form logic
    }

    public function update($new_instance, $old_instance)
    {
        // Optional: Save widget options logic
    }

    private function get_relevant_product_attributes()
    {
        global $wp_query;
        $current_slug = $wp_query->query_vars['name'];

        $category_types = [
            'screen-walls' => 'Screen Wall',
            'acoustic-walls' => 'Acoustic Wall',
            'gates' => 'Gates',
            'palmshades' => 'PalmSHADE',
            'bollards' => 'Bollard',
        ];

        $current_category_type = isset($category_types[$current_slug]) ? $category_types[$current_slug] : '';

        if (empty($current_category_type)) {
            return [];
        }

        $product_categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'meta_query' => [
                [
                    'key' => 'category_type',
                    'value' => $current_category_type,
                    'compare' => '='
                ]
            ],
        ]);

        if (empty($product_categories) || is_wp_error($product_categories)) {
            return [];
        }

        $category_ids = wp_list_pluck($product_categories, 'term_id');

        $products = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_ids,
                ],
            ],
        ]);

        $attributes = [];
        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product = wc_get_product(get_the_ID());
                $product_attributes = $product->get_attributes();

                foreach ($product_attributes as $attribute) {
                    if ($attribute->is_taxonomy()) {
                        $taxonomy = $attribute->get_name();
                        $terms = $attribute->get_terms();
                        if (!isset($attributes[$taxonomy])) {
                            $attributes[$taxonomy] = [
                                'name' => wc_attribute_label($taxonomy),
                                'terms' => [],
                            ];
                        }
                        foreach ($terms as $term) {
                            if (!in_array($term, $attributes[$taxonomy]['terms'])) {
                                $attributes[$taxonomy]['terms'][] = $term;
                            }
                        }
                    }
                }
            }
            wp_reset_postdata();
        }

        return $attributes;
    }

    private function display_filter_form($attributes)
    {
        if (!empty($attributes)) {
            echo '<form method="get" action="' . esc_url(get_permalink()) . '">';

            foreach ($attributes as $taxonomy => $attribute_data) {
                echo '<h4 class="product-term">' . esc_html($attribute_data['name']) . '</h4>';

                foreach ($attribute_data['terms'] as $term) {
                    $checked = isset($_GET[$taxonomy]) && in_array($term->slug, (array)$_GET[$taxonomy]) ? 'checked' : '';
                    echo '<label>';
                    echo '<input type="checkbox" name="' . esc_attr($taxonomy) . '[]" value="' . esc_attr($term->slug) . '" ' . $checked . '> ';
                    echo esc_html($term->name);
                    echo '</label><br>';
                }
            }

            echo '<button type="submit" class="btn-submit">Apply</button>';
            echo '</form>';
        } else {
            echo '<p>No relevant attributes found for the current products.</p>';
        }
    }
}

// Register the widget
function register_custom_product_filter_widget()
{
    register_widget('Custom_Product_Filter_Widget');
}
add_action('widgets_init', 'register_custom_product_filter_widget');


function filter_products_ajax_handler() {
    // Debugging: Output the $_GET array to ensure data is being passed correctly
    error_log(print_r($_GET, true));

    // Initialize an empty tax query
    $tax_query = [];

    // Get all registered product attributes
    $product_attributes = wc_get_attribute_taxonomies();

    if (!empty($product_attributes)) {
        foreach ($product_attributes as $product_attribute) {
            $taxonomy = wc_attribute_taxonomy_name($product_attribute->attribute_name);
        
            if (isset($_GET[$taxonomy]) && !empty($_GET[$taxonomy])) {
                $selected_terms = array_map('sanitize_text_field', $_GET[$taxonomy]);
        
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $selected_terms,
                    'operator' => 'AND', // Change to 'IN' if you want any of the terms to match
                ];
            }
        }
    }

    // Build WP_Query with the tax query
    $products = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => $tax_query,
    ]);

    // Output the product loop
    $response = [];

    if ($products->have_posts()) {
        ob_start();
        while ($products->have_posts()) : $products->the_post();
            $response[] = [
                'title' => get_the_title(),
                'link'  => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(),
            ];
        endwhile;
        wp_reset_postdata();
    } else {
        $response[] = ['message' => 'No products found.'];
    }

    // Return a JSON response
    wp_send_json_success($response);
}


add_action('wp_ajax_filter_products', 'filter_products_ajax_handler');
add_action('wp_ajax_nopriv_filter_products', 'filter_products_ajax_handler');
