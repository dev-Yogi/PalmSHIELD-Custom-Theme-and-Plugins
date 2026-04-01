<?php
/*
Plugin Name: Canopy Products Grid
Description: Displays a grid of canopy products with AJAX-powered Add to Quote buttons, integrated with YITH Request a Quote.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

// 1. Register Shortcode
add_shortcode('canopy_products_grid', function($atts) {
    ob_start();
    ?>
    <div id="canopy-products-grid">
        <h2 class="canopy-products-grid-title">Canopy Options</h2>
        <hr>
        <?php
        $canopy_products = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'product_cat'    => 'canopy',
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        if ($canopy_products->have_posts()) : ?>
            <div class="canopy-products-grid-inner">
                <?php while ($canopy_products->have_posts()) : $canopy_products->the_post();
                    global $product;
                    if ($product->is_type('variable')) {
                        $variation_objects = $product->get_children();
                        foreach ($variation_objects as $variation_id) {
                            $variation = new WC_Product_Variation($variation_id);
                            if (!$variation->is_purchasable() || !$variation->is_in_stock()) continue;
                            $variation_attributes = wc_get_formatted_variation($variation, true, false, true);
                            $image = $variation->get_image() ?: $product->get_image();
                            // Build the variation URL
                            $parent_url = get_permalink($product->get_id());
                            $attributes = $variation->get_variation_attributes();
                            $query_args = [];
                            foreach ($attributes as $attr => $value) {
                                // Remove 'attribute_' if it's already in the attribute name
                                $attr = str_replace('attribute_', '', $attr);
                                $query_args['attribute_' . sanitize_title($attr)] = $value;
                            }
                            // Add variation_id to ensure proper selection
                            $query_args['variation_id'] = $variation_id;
                            $variation_url = add_query_arg($query_args, $parent_url);
                            ?>
                            <div class="canopy-product">
                                <h3 class="canopy-product-title">
                                    <?php
                                    $attr_values = array_values($attributes);
                                    echo $attr_values ? esc_html(implode(', ', $attr_values)) : 'Variant';
                                    ?>
                                </h3>

                                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"><?php echo $image; ?></a>
                                <p><?php echo $variation->get_price_html(); ?></p>
                                <div class="canopy-product-btn-row" style="display: flex; gap: 10px; justify-content: center; margin-bottom: 10px;">
                                    <a href="<?php echo esc_url($variation_url); ?>" class="canopy-grid-btn canopy-more-details-btn" target="_blank">More Details</a>
                                    <button class="canopy-grid-btn canopy-add-to-quote-btn" 
                                        data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                                        data-variation_id="<?php echo esc_attr($variation_id); ?>"
                                        data-attributes='<?php echo json_encode($variation->get_variation_attributes()); ?>'
                                    >Add to Quote</button>
                                </div>
                                <span class="canopy-quote-status"></span>
                            </div>
                            <?php
                        }
                    } elseif ($product->is_type('simple')) {
                        ?>
                        <div class="canopy-product">
                            <h3 class="canopy-product-title"><?php echo esc_html($product->get_name()); ?></h3>
                            <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo woocommerce_get_product_thumbnail(); ?></a>
                            <p><?php echo $product->get_price_html(); ?></p>
                            <div class="canopy-product-btn-row" style="display: flex; gap: 10px; justify-content: center; margin-bottom: 10px;">
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="canopy-grid-btn canopy-more-details-btn" target="_blank">More Details</a>
                                <button class="canopy-grid-btn canopy-add-to-quote-btn" 
                                    data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                                    data-variation_id=""
                                    data-attributes="{}"
                                >Add to Quote</button>
                            </div>
                            <span class="canopy-quote-status"></span>
                        </div>
                        <?php
                    }
                endwhile; ?>
            </div>
        <?php endif;
        wp_reset_postdata();
        ?>
    </div>
    <?php
    return ob_get_clean();
});

// 2. Enqueue JS
add_action('wp_enqueue_scripts', function() {
    if (is_singular('product') || is_page()) { // adjust as needed
        wp_enqueue_script('canopy-products-grid', plugins_url('js/canopy-products-grid.js', __FILE__), ['jquery'], '1.0', true);
        wp_localize_script('canopy-products-grid', 'CanopyGridAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('canopy_grid_nonce')
        ]);
        wp_enqueue_style('canopy-products-grid', plugins_url('css/canopy-products-grid.css', __FILE__));
    }
});

// 3. AJAX Handler
add_action('wp_ajax_canopy_add_to_quote', 'canopy_add_to_quote_handler');
add_action('wp_ajax_nopriv_canopy_add_to_quote', 'canopy_add_to_quote_handler');
function canopy_add_to_quote_handler() {
    check_ajax_referer('canopy_grid_nonce', 'nonce');
    $product_id = intval($_POST['product_id']);
    $variation_id = intval($_POST['variation_id']);
    $attributes = isset($_POST['attributes']) ? (array) $_POST['attributes'] : [];
    $raq_data = [
        'product_id' => $product_id,
        'quantity' => 1,
    ];
    if ($variation_id) {
        $raq_data['variation_id'] = $variation_id;
        foreach ($attributes as $key => $value) {
            $raq_data[$key] = sanitize_text_field($value);
        }
    }
    if (function_exists('YITH_Request_Quote')) {
        $result = YITH_Request_Quote()->add_item($raq_data);
        if ($result === 'true' || $result === 'exists') {
            wp_send_json_success(['message' => 'Added to quote!']);
        }
    }
    wp_send_json_error(['message' => 'Could not add to quote.']);
}
