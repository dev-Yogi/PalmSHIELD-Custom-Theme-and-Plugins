<?php
/*
Plugin Name: Scrolling Announcement Banner
Description: Adds a customizable scrolling banner to your website
Version: 1.0
Author: Vanessa Kasun
*/

if (!defined('ABSPATH')) exit;

class ScrollingBanner {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Use output buffering to ensure banner appears at the top
        add_action('template_redirect', array($this, 'start_output_buffer'));
    }

    public function enqueue_front_scripts() {
        // Debug output
        error_log('Enqueuing front-end scripts and styles');
        
        wp_enqueue_style(
            'scrolling-banner-style', 
            plugins_url('css/front-style.css', __FILE__),
            array(),
            time() // Force cache bust
        );
        
        wp_enqueue_script(
            'scrolling-banner-script',
            plugins_url('js/front-script.js', __FILE__),
            array('jquery'),
            time(), // Force cache bust
            true
        );
    }

    public function enqueue_admin_scripts($hook) {
        if('settings_page_scrolling-banner' != $hook) return;
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_media();
        wp_enqueue_script('scrolling-banner-admin', plugins_url('js/admin-script.js', __FILE__), array('jquery', 'wp-color-picker'), '1.0', true);
    }

    public function add_admin_menu() {
        add_options_page(
            'Scrolling Banner Settings',
            'Scrolling Banner',
            'manage_options',
            'scrolling-banner',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('scrolling_banner_options', 'scrolling_banner_settings', array($this, 'sanitize_settings'));
        
        add_settings_section(
            'scrolling_banner_main',
            'Banner Settings',
            array($this, 'settings_section_callback'),
            'scrolling-banner'
        );

        // Banner Content
        add_settings_field(
            'banner_content',
            'Banner Content',
            array($this, 'banner_content_callback'),
            'scrolling-banner',
            'scrolling_banner_main'
        );

        // Display Location
        add_settings_field(
            'display_location',
            'Display Location',
            array($this, 'display_location_callback'),
            'scrolling-banner',
            'scrolling_banner_main'
        );

        // Specific Pages
        add_settings_field(
            'specific_pages',
            'Select Specific Pages',
            array($this, 'specific_pages_callback'),
            'scrolling-banner',
            'scrolling_banner_main'
        );

        // Banner Style
        add_settings_field(
            'banner_style',
            'Banner Style',
            array($this, 'banner_style_callback'),
            'scrolling-banner',
            'scrolling_banner_main'
        );

        // Add cookie duration setting
        add_settings_field(
            'cookie_duration',
            'Banner Hide Duration',
            array($this, 'cookie_duration_callback'),
            'scrolling-banner',
            'scrolling_banner_main'
        );
    }

    public function settings_section_callback() {
        echo '<p>Configure your scrolling announcement banner settings below.</p>';
    }

    public function banner_content_callback() {
        $options = get_option('scrolling_banner_settings');
        $content = isset($options['banner_content']) ? wp_unslash($options['banner_content']) : '';
        wp_editor($content, 'banner_content', array(
            'textarea_name' => 'scrolling_banner_settings[banner_content]',
            'textarea_rows' => 5,
            'media_buttons' => true,
            'teeny' => true
        ));
    }

    public function display_location_callback() {
        $options = get_option('scrolling_banner_settings');
        $location = isset($options['display_location']) ? $options['display_location'] : 'all';
        ?>
        <select name="scrolling_banner_settings[display_location]" id="display_location">
            <option value="all" <?php selected($location, 'all'); ?>>All Pages</option>
            <option value="home" <?php selected($location, 'home'); ?>>Homepage Only</option>
            <option value="specific" <?php selected($location, 'specific'); ?>>Specific Pages</option>
        </select>
        <p class="description">Choose where you want the banner to appear</p>
        <?php
    }

    public function specific_pages_callback() {
        $options = get_option('scrolling_banner_settings');
        $selected_pages = isset($options['specific_pages']) ? $options['specific_pages'] : array();
        
        $pages = get_pages();
        echo '<div class="specific-pages" style="max-height: 200px; overflow-y: auto;">';
        foreach ($pages as $page) {
            $checked = in_array($page->ID, (array)$selected_pages) ? 'checked' : '';
            echo sprintf(
                '<label><input type="checkbox" name="scrolling_banner_settings[specific_pages][]" value="%d" %s> %s</label><br>',
                $page->ID,
                $checked,
                esc_html($page->post_title)
            );
        }
        echo '</div>';
    }

    public function banner_style_callback() {
        $options = get_option('scrolling_banner_settings');
        $bg_color = isset($options['bg_color']) ? $options['bg_color'] : '#ffffff';
        $text_color = isset($options['text_color']) ? $options['text_color'] : '#000000';
        $speed = isset($options['scroll_speed']) ? $options['scroll_speed'] : '20';
        ?>
        <p>
            <label>Background Color:<br>
            <input type="text" class="color-picker" name="scrolling_banner_settings[bg_color]" value="<?php echo esc_attr($bg_color); ?>">
            </label>
        </p>
        <p>
            <label>Text Color:<br>
            <input type="text" class="color-picker" name="scrolling_banner_settings[text_color]" value="<?php echo esc_attr($text_color); ?>">
            </label>
        </p>
        <p>
            <label>Scroll Speed (seconds):<br>
            <input type="number" name="scrolling_banner_settings[scroll_speed]" value="<?php echo esc_attr($speed); ?>" min="5" max="60">
            </label>
        </p>
        <?php
    }

    public function cookie_duration_callback() {
        $options = get_option('scrolling_banner_settings');
        $duration = isset($options['cookie_duration']) ? $options['cookie_duration'] : 1;
        ?>
        <select name="scrolling_banner_settings[cookie_duration]">
            <option value="0" <?php selected($duration, 0); ?>>Until browser closes</option>
            <option value="1" <?php selected($duration, 1); ?>>1 day</option>
            <option value="7" <?php selected($duration, 7); ?>>1 week</option>
            <option value="30" <?php selected($duration, 30); ?>>1 month</option>
        </select>
        <?php
    }

    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (isset($input['banner_content'])) {
            $sanitized['banner_content'] = wp_kses_post($input['banner_content']);
        }
        
        if (isset($input['display_location'])) {
            $sanitized['display_location'] = sanitize_text_field($input['display_location']);
        }
        
        if (isset($input['specific_pages']) && is_array($input['specific_pages'])) {
            $sanitized['specific_pages'] = array_map('absint', $input['specific_pages']);
        }
        
        if (isset($input['bg_color'])) {
            $sanitized['bg_color'] = sanitize_hex_color($input['bg_color']);
        }
        
        if (isset($input['text_color'])) {
            $sanitized['text_color'] = sanitize_hex_color($input['text_color']);
        }
        
        if (isset($input['scroll_speed'])) {
            $sanitized['scroll_speed'] = absint($input['scroll_speed']);
            if ($sanitized['scroll_speed'] < 5) $sanitized['scroll_speed'] = 5;
            if ($sanitized['scroll_speed'] > 60) $sanitized['scroll_speed'] = 60;
        }
        
        // Add cookie duration sanitization
        if (isset($input['cookie_duration'])) {
            $sanitized['cookie_duration'] = absint($input['cookie_duration']);
        }
        
        return $sanitized;
    }

    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('scrolling_banner_options');
                do_settings_sections('scrolling-banner');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    public function start_output_buffer() {
        ob_start(array($this, 'inject_banner_into_html'));
    }

    public function inject_banner_into_html($html) {
        $options = get_option('scrolling_banner_settings');
        
        if (!$this->should_display_banner($options)) {
            return $html;
        }
        
        // Get banner settings
        $content = isset($options['banner_content']) ? wp_kses_post($options['banner_content']) : '';
        $bg_color = isset($options['bg_color']) ? esc_attr($options['bg_color']) : '#ffffff';
        $text_color = isset($options['text_color']) ? esc_attr($options['text_color']) : '#000000';
        $speed = isset($options['scroll_speed']) ? absint($options['scroll_speed']) : 20;
        $cookie_duration = isset($options['cookie_duration']) ? absint($options['cookie_duration']) : 1;
        
        // Create banner HTML
        $banner = sprintf(
            '<div class="scrolling-banner" style="background-color: %s; color: %s;" data-cookie-duration="%d">
                <div class="scrolling-banner-content" data-speed="%d">%s</div>
                <button class="scrolling-banner-close" aria-label="Close banner">×</button>
            </div>',
            $bg_color,
            $text_color,
            $cookie_duration,
            $speed,
            do_shortcode($content)
        );
        
        // Insert banner after the body tag
        $html = preg_replace('/(<body[^>]*>)/i', '$1' . $banner, $html);
        
        return $html;
    }

    public function should_display_banner($options) {
        // Always show for admin preview
        if (isset($_GET['preview_banner']) && current_user_can('manage_options')) {
            return true;
        }
        
        // Check if banner is enabled and has content
        if (empty($options) || empty($options['banner_content'])) {
            return false;
        }
        
        // Check if banner was closed
        if (isset($_COOKIE['scrolling_banner_closed']) && $_COOKIE['scrolling_banner_closed'] === 'true') {
            return false;
        }
        
        // Check display location settings
        $display_location = isset($options['display_location']) ? $options['display_location'] : 'all';
        
        switch ($display_location) {
            case 'home':
                // Only show on homepage
                if (!is_front_page() && !is_home()) {
                    return false;
                }
                break;
                
            case 'specific':
                // Only show on specific pages
                $specific_pages = isset($options['specific_pages']) ? (array)$options['specific_pages'] : array();
                if (!empty($specific_pages)) {
                    $current_page_id = get_queried_object_id();
                    if (!in_array($current_page_id, $specific_pages)) {
                        return false;
                    }
                } else {
                    // If specific pages is selected but no pages are chosen, don't show anywhere
                    return false;
                }
                break;
                
            case 'all':
            default:
                // Show on all pages (default behavior)
                break;
        }
        
        return true;
    }

    public function display_scrolling_banner() {
        $options = get_option('scrolling_banner_settings');
        
        if (!$this->should_display_banner($options)) {
            return;
        }
        
        // Get banner settings
        $content = isset($options['banner_content']) ? wp_kses_post($options['banner_content']) : '';
        $bg_color = isset($options['bg_color']) ? esc_attr($options['bg_color']) : '#ffffff';
        $text_color = isset($options['text_color']) ? esc_attr($options['text_color']) : '#000000';
        $speed = isset($options['scroll_speed']) ? absint($options['scroll_speed']) : 20;
        $cookie_duration = isset($options['cookie_duration']) ? absint($options['cookie_duration']) : 1;
        
        // Output banner HTML
        printf(
            '<div class="scrolling-banner" style="background-color: %s; color: %s;" data-cookie-duration="%d">
                <div class="scrolling-banner-content" data-speed="%d">%s</div>
                <button class="scrolling-banner-close" aria-label="Close banner">×</button>
            </div>',
            $bg_color,
            $text_color,
            $cookie_duration,
            $speed,
            do_shortcode($content)
        );
    }
}

$scrolling_banner = new ScrollingBanner();

// Make the function available globally
add_action('init', function() {
    if (!function_exists('display_scrolling_banner')) {
        function display_scrolling_banner() {
            global $scrolling_banner_plugin;
            if ($scrolling_banner_plugin) {
                $scrolling_banner_plugin->display_scrolling_banner();
            }
        }
    }
}); 