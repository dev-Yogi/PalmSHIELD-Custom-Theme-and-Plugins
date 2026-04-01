<?php
/**
 * Plugin Name: PalmSHIELD Mega Menu
 * Plugin URI: https://palmshield.com
 * Description: Custom mega menu navigation for PalmSHIELD website with hover dropdowns and responsive design.
 * Version: 1.1.0
 * Author: PalmSHIELD
 * Author URI: https://palmshield.com
 * License: GPL v2 or later
 * Text Domain: palmshield-mega-menu
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PSMM_VERSION', '1.1.0');
define('PSMM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PSMM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class PalmSHIELD_Mega_Menu {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Shortcode
        add_shortcode('palmshield_mega_menu', array($this, 'render_mega_menu_shortcode'));
        
        // Action hook for theme integration
        add_action('palmshield_mega_menu', array($this, 'render_mega_menu'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Google Fonts
        wp_enqueue_style(
            'psmm-google-fonts',
            'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;500;600&display=swap',
            array(),
            null
        );
        
        // Main CSS
        wp_enqueue_style(
            'psmm-styles',
            PSMM_PLUGIN_URL . 'assets/css/mega-menu.css',
            array(),
            PSMM_VERSION
        );
        
        // Main JS
        wp_enqueue_script(
            'psmm-scripts',
            PSMM_PLUGIN_URL . 'assets/js/mega-menu.js',
            array(),
            PSMM_VERSION,
            true
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_palmshield-mega-menu' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'psmm-admin-styles',
            PSMM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            PSMM_VERSION
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('PalmSHIELD Mega Menu', 'palmshield-mega-menu'),
            __('Mega Menu', 'palmshield-mega-menu'),
            'manage_options',
            'palmshield-mega-menu',
            array($this, 'render_admin_page'),
            'dashicons-menu',
            30
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('psmm_settings_group', 'psmm_menu_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));
        
        register_setting('psmm_settings_group', 'psmm_logo_url');
        register_setting('psmm_settings_group', 'psmm_logo_link');
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        return $this->sanitize_array($input);
    }
    
    private function sanitize_array($array) {
        $sanitized = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[sanitize_key($key)] = $this->sanitize_array($value);
            } else {
                // Check if this is a URL field
                $is_url_field = (strpos($key, 'url') !== false || $key === 'button_url' || $key === 'cta_url');
                
                if ($is_url_field) {
                    // Validate URL - accept full URLs (http:// or https://) or relative URLs starting with /
                    $url = trim($value);
                    if (empty($url)) {
                        $sanitized[sanitize_key($key)] = '';
                    } elseif ($url === '#' || $url === '#!' || preg_match('/^#[\w-]*$/', $url)) {
                        // Reject anchor-only URLs, set to empty
                        $sanitized[sanitize_key($key)] = '';
                    } elseif (preg_match('/^https?:\/\//i', $url)) {
                        // Full URL starting with http:// or https:// - sanitize it
                        $sanitized[sanitize_key($key)] = esc_url_raw($url);
                    } elseif (preg_match('/^\/.+/', $url)) {
                        // Relative URL starting with / - sanitize it (remove any dangerous characters)
                        // Sanitize but preserve the relative path (esc_url_raw would convert to absolute)
                        $url = sanitize_text_field($url);
                        // Remove any potentially dangerous characters but keep the path structure
                        $url = preg_replace('/[^\/\w\-\.~:?#\[\]@!$&\'()*+,;=%]/', '', $url);
                        $sanitized[sanitize_key($key)] = $url;
                    } else {
                        // Reject URLs that don't match our patterns
                        $sanitized[sanitize_key($key)] = '';
                    }
                } else {
                    $sanitized[sanitize_key($key)] = wp_kses_post($value);
                }
            }
        }
        return $sanitized;
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include PSMM_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
    /**
     * Render mega menu shortcode
     */
    public function render_mega_menu_shortcode($atts) {
        ob_start();
        $this->render_mega_menu();
        return ob_get_clean();
    }
    
    /**
     * Render the mega menu
     */
    public function render_mega_menu() {
        $settings = get_option('psmm_menu_settings', $this->get_default_settings());
        $logo_url = get_option('psmm_logo_url', 'https://palmshieldlouvers.com/wp-content/themes/palmshield/images/ps-logo-large.png');
        $logo_link = get_option('psmm_logo_link', home_url('/'));
        
        include PSMM_PLUGIN_DIR . 'templates/mega-menu.php';
    }
    
    /**
     * Get default menu settings
     */
    public function get_default_settings() {
        return array(
            'screens' => array(
                'by_application' => array(
                    array('title' => 'Rooftop & Mechanical Equipment', 'description' => 'HVAC, chillers, generators', 'url' => '#'),
                    array('title' => 'Parking Garages', 'description' => 'Multi-level structures, ventilation', 'url' => '#'),
                    array('title' => 'Ground Level & Landscape', 'description' => 'Utility areas, transformers', 'url' => '#'),
                    array('title' => 'Playgrounds & Recreation', 'description' => 'Schools, parks, daycare facilities', 'url' => '#'),
                ),
                'by_performance' => array(
                    array('title' => 'Crash Rated Screens', 'description' => 'K4, K8, K12 certified barriers', 'url' => '#'),
                    array('title' => 'Acoustic Screens', 'description' => 'Sound reduction & noise control', 'url' => '#'),
                    array('title' => 'Vision Screens', 'description' => 'Standard architectural screening', 'url' => '#'),
                ),
                'by_style' => array(
                    array('title' => 'Louver Patterns', 'url' => '#'),
                    array('title' => 'Perforated Patterns', 'url' => '#'),
                    array('title' => 'Corrugated Patterns', 'url' => '#'),
                    array('title' => 'Custom Designs', 'url' => '#'),
                ),
                'quick_links' => array(
                    'view_all' => array('title' => 'View All Screens', 'url' => '#'),
                    'color_options' => array('title' => 'Color Options', 'url' => '#'),
                ),
                'featured' => array(
                    'title' => 'Not Sure What You Need?',
                    'description' => "Answer 3 quick questions and we'll recommend the perfect screening solution for your project.",
                    'button_text' => 'Help Me Choose →',
                    'button_url' => '#',
                ),
            ),
            'enclosures' => array(
                'enclosure_types' => array(
                    array('title' => 'Dumpster Enclosures', 'description' => 'Commercial waste containment solutions', 'url' => '#'),
                    array('title' => 'Rooftop Enclosures', 'description' => 'Equipment screening for rooftop installations', 'url' => '#'),
                    array('title' => 'Pedestrian Surround Enclosures', 'description' => 'Protective screening for pedestrian areas', 'url' => '#'),
                ),
                'enclosure_types_view_all' => array('title' => 'View All Enclosures', 'url' => '#'),
                'featured' => array(
                    'title' => 'Enclosure Solutions',
                    'description' => 'From architect-driven projects to municipal applications, our enclosures provide scalable solutions for any site requirement.',
                    'button_text' => 'Compare All Options →',
                    'button_url' => '#',
                ),
            ),
            'amenities' => array(
                'railings' => array(
                    array('title' => 'Architectural Railings', 'description' => 'Commercial & residential applications', 'url' => '#'),
                    array('title' => 'ADA Compliant Railings', 'description' => 'Code-compliant accessibility solutions', 'url' => '#'),
                    
                ),
                'railings_view_all' => array('title' => 'View All Railings', 'url' => '#'),
                'bollards' => array(
                    array('title' => 'Decorative Bollards', 'description' => 'Aesthetic site protection', 'url' => '#'),
                    array('title' => 'Security Bollards', 'description' => 'High-impact vehicle barriers', 'url' => '#'),
                    array('title' => 'Removable Bollards', 'description' => 'Flexible access control', 'url' => '#'),
                ),
                'bollards_view_all' => array('title' => 'View All Bollards', 'url' => '#'),
                'shades' => array(
                    array('title' => 'Canopy Shade Systems', 'description' => 'Large-scale coverage solutions', 'url' => '#'),
                    array('title' => 'Pergola Shades', 'description' => 'Architectural shade structures', 'url' => '#'),
                    array('title' => 'Custom Shade Solutions', 'description' => 'Project-specific designs', 'url' => '#'),
                ),
                'shades_view_all' => array('title' => 'View All Shades', 'url' => '#'),
                'featured' => array(
                    'title' => 'Complete Your Project',
                    'description' => 'Our site amenities integrate seamlessly with PalmSHIELD screening systems for a unified architectural appearance.',
                    'button_text' => 'Request Consultation →',
                    'button_url' => '#',
                ),
            ),
            'gates' => array(
                'access_control' => array(
                    array('title' => 'Complete Slide Gate Packages', 'description' => 'Turnkey automated gate systems with operators', 'url' => '#'),
                    array('title' => 'Single Track Systems', 'description' => 'Standard cantilever slide gates', 'url' => '#'),
                    array('title' => 'Double Track Systems', 'description' => 'Bi-parting slide gate configurations', 'url' => '#'),
                    array('title' => 'Gate Automation Kits', 'description' => 'Operators, photo eyes, keypads & more', 'url' => '#'),
                ),
                'access_control_view_all' => array('title' => 'View Access Control+', 'url' => '#'),
                'architectural' => array(
                    array('title' => 'Single Slide Gates', 'description' => 'Industrial strength, architectural flair', 'url' => '#'),
                    array('title' => 'Swing Gates', 'description' => 'Single & double leaf configurations', 'url' => '#'),
                    array('title' => 'Man Gates', 'description' => 'Personnel entry points', 'url' => '#'),
                    array('title' => 'Swing Man Gates', 'description' => 'Walk gate with swing operation', 'url' => '#'),
                ),
                'architectural_view_all' => array('title' => 'View Architectural Gates', 'url' => '#'),
                'pedestrian' => array(
                    array('title' => 'Wire Mesh Systems', 'description' => 'Full gate & surround packages', 'url' => '#'),
                    array('title' => 'Picket Solid Systems', 'description' => 'Privacy-focused pedestrian entry', 'url' => '#'),
                    array('title' => 'Ornamental Systems', 'description' => 'Decorative high-traffic access', 'url' => '#'),
                    array('title' => 'Bar Grating Systems', 'description' => 'Industrial-grade pedestrian gates', 'url' => '#'),
                ),
                'pedestrian_view_all' => array('title' => 'View Pedestrian Gates', 'url' => '#'),
                'hardware' => array(
                    array('title' => 'Gate Operators', 'url' => '#'),
                    array('title' => 'Hinges & Latches', 'url' => '#'),
                    array('title' => 'Access Controls', 'url' => '#'),
                    array('title' => 'Safety Devices', 'url' => '#'),
                ),
                'featured' => array(
                    'title' => 'Architect Resources',
                    'description' => 'CAD drawings, specs & BIM models for chain link and ornamental gate systems.',
                    'button_text' => 'Download Resources →',
                    'button_url' => '#',
                ),
            ),
            'resources' => array(
                'technical' => array(
                    array('title' => 'CAD Drawings', 'description' => 'DWG & PDF downloads', 'url' => '#'),
                    array('title' => 'Specifications', 'description' => 'CSI 3-part specs', 'url' => '#'),
                    array('title' => 'BIM Models', 'description' => 'Revit families', 'url' => '#'),
                    array('title' => 'Installation Guides', 'description' => 'Step-by-step instructions', 'url' => '#'),
                ),
                'sales' => array(
                    array('title' => 'Product Brochures', 'description' => 'Downloadable PDFs', 'url' => '#'),
                    array('title' => 'Color Charts', 'description' => 'Standard & custom colors', 'url' => '#'),
                    array('title' => 'Project Gallery', 'description' => 'Completed installations', 'url' => '#'),
                    array('title' => 'Capabilities Statement', 'description' => 'Custom Metal Solutions', 'url' => '#'),
                ),
                'support' => array(
                    array('title' => 'FAQs', 'description' => 'Common questions answered', 'url' => '#'),
                    array('title' => 'Warranty Information', 'description' => 'Coverage details', 'url' => '#'),
                    array('title' => 'Find a Rep', 'description' => 'Local representation', 'url' => '#'),
                    array('title' => 'Contact Us', 'description' => 'Get in touch', 'url' => '#'),
                ),
                'featured' => array(
                    'title' => 'Request a Quote',
                    'description' => 'Ready to start your project? Get a custom quote from our team with specifications tailored to your requirements.',
                    'button_text' => 'Get a Quote →',
                    'button_url' => '#',
                ),
            ),
            'cta_menu' => array(
                'careers' => array(
                    'title' => 'Careers',
                    'url' => '#',
                ),
                'dealer_network' => array(
                    'title' => 'Join Our Dealer Network',
                    'url' => '#',
                ),
                'search' => array(
                    'title' => 'Search',
                    'url' => '#',
                    'is_search' => true,
                ),
            ),
        );
    }
}

// Initialize the plugin
function psmm_init() {
    return PalmSHIELD_Mega_Menu::get_instance();
}
add_action('plugins_loaded', 'psmm_init');

// Activation hook
register_activation_hook(__FILE__, 'psmm_activate');
function psmm_activate() {
    $plugin = PalmSHIELD_Mega_Menu::get_instance();
    if (!get_option('psmm_menu_settings')) {
        update_option('psmm_menu_settings', $plugin->get_default_settings());
    }
}
