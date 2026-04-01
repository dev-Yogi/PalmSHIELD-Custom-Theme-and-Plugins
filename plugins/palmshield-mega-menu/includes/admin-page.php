<?php
/**
 * Admin Settings Page with Repeater Fields
 * 
 * @package PalmSHIELD_Mega_Menu
 * @version 1.1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('psmm_menu_settings', array());
$logo_url = get_option('psmm_logo_url', 'https://palmshieldlouvers.com/wp-content/themes/palmshield/images/ps-logo-large.png');
$logo_link = get_option('psmm_logo_link', home_url('/'));

// Get default settings for reference
$plugin = PalmSHIELD_Mega_Menu::get_instance();
$defaults = $plugin->get_default_settings();

// Merge with defaults to ensure all keys exist
$settings = wp_parse_args($settings, $defaults);

// Helper function to clean URL values
function psmm_clean_url_value($url) {
    $url = trim($url);
    if (empty($url) || $url === '#' || $url === '#!' || preg_match('/^#[\w-]*$/', $url)) {
        return '';
    }
    return $url;
}

/**
 * Render a repeater section
 */
function psmm_render_repeater($section_key, $category_key, $items, $has_description = true) {
    $base_name = "psmm_menu_settings[{$section_key}][{$category_key}]";
    ?>
    <div class="psmm-repeater" data-section="<?php echo esc_attr($section_key); ?>" data-category="<?php echo esc_attr($category_key); ?>" data-has-description="<?php echo $has_description ? 'true' : 'false'; ?>">
        <div class="psmm-repeater-items">
            <?php 
            if (!empty($items)) :
                foreach ($items as $index => $item) : 
            ?>
                <div class="psmm-repeater-item" data-index="<?php echo $index; ?>">
                    <div class="psmm-item-fields">
                        <input type="text" 
                               name="<?php echo $base_name; ?>[<?php echo $index; ?>][title]" 
                               value="<?php echo esc_attr($item['title']); ?>" 
                               placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" 
                               class="regular-text psmm-field-title">
                        <?php if ($has_description) : ?>
                        <input type="text" 
                               name="<?php echo $base_name; ?>[<?php echo $index; ?>][description]" 
                               value="<?php echo esc_attr(isset($item['description']) ? $item['description'] : ''); ?>" 
                               placeholder="<?php esc_attr_e('Description (optional)', 'palmshield-mega-menu'); ?>" 
                               class="regular-text psmm-field-description">
                        <?php endif; ?>
                        <input type="text" 
                               name="<?php echo $base_name; ?>[<?php echo $index; ?>][url]" 
                               value="<?php echo esc_attr(psmm_clean_url_value($item['url'])); ?>" 
                               placeholder="/page-slug/ or https://..." 
                               class="regular-text psmm-field-url">
                    </div>
                    <button type="button" class="button psmm-remove-item" title="<?php esc_attr_e('Remove Item', 'palmshield-mega-menu'); ?>">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            <?php 
                endforeach;
            endif; 
            ?>
        </div>
        <button type="button" class="button button-secondary psmm-add-item">
            <span class="dashicons dashicons-plus-alt2"></span>
            <?php esc_html_e('Add Item', 'palmshield-mega-menu'); ?>
        </button>
    </div>
    <?php
}
?>

<div class="wrap psmm-admin-wrap">
    <h1><?php esc_html_e('PalmSHIELD Mega Menu Settings', 'palmshield-mega-menu'); ?></h1>
    
    <div class="psmm-admin-intro">
        <p><?php esc_html_e('Configure your mega menu links and content below. Use the shortcode', 'palmshield-mega-menu'); ?> <code>[palmshield_mega_menu]</code> <?php esc_html_e('to display the menu, or add', 'palmshield-mega-menu'); ?> <code>&lt;?php do_action('palmshield_mega_menu'); ?&gt;</code> <?php esc_html_e('to your theme.', 'palmshield-mega-menu'); ?></p>
        <p class="psmm-tip"><strong><?php esc_html_e('Tip:', 'palmshield-mega-menu'); ?></strong> <?php esc_html_e('Click "+ Add Item" to add more links to any category. Click the trash icon to remove items.', 'palmshield-mega-menu'); ?></p>
    </div>
    
    <form method="post" action="options.php" id="psmm-settings-form">
        <?php settings_fields('psmm_settings_group'); ?>
        
        <!-- Logo Settings -->
        <div class="psmm-section">
            <h2><?php esc_html_e('Logo Settings', 'palmshield-mega-menu'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="psmm_logo_url"><?php esc_html_e('Logo URL', 'palmshield-mega-menu'); ?></label></th>
                    <td>
                        <input type="url" id="psmm_logo_url" name="psmm_logo_url" value="<?php echo esc_url($logo_url); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('Enter the full URL to your logo image.', 'palmshield-mega-menu'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="psmm_logo_link"><?php esc_html_e('Logo Link', 'palmshield-mega-menu'); ?></label></th>
                    <td>
                        <input type="url" id="psmm_logo_link" name="psmm_logo_link" value="<?php echo esc_url($logo_link); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('Where should the logo link to? (Usually your homepage)', 'palmshield-mega-menu'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tab Navigation -->
        <h2 class="nav-tab-wrapper psmm-tabs">
            <a href="#screens-tab" class="nav-tab nav-tab-active"><?php esc_html_e('Screens', 'palmshield-mega-menu'); ?></a>
            <a href="#enclosures-tab" class="nav-tab"><?php esc_html_e('Enclosures', 'palmshield-mega-menu'); ?></a>
            <a href="#amenities-tab" class="nav-tab"><?php esc_html_e('Site Amenities', 'palmshield-mega-menu'); ?></a>
            <a href="#gates-tab" class="nav-tab"><?php esc_html_e('Gates', 'palmshield-mega-menu'); ?></a>
            <a href="#resources-tab" class="nav-tab"><?php esc_html_e('Resources', 'palmshield-mega-menu'); ?></a>
            <a href="#cta-menu-tab" class="nav-tab"><?php esc_html_e('CTA Menu Items', 'palmshield-mega-menu'); ?></a>
        </h2>

        <!-- SCREENS Tab -->
        <div id="screens-tab" class="psmm-tab-content active">
            <h3><?php esc_html_e('By Application', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['screens']['by_application']) ? $settings['screens']['by_application'] : $defaults['screens']['by_application'];
            psmm_render_repeater('screens', 'by_application', $items, true);
            ?>

            <h3><?php esc_html_e('By Performance', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['screens']['by_performance']) ? $settings['screens']['by_performance'] : $defaults['screens']['by_performance'];
            psmm_render_repeater('screens', 'by_performance', $items, true);
            ?>

            <h3><?php esc_html_e('By Style', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['screens']['by_style']) ? $settings['screens']['by_style'] : $defaults['screens']['by_style'];
            psmm_render_repeater('screens', 'by_style', $items, false);
            ?>

            <h3><?php esc_html_e('Quick Links', 'palmshield-mega-menu'); ?></h3>
            <div class="psmm-link-group">
                <label><?php esc_html_e('View All Link', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[screens][quick_links][view_all][title]" value="<?php echo esc_attr($settings['screens']['quick_links']['view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[screens][quick_links][view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['screens']['quick_links']['view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>
            <div class="psmm-link-group">
                <label><?php esc_html_e('Color Options', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[screens][quick_links][color_options][title]" value="<?php echo esc_attr($settings['screens']['quick_links']['color_options']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[screens][quick_links][color_options][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['screens']['quick_links']['color_options']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Featured Box', 'palmshield-mega-menu'); ?></h3>
            <div class="psmm-featured-group">
                <p><label><?php esc_html_e('Title', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[screens][featured][title]" value="<?php echo esc_attr($settings['screens']['featured']['title']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Description', 'palmshield-mega-menu'); ?></label>
                <textarea name="psmm_menu_settings[screens][featured][description]" class="large-text" rows="2"><?php echo esc_textarea($settings['screens']['featured']['description']); ?></textarea></p>
                <p><label><?php esc_html_e('Button Text', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[screens][featured][button_text]" value="<?php echo esc_attr($settings['screens']['featured']['button_text']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Button URL', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[screens][featured][button_url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['screens']['featured']['button_url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text"></p>
            </div>
        </div>

        <!-- ENCLOSURES Tab -->
        <div id="enclosures-tab" class="psmm-tab-content">
            <h3><?php esc_html_e('Enclosure Types', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['enclosures']['enclosure_types']) ? $settings['enclosures']['enclosure_types'] : $defaults['enclosures']['enclosure_types'];
            psmm_render_repeater('enclosures', 'enclosure_types', $items, true);
            ?>

            <div class="psmm-link-group">
                <label><?php esc_html_e('View All Link', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[enclosures][enclosure_types_view_all][title]" value="<?php echo esc_attr(isset($settings['enclosures']['enclosure_types_view_all']['title']) ? $settings['enclosures']['enclosure_types_view_all']['title'] : 'View All Enclosures'); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[enclosures][enclosure_types_view_all][url]" value="<?php echo esc_attr(isset($settings['enclosures']['enclosure_types_view_all']['url']) ? psmm_clean_url_value($settings['enclosures']['enclosure_types_view_all']['url']) : ''); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Featured Box', 'palmshield-mega-menu'); ?></h3>
            <div class="psmm-featured-group">
                <p><label><?php esc_html_e('Title', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[enclosures][featured][title]" value="<?php echo esc_attr($settings['enclosures']['featured']['title']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Description', 'palmshield-mega-menu'); ?></label>
                <textarea name="psmm_menu_settings[enclosures][featured][description]" class="large-text" rows="2"><?php echo esc_textarea($settings['enclosures']['featured']['description']); ?></textarea></p>
                <p><label><?php esc_html_e('Button Text', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[enclosures][featured][button_text]" value="<?php echo esc_attr($settings['enclosures']['featured']['button_text']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Button URL', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[enclosures][featured][button_url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['enclosures']['featured']['button_url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text"></p>
            </div>
        </div>

        <!-- SITE AMENITIES Tab -->
        <div id="amenities-tab" class="psmm-tab-content">
            <h3><?php esc_html_e('Railings', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['amenities']['railings']) ? $settings['amenities']['railings'] : $defaults['amenities']['railings'];
            psmm_render_repeater('amenities', 'railings', $items, true);
            ?>
            <div class="psmm-link-group psmm-view-all-link">
                <label><?php esc_html_e('View All Railings', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[amenities][railings_view_all][title]" value="<?php echo esc_attr($settings['amenities']['railings_view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[amenities][railings_view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['amenities']['railings_view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Bollards', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['amenities']['bollards']) ? $settings['amenities']['bollards'] : $defaults['amenities']['bollards'];
            psmm_render_repeater('amenities', 'bollards', $items, true);
            ?>
            <div class="psmm-link-group psmm-view-all-link">
                <label><?php esc_html_e('View All Bollards', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[amenities][bollards_view_all][title]" value="<?php echo esc_attr($settings['amenities']['bollards_view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[amenities][bollards_view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['amenities']['bollards_view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Shades', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['amenities']['shades']) ? $settings['amenities']['shades'] : $defaults['amenities']['shades'];
            psmm_render_repeater('amenities', 'shades', $items, true);
            ?>
            <div class="psmm-link-group psmm-view-all-link">
                <label><?php esc_html_e('View All Shades', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[amenities][shades_view_all][title]" value="<?php echo esc_attr($settings['amenities']['shades_view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[amenities][shades_view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['amenities']['shades_view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Featured Box', 'palmshield-mega-menu'); ?></h3>
            <div class="psmm-featured-group">
                <p><label><?php esc_html_e('Title', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[amenities][featured][title]" value="<?php echo esc_attr($settings['amenities']['featured']['title']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Description', 'palmshield-mega-menu'); ?></label>
                <textarea name="psmm_menu_settings[amenities][featured][description]" class="large-text" rows="2"><?php echo esc_textarea($settings['amenities']['featured']['description']); ?></textarea></p>
                <p><label><?php esc_html_e('Button Text', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[amenities][featured][button_text]" value="<?php echo esc_attr($settings['amenities']['featured']['button_text']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Button URL', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[amenities][featured][button_url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['amenities']['featured']['button_url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text"></p>
            </div>
        </div>

        <!-- GATES Tab -->
        <div id="gates-tab" class="psmm-tab-content">
            <h3><?php esc_html_e('Access Control+', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['gates']['access_control']) ? $settings['gates']['access_control'] : $defaults['gates']['access_control'];
            psmm_render_repeater('gates', 'access_control', $items, true);
            ?>
            <div class="psmm-link-group psmm-view-all-link">
                <label><?php esc_html_e('View All Access Control+', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[gates][access_control_view_all][title]" value="<?php echo esc_attr($settings['gates']['access_control_view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[gates][access_control_view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['gates']['access_control_view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Architectural Gates', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['gates']['architectural']) ? $settings['gates']['architectural'] : $defaults['gates']['architectural'];
            psmm_render_repeater('gates', 'architectural', $items, true);
            ?>
            <div class="psmm-link-group psmm-view-all-link">
                <label><?php esc_html_e('View All Architectural Gates', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[gates][architectural_view_all][title]" value="<?php echo esc_attr($settings['gates']['architectural_view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[gates][architectural_view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['gates']['architectural_view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Vulcan Pedestrian Gates', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['gates']['pedestrian']) ? $settings['gates']['pedestrian'] : $defaults['gates']['pedestrian'];
            psmm_render_repeater('gates', 'pedestrian', $items, true);
            ?>
            <div class="psmm-link-group psmm-view-all-link">
                <label><?php esc_html_e('View All Pedestrian Gates', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[gates][pedestrian_view_all][title]" value="<?php echo esc_attr($settings['gates']['pedestrian_view_all']['title']); ?>" placeholder="<?php esc_attr_e('Title', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[gates][pedestrian_view_all][url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['gates']['pedestrian_view_all']['url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>

            <h3><?php esc_html_e('Hardware', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['gates']['hardware']) ? $settings['gates']['hardware'] : $defaults['gates']['hardware'];
            psmm_render_repeater('gates', 'hardware', $items, false);
            ?>

            <h3><?php esc_html_e('Featured Box (Architect Resources)', 'palmshield-mega-menu'); ?></h3>
            <div class="psmm-featured-group">
                <p><label><?php esc_html_e('Title', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[gates][featured][title]" value="<?php echo esc_attr($settings['gates']['featured']['title']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Description', 'palmshield-mega-menu'); ?></label>
                <textarea name="psmm_menu_settings[gates][featured][description]" class="large-text" rows="2"><?php echo esc_textarea($settings['gates']['featured']['description']); ?></textarea></p>
                <p><label><?php esc_html_e('Button Text', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[gates][featured][button_text]" value="<?php echo esc_attr($settings['gates']['featured']['button_text']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Button URL', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[gates][featured][button_url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['gates']['featured']['button_url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text"></p>
            </div>
        </div>

        <!-- RESOURCES Tab -->
        <div id="resources-tab" class="psmm-tab-content">
            <h3><?php esc_html_e('Technical Resources', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['resources']['technical']) ? $settings['resources']['technical'] : $defaults['resources']['technical'];
            psmm_render_repeater('resources', 'technical', $items, true);
            ?>

            <h3><?php esc_html_e('Sales Resources', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['resources']['sales']) ? $settings['resources']['sales'] : $defaults['resources']['sales'];
            psmm_render_repeater('resources', 'sales', $items, true);
            ?>

            <h3><?php esc_html_e('Support', 'palmshield-mega-menu'); ?></h3>
            <?php 
            $items = isset($settings['resources']['support']) ? $settings['resources']['support'] : $defaults['resources']['support'];
            psmm_render_repeater('resources', 'support', $items, true);
            ?>

            <h3><?php esc_html_e('Featured Box', 'palmshield-mega-menu'); ?></h3>
            <div class="psmm-featured-group">
                <p><label><?php esc_html_e('Title', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[resources][featured][title]" value="<?php echo esc_attr($settings['resources']['featured']['title']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Description', 'palmshield-mega-menu'); ?></label>
                <textarea name="psmm_menu_settings[resources][featured][description]" class="large-text" rows="2"><?php echo esc_textarea($settings['resources']['featured']['description']); ?></textarea></p>
                <p><label><?php esc_html_e('Button Text', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[resources][featured][button_text]" value="<?php echo esc_attr($settings['resources']['featured']['button_text']); ?>" class="regular-text"></p>
                <p><label><?php esc_html_e('Button URL', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[resources][featured][button_url]" value="<?php echo esc_attr(psmm_clean_url_value($settings['resources']['featured']['button_url'])); ?>" placeholder="/page-slug/ or https://..." class="regular-text"></p>
            </div>
        </div>

        <!-- CTA MENU ITEMS Tab -->
        <div id="cta-menu-tab" class="psmm-tab-content">
            <h3><?php esc_html_e('CTA Menu Items', 'palmshield-mega-menu'); ?></h3>
            <p class="description"><?php esc_html_e('These items will appear as separate CTA buttons in the navigation bar, styled differently from the main menu items.', 'palmshield-mega-menu'); ?></p>
            
            <div class="psmm-link-group">
                <label><?php esc_html_e('Careers', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[cta_menu][careers][title]" value="<?php echo esc_attr(isset($settings['cta_menu']['careers']['title']) ? $settings['cta_menu']['careers']['title'] : ''); ?>" placeholder="<?php esc_attr_e('Button Text', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[cta_menu][careers][url]" value="<?php echo esc_attr(isset($settings['cta_menu']['careers']['url']) ? psmm_clean_url_value($settings['cta_menu']['careers']['url']) : ''); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>
            
            <div class="psmm-link-group">
                <label><?php esc_html_e('Join Our Dealer Network', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[cta_menu][dealer_network][title]" value="<?php echo esc_attr(isset($settings['cta_menu']['dealer_network']['title']) ? $settings['cta_menu']['dealer_network']['title'] : ''); ?>" placeholder="<?php esc_attr_e('Button Text', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[cta_menu][dealer_network][url]" value="<?php echo esc_attr(isset($settings['cta_menu']['dealer_network']['url']) ? psmm_clean_url_value($settings['cta_menu']['dealer_network']['url']) : ''); ?>" placeholder="/page-slug/ or https://..." class="regular-text">
            </div>
            
            <div class="psmm-link-group">
                <label><?php esc_html_e('Search', 'palmshield-mega-menu'); ?></label>
                <input type="text" name="psmm_menu_settings[cta_menu][search][title]" value="<?php echo esc_attr(isset($settings['cta_menu']['search']['title']) ? $settings['cta_menu']['search']['title'] : ''); ?>" placeholder="<?php esc_attr_e('Button Text', 'palmshield-mega-menu'); ?>" class="regular-text">
                <input type="text" name="psmm_menu_settings[cta_menu][search][url]" value="<?php echo esc_attr(isset($settings['cta_menu']['search']['url']) ? psmm_clean_url_value($settings['cta_menu']['search']['url']) : ''); ?>" placeholder="/search/ or https://..." class="regular-text">
                <p class="description"><?php esc_html_e('Leave URL empty to use WordPress default search functionality.', 'palmshield-mega-menu'); ?></p>
            </div>
        </div>

        <?php submit_button(__('Save Changes', 'palmshield-mega-menu')); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.psmm-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        
        $('.psmm-tabs .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.psmm-tab-content').removeClass('active');
        $(target).addClass('active');
    });

    // Repeater: Add Item
    $(document).on('click', '.psmm-add-item', function(e) {
        e.preventDefault();
        
        var $repeater = $(this).closest('.psmm-repeater');
        var $itemsContainer = $repeater.find('.psmm-repeater-items');
        var section = $repeater.data('section');
        var category = $repeater.data('category');
        var hasDescription = $repeater.data('has-description') === true || $repeater.data('has-description') === 'true';
        
        // Find the highest current index
        var maxIndex = -1;
        $itemsContainer.find('.psmm-repeater-item').each(function() {
            var idx = parseInt($(this).data('index'), 10);
            if (idx > maxIndex) maxIndex = idx;
        });
        var newIndex = maxIndex + 1;
        
        var baseName = 'psmm_menu_settings[' + section + '][' + category + '][' + newIndex + ']';
        
        var descriptionField = '';
        if (hasDescription) {
            descriptionField = '<input type="text" name="' + baseName + '[description]" value="" placeholder="Description (optional)" class="regular-text psmm-field-description">';
        }
        
        var newItem = '<div class="psmm-repeater-item" data-index="' + newIndex + '">' +
            '<div class="psmm-item-fields">' +
                '<input type="text" name="' + baseName + '[title]" value="" placeholder="Title" class="regular-text psmm-field-title">' +
                descriptionField +
                '<input type="text" name="' + baseName + '[url]" value="" placeholder="/page-slug/ or https://..." class="regular-text psmm-field-url">' +
            '</div>' +
            '<button type="button" class="button psmm-remove-item" title="Remove Item">' +
                '<span class="dashicons dashicons-trash"></span>' +
            '</button>' +
        '</div>';
        
        $itemsContainer.append(newItem);
        
        // Focus the new title field
        $itemsContainer.find('.psmm-repeater-item:last-child .psmm-field-title').focus();
    });

    // Repeater: Remove Item
    $(document).on('click', '.psmm-remove-item', function(e) {
        e.preventDefault();
        
        var $item = $(this).closest('.psmm-repeater-item');
        var $repeater = $(this).closest('.psmm-repeater');
        
        // Confirm if there's data in the fields
        var hasData = false;
        $item.find('input').each(function() {
            if ($(this).val().trim() !== '') {
                hasData = true;
            }
        });
        
        if (hasData) {
            if (!confirm('Are you sure you want to remove this item?')) {
                return;
            }
        }
        
        $item.slideUp(200, function() {
            $(this).remove();
        });
    });
});
</script>
