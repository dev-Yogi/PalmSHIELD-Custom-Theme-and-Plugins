<?php
/**
 * Plugin Name: WooCommerce Variant Gallery
 * Description: Add multiple images to product variations
 * Version: 1.0.0
 * Author: Vanessa K.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

class WC_Variant_Gallery {
    
    private $meta_key = '_variant_gallery';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Admin hooks
        add_action('woocommerce_product_after_variable_attributes', array($this, 'add_variant_gallery_field'), 10, 3);
        add_action('woocommerce_save_product_variation', array($this, 'save_variant_gallery'), 10, 2);
        add_action('save_post', array($this, 'save_variant_gallery_on_product_save'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('woocommerce_available_variation', array($this, 'add_gallery_to_variation_data'), 10, 3);
        
        // AJAX hooks
        add_action('wp_ajax_get_variant_gallery', array($this, 'ajax_get_variant_gallery'));
        add_action('wp_ajax_save_variant_gallery', array($this, 'ajax_save_variant_gallery'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue admin scripts and styles only on product edit pages
        if (is_admin() && (get_current_screen()->id === 'product' || get_current_screen()->id === 'edit-product')) {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('wc-variant-gallery-admin', plugin_dir_url(__FILE__) . 'admin.js', array('jquery', 'media-upload'), '1.0.0', true);
            wp_enqueue_style('wc-variant-gallery-admin', plugin_dir_url(__FILE__) . 'admin.css', array(), '1.0.0');
            
            // Localize admin script
            wp_localize_script('wc-variant-gallery-admin', 'variant_gallery_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('variant_gallery_nonce')
            ));
        }
        
        // Enqueue frontend scripts
        if (is_product()) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('wc-variant-gallery-frontend', plugin_dir_url(__FILE__) . 'frontend.js', array('jquery'), '1.0.0', true);
            
            // Localize frontend script
            wp_localize_script('wc-variant-gallery-frontend', 'variant_gallery_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('variant_gallery_nonce')
            ));
        }
    }
    
    /**
     * Add variant gallery field to admin
     */
    public function add_variant_gallery_field($loop, $variation_data, $variation) {
        $gallery_images = get_post_meta($variation->ID, $this->meta_key, true);
        $gallery_images = is_array($gallery_images) ? $gallery_images : array();
        ?>
        <div class="form-row form-row-full">
            <label><?php _e('Variant Gallery Images', 'wc-variant-gallery'); ?></label>
            <p class="description"><?php _e('Add additional images for this variation. The main variant image should be set above using the "Variation image" field.', 'wc-variant-gallery'); ?></p>
            <div class="variant-gallery-container" data-variation-id="<?php echo esc_attr($variation->ID); ?>">
                <div class="variant-gallery-images">
                    <?php foreach ($gallery_images as $image_id): ?>
                        <div class="variant-gallery-image" data-image-id="<?php echo esc_attr($image_id); ?>">
                            <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                            <button type="button" class="remove-image">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="add-gallery-images button"><?php _e('Add Images', 'wc-variant-gallery'); ?></button>
                <button type="button" class="save-gallery button button-primary"><?php _e('Save Gallery', 'wc-variant-gallery'); ?></button>
                <div class="gallery-status"></div>
            </div>
        </div>
        <?php
    }
       
    
    /**
     * Save variant gallery
     */
    public function save_variant_gallery($variation_id, $loop) {
        if (isset($_POST['variant_gallery'][$variation_id])) {
            $gallery_images = $this->process_gallery_data($_POST['variant_gallery'][$variation_id]);
            update_post_meta($variation_id, $this->meta_key, $gallery_images);
        }
    }
    
    /**
     * Save variant gallery on product save
     */
    public function save_variant_gallery_on_product_save($post_id) {
        if (get_post_type($post_id) === 'product') {
            $variations = get_posts(array(
                'post_type' => 'product_variation',
                'post_parent' => $post_id,
                'numberposts' => -1
            ));
            
            foreach ($variations as $variation) {
                if (isset($_POST['variant_gallery'][$variation->ID])) {
                    $gallery_images = $this->process_gallery_data($_POST['variant_gallery'][$variation->ID]);
                    update_post_meta($variation->ID, $this->meta_key, $gallery_images);
                }
            }
        }
    }
    
    /**
     * Process gallery data
     */
    private function process_gallery_data($gallery_data) {
        if (!is_array($gallery_data)) {
            return array();
        }
        
        $processed = array();
        foreach ($gallery_data as $image_id) {
            $image_id = intval($image_id);
            if ($image_id > 0 && wp_attachment_is_image($image_id)) {
                $processed[] = $image_id;
            }
        }
        
        return array_unique($processed);
    }
    
    /**
     * AJAX save variant gallery
     */
    public function ajax_save_variant_gallery() {
        check_ajax_referer('variant_gallery_nonce', 'nonce');
        
        $variation_id = intval($_POST['variation_id']);
        $gallery_images = isset($_POST['gallery_images']) ? $_POST['gallery_images'] : array();
        
        $processed = $this->process_gallery_data($gallery_images);
        update_post_meta($variation_id, $this->meta_key, $processed);
        
        wp_send_json_success(array(
            'message' => 'Gallery saved successfully',
            'image_count' => count($processed)
        ));
    }
    
    /**
     * AJAX get variant gallery
     */
    public function ajax_get_variant_gallery() {
        check_ajax_referer('variant_gallery_nonce', 'nonce');
        
        $variation_id = intval($_POST['variation_id']);
        $gallery_images = get_post_meta($variation_id, $this->meta_key, true);
        
        if (empty($gallery_images) || !is_array($gallery_images)) {
            wp_send_json_success(array());
        }
        
            $gallery_data = array();
            foreach ($gallery_images as $image_id) {
            if ($image_id && wp_attachment_is_image($image_id)) {
                $image_src = wp_get_attachment_image_src($image_id, 'woocommerce_single');
                $image_thumb = wp_get_attachment_image_src($image_id, 'woocommerce_thumbnail');
                $image_full = wp_get_attachment_image_src($image_id, 'full');
                
                if ($image_src) {
                    $gallery_data[] = array(
                        'id' => $image_id,
                        'src' => $image_src[0],
                        'thumb' => $image_thumb ? $image_thumb[0] : $image_src[0],
                        'full' => $image_full ? $image_full[0] : $image_src[0],
                        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                        'width' => $image_src[1],
                        'height' => $image_src[2],
                        'title' => get_the_title($image_id)
                    );
                }
            }
        }
        
        wp_send_json_success($gallery_data);
    }
    
    /**
     * Add gallery data to variation data
     */
    public function add_gallery_to_variation_data($variation, $product, $variation_obj) {
        $variation_id = $variation_obj->get_id();
        $gallery_images = get_post_meta($variation_id, $this->meta_key, true);
        
        if ($gallery_images && is_array($gallery_images) && !empty($gallery_images)) {
            $gallery_data = array();
            foreach ($gallery_images as $image_id) {
                if (!$image_id || $image_id <= 0) continue;
                
                $image_data = wp_get_attachment_image_src($image_id, 'woocommerce_single');
                $thumb_data = wp_get_attachment_image_src($image_id, 'woocommerce_thumbnail');
                $full_data = wp_get_attachment_image_src($image_id, 'full');
                
                if ($image_data) {
                    $gallery_data[] = array(
                        'id' => $image_id,
                        'src' => $image_data[0],
                        'thumb' => $thumb_data ? $thumb_data[0] : $image_data[0],
                        'full' => $full_data ? $full_data[0] : $image_data[0],
                        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                        'width' => $image_data[1],
                        'height' => $image_data[2],
                        'title' => get_the_title($image_id)
                    );
                }
            }
            
            if (!empty($gallery_data)) {
                $variation['variant_gallery'] = $gallery_data;
                
                // Only set the main variation image if there isn't already one set
                $existing_image_id = get_post_meta($variation_id, '_thumbnail_id', true);
                
                if (!$existing_image_id) {
                    // Set the first image as the main variation image only if no main image is set
                    $first_image = $gallery_data[0];
                    $variation['image'] = array(
                        'id' => $first_image['id'],
                        'src' => $first_image['src'],
                        'src_w' => $first_image['width'],
                        'src_h' => $first_image['height'],
                        'srcset' => wp_get_attachment_image_srcset($first_image['id'], 'woocommerce_single'),
                        'sizes' => wp_get_attachment_image_sizes($first_image['id'], 'woocommerce_single'),
                        'title' => $first_image['title'],
                        'alt' => $first_image['alt'],
                        'full_src' => $first_image['full'],
                        'full_src_w' => $first_image['width'],
                        'full_src_h' => $first_image['height'],
                        'gallery_thumbnail_src' => $first_image['thumb'],
                        'gallery_thumbnail_src_w' => 150,
                        'gallery_thumbnail_src_h' => 150,
                        'caption' => get_post_field('post_excerpt', $first_image['id'])
                    );
                    
                    // Set the image_id for WooCommerce compatibility
                    $variation['image_id'] = $first_image['id'];
                }
            }
        }
        
        return $variation;
    }
}

// Initialize the plugin
function wc_variant_gallery_init() {
    new WC_Variant_Gallery();
}
add_action('plugins_loaded', 'wc_variant_gallery_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Flush rewrite rules
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});