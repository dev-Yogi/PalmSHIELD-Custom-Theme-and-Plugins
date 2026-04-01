<?php


function enqueue_custom_fonts()
{
    wp_enqueue_style('bebas-neue-font', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap', false);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_fonts');

function addScripts()
{
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js', array(), null);
    wp_enqueue_script('jquery');

    wp_register_script('submenu-toggle', '/wp-content/themes/palmshield/js/menu/menu.js', array(), '0.0.2');
    wp_enqueue_script('submenu-toggle');

    wp_register_script('quote', '/wp-content/themes/palmshield/js/quote.js', array(), '0.0.1');
    wp_enqueue_script('quote');

    wp_register_script('misc', '/wp-content/themes/palmshield/js/misc.js', array(), '0.0.1');
    wp_enqueue_script('misc');

    wp_register_script('cad-menu', '/wp-content/themes/palmshield/js/cad-menu.js', array(), '0.0.1');
    wp_enqueue_script('cad-menu');

    wp_enqueue_style('bootstrap-grid', '/wp-content/themes/palmshield/css/bootstrap-grid.min.css', array(), '4.0.0');
    wp_enqueue_style('style', '/wp-content/themes/palmshield/style.css', array(), '0.4.0');

    wp_enqueue_style('azos-fonts', 'https://use.typekit.net/tiy0gmm.css', array(), '0.0.1');


    if (is_product()) {
        wp_enqueue_script(
            'gate-data-collection',
            get_template_directory_uri() . '/js/gate-data-collection.js',
            array('jquery'),
            '1.0',
            true
        );
    }

    if (is_page('palmshades')) {
        wp_enqueue_style('palmshades-style', get_template_directory_uri() . '/css/palmshades.css', array(), time());
    }

    if (is_product()) {
        global $post;
        if (has_term(array('shades', 'retractable-screen', 'canopy'), 'product_cat', $post->ID)) {
            wp_enqueue_style('product-shades-style', get_template_directory_uri() . '/css/product-shades.css', array(), time());
        }
    }
    
}
add_action('wp_enqueue_scripts', 'addScripts');

function theme_setup()
{
    add_theme_support('block-templates');
    add_theme_support('wp-block-styles');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('align-wide');
    add_theme_support('menus');
    add_post_type_support('page', 'excerpt', 'resources', 'products', 'product');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-formats', array('aside', 'image', 'gallery', 'video', 'audio', 'link', 'quote', 'status', 'chat'));
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('custom-background', array(
        'default-image'          => '',
        'default-preset'         => 'default',
        'default-size'           => 'cover',
        'default-repeat'         => 'no-repeat',
        'default-attachment'     => 'scroll',
    ));
    add_theme_support('custom-header', array(
        'default-image'          => '',
        'width'                  => 300,
        'height'                 => 60,
        'flex-height'            => true,
        'flex-width'             => true,
        'default-text-color'     => '',
        'header-text'            => true,
        'uploads'                => true,
    ));
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    ));
}
add_action('after_setup_theme', 'theme_setup');

function get_post_id_by_name($page_name)
{
    $pages = get_posts([
        'name'        => $page_name,
        'post_type'   => 'page',
        'post_status' => 'publish',
        'numberposts' => 1,
    ]);

    return !empty($pages) ? $pages[0]->ID : null;
}

function add_widget_support()
{
    register_sidebar(array(
        'name'          => 'Sidebar',
        'id'            => 'sidebar',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'add_widget_support');


function register_widget_areas()
{

    register_sidebar(array(
        'name'          => 'Footer area one',
        'id'            => 'footer_area_one',
        'description'   => 'This widget area discription',
        'before_widget' => '<section class="footer-area footer-area-one">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => 'Footer area two',
        'id'            => 'footer_area_two',
        'description'   => 'This widget area discription',
        'before_widget' => '<section class="footer-area footer-area-two">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => 'Footer area three',
        'id'            => 'footer_area_three',
        'description'   => 'This widget area discription',
        'before_widget' => '<section class="footer-area footer-area-three">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => 'Footer area four',
        'id'            => 'footer_area_four',
        'description'   => 'This widget area discription',
        'before_widget' => '<section class="footer-area footer-area-three">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => 'Footer area five',
        'id'            => 'footer_area_five',
        'description'   => 'This widget area discription',
        'before_widget' => '<section class="footer-area footer-area-five">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => 'Palmshades Footer Area One',
        'id'            => 'palmshades_footer_area_one',
        'description'   => 'This widget area discription',
        'before_widget' => '<section class="footer-area footer-area-two">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
}

add_action('widgets_init', 'register_widget_areas');

// Add the product tab functions
add_filter('woocommerce_product_tabs', 'woo_new_product_tab', 999);
function woo_new_product_tab($tabs)
{
    // Remove the "Additional Information" tab
    unset($tabs['additional_information']);

    $tabs['product_specs_downloads'] = array(
        'title'     => __('Specs & CAD/BIM Files', 'woocommerce'),
        'priority'  => 50,
        'callback'  => 'woo_new_product_tab_content'
    );

    $tabs['product_color_options'] = array(
        'title'     => __('Color Options', 'woocommerce'),
        'priority'  => 60,
        'callback'  => 'woo_product_color_options_tab_content'
    );

    return $tabs;
}

//Register custom endpoint for SendGrid webhooks
add_action('rest_api_init', function () {
    register_rest_route('sendgrid/v1', '/webhook/', array(
        'methods'  => 'POST',
        'callback' => 'handle_sendgrid_webhook',
        'permission_callback' => '__return_true', // No auth
    ));
});

function handle_sendgrid_webhook(WP_REST_Request $request) {
    $events = $request->get_json_params();

    // For debugging — log to a file
    file_put_contents(__DIR__ . '/sendgrid-log.json', json_encode($events, JSON_PRETTY_PRINT));

    // You could also store in database or send admin email
    return new WP_REST_Response(['status' => 'received'], 200);
}

//Woocommerce product files tab
function woo_new_product_tab_content() {
    global $product;
    
    echo '<h2>Downloadable Files</h2>';
    
    if ($product && $product->is_type('variable')) {
        $variations = $product instanceof WC_Product_Variable ? $product->get_available_variations() : array();
        $default_files = get_post_meta($product->get_id(), 'product_files', true);
        
        // Add initial message for when no variation is selected
        echo '<div id="no-variation-message">';
        
        // Display default files if they exist
        if (!empty($default_files)) {
            echo '<h3>Product Files</h3>';
            echo '<ul class="file-list">';
            foreach ($default_files as $file) {
                if (!empty($file['url']) && !empty($file['name'])) {
                    echo '<li>';
                    echo '<a href="' . esc_url($file['url']) . '" target="_blank">';
                    echo '<span class="file-name">' . esc_html($file['name']) . '</span>';
                    echo '</a>';
                    echo '</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p>Please select an option to view its resources.</p>';
        }
        echo '</div>';
        
        // Create a container for all variation files with initial hidden state
        echo '<div id="variation-files-container">';
        
        foreach ($variations as $variation) {
            $variation_obj = wc_get_product($variation['variation_id']);
            $variation_files = get_post_meta($variation['variation_id'], 'variation_files', true);
            
            if (!empty($variation_files)) {
                // Remove variation description, only show files
                echo '<div class="variation-files" data-variation-id="' . esc_attr($variation['variation_id']) . '" style="display: none;">';
                echo '<ul class="file-list">';
                
                foreach ($variation_files as $file) {
                    if (!empty($file['url']) && !empty($file['name'])) {
                        echo '<li>';
                        echo '<a href="' . esc_url($file['url']) . '" target="_blank">';
                        echo '<span class="file-name">' . esc_html($file['name']) . '</span>';
                        echo '</a>';
                        echo '</li>';
                    }
                }
                
                echo '</ul>';
                echo '</div>';
            } else {
                // If no variation files exist, create empty div that will show default files
                echo '<div class="variation-files default-files" data-variation-id="' . esc_attr($variation['variation_id']) . '" style="display: none;">';
                if (!empty($default_files)) {
                    echo '<h3>Product Files</h3>';
                    echo '<ul class="file-list">';
                    foreach ($default_files as $file) {
                        if (!empty($file['url']) && !empty($file['name'])) {
                            echo '<li>';
                            echo '<a href="' . esc_url($file['url']) . '" target="_blank">';
                            echo '<span class="file-name">' . esc_html($file['name']) . '</span>';
                            echo '</a>';
                            echo '</li>';
                        }
                    }
                    echo '</ul>';
                }
                echo '</div>';
            }
        }
        
        echo '</div>';
        
        // Update JavaScript to handle variation changes
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Hide all variation files initially
            $('.variation-files').hide();
            $('#no-variation-message').show();
            
            // Listen for variation changes
            $('form.variations_form').on('show_variation', function(event, variation) {
                // Hide initial message
                $('#no-variation-message').hide();
                // Hide all variation files
                $('.variation-files').hide();
                
                // Show files for selected variation
                var $variationFiles = $('.variation-files[data-variation-id="' + variation.variation_id + '"]');
                if ($variationFiles.length) {
                    $variationFiles.show();
                }
            });
            
            // Reset to default view when no variation is selected
            $('form.variations_form').on('hide_variation', function() {
                $('.variation-files').hide();
                $('#no-variation-message').show();
            });
            
            // Handle reset button click
            $('.reset_variations').on('click', function() {
                $('.variation-files').hide();
                $('#no-variation-message').show();
            });
        });
        </script>
        <?php
    } else {
        // Handle simple products
        $product_files = get_post_meta($product->get_id(), 'product_files', true);
        if (!empty($product_files)) {
            echo '<ul class="file-list">';
            foreach ($product_files as $file) {
                if (!empty($file['url']) && !empty($file['name'])) {
                    echo '<li>';
                    echo '<a href="' . esc_url($file['url']) . '" target="_blank">';
                    echo '<span class="file-name">' . esc_html($file['name']) . '</span>';
                    echo '</a>';
                    echo '</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p>No product files available.</p>';
        }
    }
}

function woo_product_color_options_tab_content()
{
    $colors = get_field('product_color_options_gallery');
    if (is_array($colors)) {
        foreach ($colors as $color) {
            echo '<div class="color-option">';
            echo '<img src="' . esc_url($color['url']) . '" alt="' . esc_attr($color['alt']) . '" />';
            echo '<div>' . esc_html($color["caption"]) . '</div>';
            echo '</div>';
        }
    } else {
        echo esc_html($colors);
    }
}

// Replace get_site_section() with new URL-based function
function get_current_site_type() {
    // Check for single product and product category
    if (is_product()) {
        global $post;
        if (has_term(array('shades', 'retractable-canopies, canopy'), 'product_cat', $post->ID)) {
            return 'shades';
        }
    }

    // Fallback: Check for keywords in the URL path
    $current_url = $_SERVER['REQUEST_URI'];
    if (strpos($current_url, '/palmshades/') !== false || strpos($current_url, '/retractable-canopies/') !== false) {
        return 'shades';
    }

    // Default
    return 'screens';
}



// Simplified menu registration
function add_Main_Nav()
{
    register_nav_menus(array(
        'header-menu' => __('Header Menu'),
        'screens-main-menu' => __('Palmshield Screens Main Menu')
    ));
}
add_action('init', 'add_Main_Nav');


function add_menu_item_caret($item_output, $item, $depth, $args)
{
    if (in_array('menu-item-has-children', $item->classes)) {
        $item_output = str_replace('</a>', ' <span class="submenu-caret">&#8964;
</span></a>', $item_output);
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_menu_item_caret', 10, 4);


function add_menu_item_attributes($atts, $item, $args, $depth)
{
    if (in_array('menu-item-has-children', $item->classes)) {
        $atts['aria-haspopup'] = 'true';
        $atts['aria-expanded'] = 'false';
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_item_attributes', 10, 4);

/**
 * Rename the additional information tab if product has attributes
 */
add_filter('woocommerce_product_tabs', 'woo_rename_tabs', 98);
function woo_rename_tabs($tabs)
{
    global $product;

    if ($product->has_attributes() || $product->has_dimensions() || $product->has_weight()) {
        $tabs['additional_information']['title'] = __('Product Data'); // Rename the additional information tab
    }

    return $tabs;
}
/**
 * Allow shortcodes in product excerpts
 */
if (!function_exists('woocommerce_template_single_excerpt')) {
    function woocommerce_template_single_excerpt($post)
    {
        global $post;
        if ($post->post_excerpt) echo '<div itemprop="description">' . do_shortcode(wpautop(wptexturize($post->post_excerpt))) . '</div>';
    }
}

// Remove default short description placement
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

// Add product title and short description
add_action('woocommerce_single_product_summary', 'custom_product_title_and_description', 20);

function custom_product_title_and_description()
{
    // Add error checking
    global $product;
    
    // Check if we have a valid product
    if (!is_object($product) || !($product instanceof WC_Product)) {
        $product = wc_get_product(get_the_ID());
        if (!$product) {
            error_log('No valid product found for ID: ' . get_the_ID());
            return; // Exit if no valid product
        }
    }

    // Now safely get the description
    $short_description = $product->get_short_description();
}



remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 30);




// ADD CUSTOM GATE SECTION TO PRODUCT PAGE FOR QUOTE
// Add new actions based on product type
add_action('woocommerce_before_single_variation', 'add_custom_gate_section_variable');
add_action('woocommerce_single_product_summary', 'add_custom_gate_section_simple', 7); // 7 is after title (5) but before price (10)

if (class_exists('YITH_YWRAQ_Frontend') && ! function_exists('ywraq_move_button_quote_single_product_page')) {
    add_action('woocommerce_before_single_product', 'ywraq_move_button_quote_single_product_page');
    function ywraq_move_button_quote_single_product_page()
    {
        $product = wc_get_product();
        if ($product && $product->is_type('simple')) {
            remove_action('woocommerce_single_product_summary', array(YITH_YWRAQ_Frontend(), 'add_button_single_page'), 35);
            add_action('woocommerce_single_product_summary', array(YITH_YWRAQ_Frontend(), 'add_button_single_page'), 10);
        }
    }
}

function add_custom_gate_section_variable()
{
    // Prevent duplicate output for variable products
    static $has_run = false;
    if ($has_run) return;
    $has_run = true;

    $product = wc_get_product();
    if ($product && $product->is_type('variable') && has_term('Screen Wall', 'product_tag', $product->get_id())) {
        output_gate_section();
    }
}

function add_custom_gate_section_simple()
{
    $product = wc_get_product();
    if ($product && $product->is_type('simple') && has_term('Screen Wall', 'product_tag', $product->get_id())) {
        output_gate_section();
    }
}

function output_gate_section()
{
?>
    <div id="custom-gate-section">
        <h3>Does Your Project Require Gates?</h3>
        <label for="add_gates">Would you like to add gates?</label>
        <select id="add_gates" name="add_gates">
            <option value="no">No</option>
            <option value="yes">Yes</option>
        </select>

        <div id="gate_options_container" style="display: none;">
            <div class="gate-option" data-index="1">
                <h4>Gate #1</h4>
                <label for="gate_type_1">Select Gate Type:</label>
                <select id="gate_type_1" name="gate_type[]">
                    <option value="single_swing">Single Swing Gate</option>
                    <option value="double_drive">Double Drive Gate</option>
                    <option value="cantilever">Cantilever Gate</option>
                </select>
                <label for="gate_height_1">Gate Height (ft):</label>
                <input type="number" id="gate_height_1" name="gate_height[]" min="1" step="0.1">

                <label for="gate_space_1">Gate Space Opening (in):</label>
                <input type="number" id="gate_space_1" name="gate_space[]" min="1" step="0.1">

                <div>
                    <label for="gate_quantity_1">Quantity:</label>
                    <button type="button" class="decrease_quantity">-</button>
                    <input type="number" id="gate_quantity_1" name="gate_quantity[]" value="1" min="1">
                    <button type="button" class="increase_quantity">+</button>
                </div>

                <button type="button" class="remove_gate">Remove Gate</button>
            </div>
            <button type="button" id="add_more_gates">✅ Add Another Gate</button>
        </div>
    </div>

    <script>
        document.getElementById('add_gates').addEventListener('change', function() {
            var gateOptionsContainer = document.getElementById('gate_options_container');
            gateOptionsContainer.style.display = this.value === 'yes' ? 'block' : 'none';
        });

        let gateIndex = 1;

        document.getElementById('add_more_gates').addEventListener('click', function() {
            gateIndex++;
            var newGateOption = document.createElement('div');
            newGateOption.classList.add('gate-option');
            newGateOption.setAttribute('data-index', gateIndex);
            newGateOption.innerHTML = `
                <h4>Gate #${gateIndex}</h4>
                <label for="gate_type_${gateIndex}">Select Gate Type:</label>
                <select id="gate_type_${gateIndex}" name="gate_type[]">
                    <option value="single_swing">Single Swing Gate</option>
                    <option value="double_drive">Double Drive Gate</option>
                    <option value="cantilever">Cantilever Gate</option>
                </select>

                <label for="gate_height_${gateIndex}">Gate Height (in):</label>
                <input type="number" id="gate_height_${gateIndex}" name="gate_height[]" min="1" step="0.1">

                <label for="gate_space_${gateIndex}">Gate Space Opening (in):</label>
                <input type="number" id="gate_space_${gateIndex}" name="gate_space[]" min="1" step="0.1">

                <div>
                    <label for="gate_quantity_${gateIndex}">Quantity:</label>
                    <button type="button" class="decrease_quantity">-</button>
                    <input type="number" id="gate_quantity_${gateIndex}" name="gate_quantity[]" value="1" min="1">
                    <button type="button" class="increase_quantity">+</button>
                </div>

                <button type="button" class="remove_gate">Remove Gate</button>
            `;
            document.getElementById('gate_options_container').insertBefore(newGateOption, this);

            // Add event listeners for incrementing and decrementing quantity
            newGateOption.querySelector('.increase_quantity').addEventListener('click', function() {
                var quantityInput = newGateOption.querySelector('input[name="gate_quantity[]"]');
                quantityInput.value = parseInt(quantityInput.value) + 1;
            });

            newGateOption.querySelector('.decrease_quantity').addEventListener('click', function() {
                var quantityInput = newGateOption.querySelector('input[name="gate_quantity[]"]');
                if (quantityInput.value > 1) {
                    quantityInput.value = parseInt(quantityInput.value) - 1;
                }
            });

            // Add event listener for removing the gate
            newGateOption.querySelector('.remove_gate').addEventListener('click', function() {
                newGateOption.remove();
            });
        });

        // Add event listener for the initial "Remove Gate" button
        document.querySelector('.remove_gate').addEventListener('click', function() {
            document.querySelector('.gate-option[data-index="1"]').remove();
        });
    </script>
<?php
}

add_filter('woocommerce_add_cart_item_data', 'save_custom_gate_data', 10, 2);
function save_custom_gate_data($cart_item_data, $product_id)
{
    // Check if the product has the 'Screen Wall' tag
    $is_screen_wall_product = has_term('Screen Wall', 'product_tag', $product_id);

    // If it's a Screen Wall product and gate data is set, save it
    if ($is_screen_wall_product && isset($_POST['gate_type']) && is_array($_POST['gate_type'])) {
        $gate_data = array();
        foreach ($_POST['gate_type'] as $index => $type) {
            $gate_data[] = array(
                'type' => sanitize_text_field($type),
                'height' => sanitize_text_field($_POST['gate_height'][$index]),
                'space' => sanitize_text_field($_POST['gate_space'][$index]),
                'quantity' => intval($_POST['gate_quantity'][$index])
            );
        }
        $cart_item_data['gate_data'] = $gate_data;
    }
    return $cart_item_data;
}
add_action('gform_after_submission_2', function ($entry, $form) {
    if (class_exists('YITH_Request_Quote')) {
        YITH_Request_Quote()->clear_raq_list();
    }
}, 10, 2);

add_action('wp_head', function () {
    error_log('Current Section: ' . get_current_site_type());
    error_log('Has Nav Menu: ' . has_nav_menu('acoustic-main-menu'));
});


//ADD CUSTOM MULTIPLE FILE UPLOAD FIELD TO EACH VARIATION

// Add custom fields to variation admin panel
add_action('woocommerce_product_after_variable_attributes', 'add_custom_variation_fields', 10, 3);
function add_custom_variation_fields($loop, $variation_data, $variation) {
    ?>
    <div class="variation-files-wrapper">
        <p class="form-field">
            <label>Variation Files</label>
            <div class="variation-files-container" data-variation-id="<?php echo esc_attr($variation->ID); ?>">
                <?php
                $files = get_post_meta($variation->ID, 'variation_files', true);
                if (!empty($files)) {
                    foreach ($files as $index => $file) {
                        ?>
                        <div class="file-row">
                            <input type="text" name="variation_files[<?php echo esc_attr($variation->ID); ?>][<?php echo esc_attr($index); ?>][name]" 
                                   value="<?php echo esc_attr($file['name']); ?>" placeholder="File Name">
                            <input type="text" name="variation_files[<?php echo esc_attr($variation->ID); ?>][<?php echo esc_attr($index); ?>][url]" 
                                   value="<?php echo esc_attr($file['url']); ?>" placeholder="File URL">
                            <button type="button" class="button upload-file">Upload</button>
                            <button type="button" class="button remove-file">Remove</button>
                        </div>
                        <?php
                    }
                }
                ?>
                <button type="button" class="button add-file">Add File</button>
            </div>
        </p>
    </div>
    <?php
}

// Save variation files data
add_action('woocommerce_save_product_variation', 'save_variation_files', 10, 2);
function save_variation_files($variation_id, $loop) {
    if (isset($_POST['variation_files'][$variation_id])) {
        $files = array_values(array_filter($_POST['variation_files'][$variation_id], function($file) {
            return !empty($file['url']) && !empty($file['name']);
        }));
        update_post_meta($variation_id, 'variation_files', $files);
    }
}

// Add necessary JavaScript for the media uploader
add_action('admin_footer', 'variation_files_admin_script');
function variation_files_admin_script() {
    ?>
    <script type="text/javascript">
     jQuery(document).ready(function($) {
        // Handle file upload button clicks
        $('.woocommerce_variations, .options_group').on('click', '.upload-file', function(e) {
            e.preventDefault();
            var button = $(this);
            var urlInput = button.siblings('input[name*="[url]"]');
            
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select or Upload a File',
                button: { text: 'Use this file' },
                multiple: false
            });

            file_frame.on('select', function() {
                var attachment = file_frame.state().get('selection').first().toJSON();
                urlInput.val(attachment.url);
            });

            file_frame.open();
        });

        // Handle adding new file rows for both simple and variable products
        $('.woocommerce_variations, .options_group').on('click', '.add-file', function(e) {
            e.preventDefault();
            var container = $(this).closest('.variation-files-container, .product-files-container');
            var isVariation = container.hasClass('variation-files-container');
            var index = container.find('.file-row').length;
            var variationId = isVariation ? container.data('variation-id') : '';
            
            var nameFieldName = isVariation ? 
                'variation_files[' + variationId + '][' + index + '][name]' : 
                'product_files[' + index + '][name]';
            var urlFieldName = isVariation ? 
                'variation_files[' + variationId + '][' + index + '][url]' : 
                'product_files[' + index + '][url]';
            
            var newRow = $('<div class="file-row">' +
                '<input type="text" name="' + nameFieldName + '" placeholder="File Name">' +
                '<input type="text" name="' + urlFieldName + '" placeholder="File URL">' +
                '<button type="button" class="button upload-file">Upload</button>' +
                '<button type="button" class="button remove-file">Remove</button>' +
                '</div>');
            
            $(this).before(newRow);
        });

        // Handle removing file rows
        $('.woocommerce_variations, .options_group').on('click', '.remove-file', function(e) {
            e.preventDefault();
            $(this).closest('.file-row').remove();
        });
    });
    </script>
    <?php
}

// Add custom meta box for simple product files
add_action('woocommerce_product_options_general_product_data', 'add_simple_product_files_field');
function add_simple_product_files_field() {
    ?>
    <div class="options_group">
        <p class="form-field">
            <label>Product Files</label>
            <div class="product-files-container">
                <?php
                $files = get_post_meta(get_the_ID(), 'product_files', true);
                if (!empty($files)) {
                    foreach ($files as $index => $file) {
                        ?>
                        <div class="file-row">
                            <input type="text" name="product_files[<?php echo esc_attr($index); ?>][name]" 
                                   value="<?php echo esc_attr($file['name']); ?>" placeholder="File Name">
                            <input type="text" name="product_files[<?php echo esc_attr($index); ?>][url]" 
                                   value="<?php echo esc_attr($file['url']); ?>" placeholder="File URL">
                            <button type="button" class="button upload-file">Upload</button>
                            <button type="button" class="button remove-file">Remove</button>
                        </div>
                        <?php
                    }
                }
                ?>
                <button type="button" class="button add-file">Add File</button>
            </div>
        </p>
    </div>
    <?php
}

// Save simple product files data
add_action('woocommerce_process_product_meta', 'save_simple_product_files');
function save_simple_product_files($post_id) {
    if (isset($_POST['product_files'])) {
        $files = array_values(array_filter($_POST['product_files'], function($file) {
            return !empty($file['url']) && !empty($file['name']);
        }));
        update_post_meta($post_id, 'product_files', $files);
    }
}
// Add related hardware section before related products
function add_related_hardware_section() {
    get_template_part('template-parts/section-related-hardware');
}
add_action('woocommerce_after_single_product_summary', 'add_related_hardware_section', 16);

// Remove the original product_cat description to replace it with our own
remove_action('woocommerce_after_subcategory_title', 'woocommerce_subcategory_description');

// Add custom subcategory display
function custom_category_description($category) {
    // Display the main category description
    if ($category->description) {
        echo '<div class="category-description">' . wp_kses_post($category->description) . '</div>';
    }

    // Get and display subcategories
    $subcategories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $category->term_id
    ));

    if (!empty($subcategories)) {
        echo '<div class="subcategories-list">';
        echo '<h3>Subcategories:</h3>';
        echo '<ul>';
        foreach ($subcategories as $subcategory) {
            echo '<li><a href="' . esc_url(get_term_link($subcategory)) . '">' . esc_html($subcategory->name) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
add_action('woocommerce_after_subcategory_title', 'custom_category_description', 10, 1);

// Modify the product count for categories to include hardware custom post type
function modify_category_product_count($terms, $taxonomies, $args) {
    if (is_admin() || !in_array('product_cat', $taxonomies)) {
        return $terms;
    }

    foreach ($terms as $key => $term) {
        // Ensure $term is a WP_Term object
        if (!is_object($term) || !isset($term->slug)) {
            continue;
        }

        if ($term->slug === 'hardware') {
            // Get count of hardware custom post type items
            $hardware_count = wp_count_posts('hardware')->publish;
            
            // Add hardware items count to the category count
            $term->count += $hardware_count;
            
            // Make sure the category isn't hidden due to empty count
            if ($term->count > 0) {
                $term->hide_empty = false;
            }
        }
    }

    return $terms;
}
add_filter('get_terms', 'modify_category_product_count', 10, 3);

// Ensure the hardware category is always shown even if it has no WooCommerce products
function show_empty_hardware_category($hide_empty) {
    if (!is_admin() && is_shop()) {
        global $wp_query;
        
        // Check if we're querying product categories
        if (isset($wp_query->query_vars['taxonomy']) && $wp_query->query_vars['taxonomy'] === 'product_cat') {
            return false; // Show all categories, including empty ones
        }
    }
    return $hide_empty;
}
add_filter('woocommerce_product_subcategories_hide_empty', 'show_empty_hardware_category', 10, 1);

// Hook into WooCommerce template loader
function add_hardware_to_category_display($template, $template_name, $template_path) {
    if (is_tax('product_cat', 'hardware')) {
        error_log('DEBUG: On hardware category page');
        
        // Get hardware items
        $args = array(
            'post_type' => 'hardware',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        $hardware_items = get_posts($args);
        
        // Store hardware items in global variable for template use
        set_query_var('hardware_items', $hardware_items);
        
        // Add our template override
        add_action('woocommerce_before_shop_loop', 'display_hardware_items', 15);
    }
    
    return $template;
}
add_filter('woocommerce_locate_template', 'add_hardware_to_category_display', 10, 3);

// Display hardware items
function display_hardware_items() {
    $hardware_items = get_query_var('hardware_items');
    
    if (!empty($hardware_items)) {
        echo '<ul class="products columns-' . esc_attr(wc_get_loop_prop('columns')) . '">';
        
        foreach ($hardware_items as $hardware) {
            echo '<li class="product type-product">';
            echo '<a href="' . get_permalink($hardware->ID) . '">';
            
            // Display featured image
            if (has_post_thumbnail($hardware->ID)) {
                echo get_the_post_thumbnail($hardware->ID, 'woocommerce_thumbnail');
            } else {
                echo wc_placeholder_img();
            }
            
            // Display title
            echo '<h2 class="woocommerce-loop-product__title">' . get_the_title($hardware->ID) . '</h2>';
            
            echo '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        
        // Remove the "no products found" notice if we have hardware items
        remove_action('woocommerce_no_products_found', 'wc_no_products_found');
    }
}

// Add function to check if quote cart has items
function yith_ywraq_quote_cart_has_items() {
    if (class_exists('YITH_Request_Quote')) {
        $quote_cart = YITH_Request_Quote()->get_raq_return();
        return !empty($quote_cart);
    }
    return false;
}

// Add quote cart icon to header action wrapper
function add_quote_cart_to_header() {
    if (yith_ywraq_quote_cart_has_items()) {
        $quote_page_id = get_option('ywraq_page_id');
        $quote_page_url = get_permalink($quote_page_id);
        
        echo '<div class="quote-cart-icon">';
        echo '<span class="cart-notification">!</span>';
        echo '<a href="' . esc_url($quote_page_url) . '"><i class="fas fa-shopping-cart"></i></a>';
        echo '</div>';
    }
}
add_action('palmshield_header_actions', 'add_quote_cart_to_header', 10);

// Add AJAX save functionality for file changes
add_action('wp_ajax_save_product_files', 'save_product_files_ajax');
function save_product_files_ajax() {
    check_ajax_referer('save_product_files', 'nonce');
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $files = isset($_POST['files']) ? $_POST['files'] : array();
    
    if ($post_id && current_user_can('edit_post', $post_id)) {
        update_post_meta($post_id, 'product_files', $files);
        wp_send_json_success();
    } else {
        wp_send_json_error('Permission denied');
    }
}

// Add JavaScript to handle automatic saving
add_action('admin_footer', 'add_file_save_script');
function add_file_save_script() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var saveTimeout;
        
        function saveFiles() {
            var $container = $('.product-files-container');
            var files = [];
            
            $container.find('.file-row').each(function() {
                var $row = $(this);
                files.push({
                    name: $row.find('input[name*="[name]"]').val(),
                    url: $row.find('input[name*="[url]"]').val()
                });
            });
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_product_files',
                    nonce: $('#product_files_nonce').val(),
                    post_id: $('#post_ID').val(),
                    files: files
                },
                success: function(response) {
                    if (!response.success) {
                        console.error('Failed to save files:', response);
                    }
                }
            });
        }
        
        // Save files when inputs change
        $('.product-files-container').on('change', 'input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveFiles, 1000); // Debounce save
        });
    });
    </script>
    <?php
}

// Add nonce field for AJAX saves
add_action('woocommerce_product_options_general_product_data', 'add_product_files_nonce');
function add_product_files_nonce() {
    wp_nonce_field('save_product_files', 'product_files_nonce');
}

// Modify the description tab content
add_filter('woocommerce_product_tabs', 'modify_product_description_tab', 98);
function modify_product_description_tab($tabs) {
    $tabs['description']['callback'] = 'custom_product_description_tab_content';
    return $tabs;
}

function custom_product_description_tab_content() {
    global $product;
    
    if ($product && $product->is_type('variable')) {
        // Get the default description
        $default_description = $product->get_description();
        
        // Get variations
        $variations = $product instanceof WC_Product_Variable ? $product->get_available_variations() : array();
        
        // Output the descriptions container
        ?>
        <div id="description-content">
            <?php echo wpautop($default_description); ?>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $descriptionContent = $('#description-content');
            var defaultDescription = <?php echo json_encode(wpautop($default_description)); ?>;
            
            $('form.variations_form')
                .on('found_variation', function(event, variation) {
                    if (variation.variation_description) {
                        $descriptionContent.html(variation.variation_description);
                    }
                })
                .on('reset_data hide_variation', function() {
                    $descriptionContent.html(defaultDescription);
                });
        });
        </script>
        <?php
    } else {
        // For non-variable products, just show the description
        echo wpautop($product->get_description());
    }
}

function add_shade_options_before_hardware() {
    get_template_part('template-parts/product/shade-options');
}
add_action('woocommerce_after_single_product_summary', 'add_shade_options_before_hardware', 14);

// make 100% sure Gravity Forms never attaches the file to avoid 413 request errors
add_filter('gform_notification', 'disable_file_attachments_in_emails', 10, 3);
function disable_file_attachments_in_emails($notification, $form, $entry) {
    if (isset($notification['attachments'])) {
        $notification['attachments'] = array(); // Clear all attachments
    }
    return $notification;
}

function cptui_register_my_cpts_palmshield_resources() {

	/**
	 * Post Type: Resources.
	 */

	$labels = [
		"name" => esc_html__( "Resources", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Resources", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Resources", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "palmshield_resources", "with_front" => true ],
		"query_var" => true,
		"menu_position" => 5,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "revisions", "author", "page-attributes", "post-formats" ],
		"taxonomies" => [ "category" ],
		"show_in_graphql" => false,
	];

	register_post_type( "palmshield_resources", $args );
}

add_action( 'init', 'cptui_register_my_cpts_palmshield_resources' );

function cptui_register_my_taxes_resource_type() {

	/**
	 * Taxonomy: Resource type.
	 */

	$labels = [
		"name" => esc_html__( "Resource type", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Resource type", "custom-post-type-ui" ),
	];

	
	$args = [
		"label" => esc_html__( "Resource type", "custom-post-type-ui" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'resource_type', 'with_front' => true, ],
		"show_admin_column" => true, 
		"show_in_rest" => true,
		"show_tagcloud" => true,
		"rest_base" => "resource_type",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true, 
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "resource_type", [ "palmshield_resources" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_resource_type' );

// Force lazy loading for all Smart Slider instances
// add_filter('smartslider3_slider_params', function($params) {
//     $params['lazy'] = 1;
//     $params['lazyload'] = 'afterOnLoad';
//     return $params;
// });

// Delay slider initialization until needed
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize sliders when they come into viewport
        if ('IntersectionObserver' in window) {
            const sliderObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const slider = entry.target;
                        if (slider.getAttribute('data-lazy-slider')) {
                            // Trigger slider initialization
                            slider.removeAttribute('data-lazy-slider');
                            sliderObserver.unobserve(slider);
                        }
                    }
                });
            });
            
            document.querySelectorAll('[data-lazy-slider]').forEach(function(slider) {
                sliderObserver.observe(slider);
            });
        }
    });
    </script>
    <?php
});


add_action('woocommerce_after_single_product_summary', function() {
    global $product;
    if ($product && has_term('shades', 'product_cat', $product->get_id())) {
        echo do_shortcode('[canopy_products_grid]');
    }
}, 14);
 

//override nofollow links set by woocommerce's product button block
add_filter('woocommerce_loop_add_to_cart_args', function( $args, $product ) {
    // If it's a link with rel="nofollow", remove it
    if ( isset( $args['attributes']['rel'] ) && $args['attributes']['rel'] === 'nofollow' ) {
        unset( $args['attributes']['rel'] );
    }
    return $args;
}, 20, 2);

// ============================================================================
// LOCAL BUSINESS SCHEMA MARKUP - Official External Profiles
// ============================================================================

/**
 * Add LocalBusiness JSON-LD schema markup for SEO
 * Includes official external profiles for Arcat, CADdetails, Sweets, and Architizer
 */
add_action('wp_head', 'add_local_business_schema', 1);
function add_local_business_schema() {
    // Only add on frontend, not admin
    if (is_admin()) {
        return;
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        '@id' => 'https://palmshieldlouvers.com/#organization',
        'name' => 'PalmSHIELD Louvers & Architectural Screening',
        'url' => 'https://palmshieldlouvers.com/',
        'logo' => 'https://palmshieldlouvers.com/wp-content/uploads/2024/10/palmshield-logo-large-trans.png',
        'image' => 'https://palmshieldlouvers.com/wp-content/uploads/2024/10/palmshield-logo-large-trans.png',
        'description' => 'PalmSHIELD Louvers & Architectural Screening is a leading manufacturer of architectural louvers, mechanical equipment enclosures, rooftop screening, and industrial screening systems.',
        'telephone' => '+1-531-325-8080',
        'email' => 'info@palmshieldlouvers.com',
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => '300 E Locust St',
            'addressLocality' => 'Carter Lake',
            'addressRegion' => 'IA',
            'postalCode' => '51510',
            'addressCountry' => 'US'
        ),
        'openingHoursSpecification' => array(
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array(
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday'
                ),
                'opens' => '08:00',
                'closes' => '16:30'
            ),
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array(
                    'Saturday',
                    'Sunday'
                ),
                'opens' => '00:00',
                'closes' => '00:00',
                'description' => 'Closed'
            )
        ),
        'sameAs' => array(
            'https://www.facebook.com/PalmSHIELDLouvers',
            'https://www.pinterest.com/palmshield/',
            'https://www.arcat.com/company/palmshield-53925',
            'https://www.caddetails.com/main/company/viewcompanycontent?companyID=5555&viewSource=Company%20Content&isFeatured=False',
            'https://sweets.construction.com/searchresults/internal/manufacturer-palmshield',
            'https://architizer.com/brands/palmshield/'
        ),
        'department' => array(
            '@type' => 'MechanicalEngineering',
            'name' => 'PalmSHIELD Louvers & Architectural Screening - Mechanical Engineering'
        )
    );
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}


// Disable WordPress core sitemaps
add_filter( 'wp_sitemaps_enabled', '__return_false' );

// ============================================================================
// TEMPORARY FIX: Convert uppercase block names to lowercase
// ============================================================================
// This filter catches blocks with uppercase characters and converts them to lowercase
// Remove this after identifying and fixing the source block registration
add_filter( 'block_type_metadata', function( $metadata ) {
    if ( isset( $metadata['name'] ) && preg_match( '/[A-Z]/', $metadata['name'] ) ) {
        error_log( 'WARNING: Block with uppercase name detected: ' . $metadata['name'] );
        // Convert to lowercase and ensure proper namespace format
        $name = strtolower( $metadata['name'] );
        // If no namespace, add acf/ prefix
        if ( strpos( $name, '/' ) === false ) {
            $name = 'acf/' . $name;
        }
        $metadata['name'] = $name;
        error_log( 'Fixed block name to: ' . $metadata['name'] );
    }
    return $metadata;
}, 5 ); // Priority 5 to run before ACF processes it

// Also catch blocks registered via acf_register_block_type
add_filter( 'acf/register_block_type_args', function( $block ) {
    if ( isset( $block['name'] ) && preg_match( '/[A-Z]/', $block['name'] ) ) {
        error_log( 'WARNING: ACF Block with uppercase name detected: ' . $block['name'] );
        // Convert to lowercase and ensure proper namespace format
        $name = strtolower( $block['name'] );
        // If no namespace, add acf/ prefix
        if ( strpos( $name, '/' ) === false ) {
            $name = 'acf/' . $name;
        }
        $block['name'] = $name;
        error_log( 'Fixed ACF block name to: ' . $block['name'] );
    }
    return $block;
}, 5 );


// Disable Gravity Forms submit button after click to prevent multiple submissions
add_filter( 'gform_submit_button', 'gf_disable_button_on_submit', 10, 2 );
function gf_disable_button_on_submit( $button, $form ) {
    $dom = new DOMDocument();
    $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $button );
    $input = $dom->getElementsByTagName( 'input' )->item(0);
    
    if ( $input ) {
        $onclick = $input->getAttribute( 'onclick' );
        $onclick .= " this.value='Submitting...'; this.disabled=true;";
        $input->setAttribute( 'onclick', $onclick );
        $button = $dom->saveHTML( $input );
    }
    
    return $button;
}