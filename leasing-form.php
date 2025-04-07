<?php

/**
 * Plugin Name: WP Customizable Vehicle Leasing Form
 * Plugin URI: https://github.com/pixelabs-wp/wp-customizable-vehicle-leasing-form
 * Description: A plugin to create and manage vehicle leasing options with a customizable form. Use the shortcode [leasing_form id="123"] or simply [leasing_form] within a vehicle post.
 * Version: 1.0.0
 * Author: Pixelabs | Ali Shahmir Khan
 * Author URI: https://github.com/pixelabs-wp
 * Text Domain: leasing-form
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('LEASING_FORM_PATH', plugin_dir_path(__FILE__));
define('LEASING_FORM_URL', plugin_dir_url(__FILE__));
define('LEASING_FORM_VERSION', '1.0.0');

// Include required files
require_once LEASING_FORM_PATH . 'includes/class-post-types.php';
require_once LEASING_FORM_PATH . 'includes/class-meta-boxes.php';
require_once LEASING_FORM_PATH . 'includes/class-templates.php';

/**
 * Main plugin class
 */
class Leasing_Form_Plugin {
    private static $instance = null;

    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Initialize post types
        new Leasing_Form_Post_Types();
        
        // Initialize meta boxes
        new Leasing_Form_Meta_Boxes();
        
        // Initialize template loader
        new Leasing_Form_Templates();
        
        // Register assets
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        
        // Register admin assets
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));
        
        // Add shortcode
        add_shortcode('leasing_form', array($this, 'leasing_form_shortcode'));
    }
    
    /**
     * Register frontend assets
     */
    public function register_assets() {
        wp_register_style(
            'leasing-form-styles',
            LEASING_FORM_URL . 'assets/css/subscription-form.css',
            array(),
            LEASING_FORM_VERSION
        );
        
        wp_register_script(
            'leasing-form-script',
            LEASING_FORM_URL . 'assets/js/subscription-form.js',
            array('jquery'),
            LEASING_FORM_VERSION,
            true
        );
    }
    
    /**
     * Register admin assets
     */
    public function register_admin_assets($hook) {
        // Only load on our custom post type edit screen
        global $post;
        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if (isset($post) && $post->post_type === 'vehicle') {
                wp_enqueue_style(
                    'leasing-form-admin-styles',
                    LEASING_FORM_URL . 'assets/css/admin.css',
                    array(),
                    LEASING_FORM_VERSION
                );
                
                wp_enqueue_script(
                    'leasing-form-admin-script',
                    LEASING_FORM_URL . 'assets/js/admin.js',
                    array('jquery', 'jquery-ui-sortable'),
                    LEASING_FORM_VERSION,
                    true
                );
            }
        }
    }
    
    /**
     * Shortcode function to display the leasing form
     */
    public function leasing_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);
        
        $vehicle_id = intval($atts['id']);
        
        // If no ID is specified, try to get the current post ID if it's a vehicle
        if (empty($vehicle_id)) {
            // Check if we're in a vehicle post
            global $post;
            if (is_singular('vehicle') && isset($post) && $post->post_type === 'vehicle') {
                $vehicle_id = $post->ID;
            } else {
                // Check if we're in the loop or can get the current post
                $current_post = get_post();
                if ($current_post && $current_post->post_type === 'vehicle') {
                    $vehicle_id = $current_post->ID;
                }
            }
            
            // If we still don't have a vehicle ID, show an error
            if (empty($vehicle_id)) {
                return '<p>' . __('No vehicle ID specified. Please use the id attribute or use this shortcode within a vehicle post.', 'leasing-form') . '</p>';
            }
        }
        
        wp_enqueue_style('leasing-form-styles');
        wp_enqueue_script('leasing-form-script');
        
        // Pass vehicle data to JavaScript
        wp_localize_script('leasing-form-script', 'leasingFormData', $this->get_vehicle_data($vehicle_id));
        
        ob_start();
        include LEASING_FORM_PATH . 'templates/leasing-form.php';
        return ob_get_clean();
    }
    
    /**
     * Get vehicle data for the form
     */
    private function get_vehicle_data($vehicle_id) {
        $subscription_options = get_post_meta($vehicle_id, '_subscription_options', true);
        $insurance_options = get_post_meta($vehicle_id, '_insurance_options', true);
        $mileage_options = get_post_meta($vehicle_id, '_mileage_options', true);
        
        return array(
            'vehicle_id' => $vehicle_id,
            'vehicle_title' => get_the_title($vehicle_id),
            'subscription_options' => !empty($subscription_options) ? $subscription_options : array(),
            'insurance_options' => !empty($insurance_options) ? $insurance_options : array(),
            'mileage_options' => !empty($mileage_options) ? $mileage_options : array(),
            'watching_count' => get_post_meta($vehicle_id, '_watching_count', true) ?: rand(5, 15)
        );
    }
}

// Initialize the plugin
function leasing_form_init() {
    Leasing_Form_Plugin::get_instance();
}
add_action('plugins_loaded', 'leasing_form_init');

// Activation hook
register_activation_hook(__FILE__, 'leasing_form_activate');
function leasing_form_activate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'leasing_form_deactivate');
function leasing_form_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
} 