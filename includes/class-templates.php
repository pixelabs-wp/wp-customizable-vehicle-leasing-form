<?php
/**
 * Handle template overrides for the vehicle post type
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Leasing_Form_Templates {

    /**
     * Constructor
     */
    public function __construct() {
        // Filter to modify single vehicle template
        add_filter('single_template', array($this, 'vehicle_single_template'));
        
        // Filter to modify archive template
        add_filter('archive_template', array($this, 'vehicle_archive_template'));
        
        // Add shortcode support to the_content for vehicle post type
        add_filter('the_content', array($this, 'append_leasing_form_to_content'));
    }

    /**
     * Override the single template for vehicles
     */
    public function vehicle_single_template($single_template) {
        global $post;
        
        if ($post->post_type == 'vehicle') {
            // Check if a template exists in the theme
            $theme_template = locate_template('single-vehicle.php');
            
            if ($theme_template) {
                // Use the theme's template if it exists
                return $theme_template;
            } else {
                // Use the plugin's template
                $plugin_template = LEASING_FORM_PATH . 'templates/single-vehicle.php';
                
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
        }
        
        return $single_template;
    }
    
    /**
     * Override the archive template for vehicles
     */
    public function vehicle_archive_template($archive_template) {
        if (is_post_type_archive('vehicle') || is_tax('vehicle_category')) {
            // Check if a template exists in the theme
            $theme_template = locate_template(array('archive-vehicle.php', 'taxonomy-vehicle_category.php'));
            
            if ($theme_template) {
                // Use the theme's template if it exists
                return $theme_template;
            } else {
                // Use the plugin's template
                $plugin_template = LEASING_FORM_PATH . 'templates/archive-vehicle.php';
                
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
        }
        
        return $archive_template;
    }
    
    /**
     * Append the leasing form to the vehicle content
     */
    public function append_leasing_form_to_content($content) {
        // Only on single vehicle pages
        if (is_singular('vehicle') && in_the_loop() && is_main_query()) {
            global $post;
            
            // Enqueue the required assets
            wp_enqueue_style('leasing-form-styles');
            wp_enqueue_script('leasing-form-script');
            
            // Pass vehicle data to JavaScript
            wp_localize_script('leasing-form-script', 'leasingFormData', $this->get_vehicle_data($post->ID));
            
            // Get the form HTML
            ob_start();
            include LEASING_FORM_PATH . 'templates/leasing-form.php';
            $form_html = ob_get_clean();
            
            // Create a responsive container with image on left, form on right
            $output = '<div class="leasing-vehicle-container">';
            $output .= '<div class="leasing-vehicle-image">';
            
            if (has_post_thumbnail()) {
                $output .= get_the_post_thumbnail(null, 'large');
            } else {
                $output .= '<div class="no-image-placeholder">No Image Available</div>';
            }
            
            $output .= '</div>';
            $output .= '<div class="leasing-vehicle-form">';
            $output .= $form_html;
            $output .= '</div>';
            $output .= '</div>';
            
            // Append to content
            $content .= $output;
        }
        
        return $content;
    }
    
    /**
     * Get vehicle data for the form
     */
    private function get_vehicle_data($vehicle_id) {
        $subscription_options = get_post_meta($vehicle_id, '_subscription_options', true);
        $insurance_options = get_post_meta($vehicle_id, '_insurance_options', true);
        $mileage_options = get_post_meta($vehicle_id, '_mileage_options', true);
        $base_price = get_post_meta($vehicle_id, '_base_price', true);
        $whatsapp_number = get_post_meta($vehicle_id, '_whatsapp_number', true);
        
        if (empty($base_price)) {
            $base_price = 299; // Default price if not set
        }
        
        // Sanitize arrays before passing to JS
        $sanitized_subscription_options = array();
        if (!empty($subscription_options) && is_array($subscription_options)) {
            foreach ($subscription_options as $option) {
                $sanitized_subscription_options[] = array(
                    'months' => absint($option['months'] ?? 0),
                    'base_price' => (float) ($option['base_price'] ?? 0),
                    'price_adjustment' => (float) ($option['price_adjustment'] ?? 0),
                    'is_selected' => (bool) ($option['is_selected'] ?? false),
                    'is_recommended' => (bool) ($option['is_recommended'] ?? false),
                    'description' => esc_js($option['description'] ?? ''),
                );
            }
        }
        
        $sanitized_insurance_options = array();
        if (!empty($insurance_options) && is_array($insurance_options)) {
            foreach ($insurance_options as $option) {
                $sanitized_insurance_options[] = array(
                    'name' => esc_js($option['name'] ?? ''),
                    'price_adjustment' => (float) ($option['price_adjustment'] ?? 0),
                    'is_selected' => (bool) ($option['is_selected'] ?? false),
                    'is_recommended' => (bool) ($option['is_recommended'] ?? false),
                    'description' => esc_js($option['description'] ?? ''),
                );
            }
        }
        
        $sanitized_mileage_options = array();
        if (!empty($mileage_options) && is_array($mileage_options)) {
            foreach ($mileage_options as $option) {
                $sanitized_mileage_options[] = array(
                    'miles' => absint($option['miles'] ?? 0),
                    'price_adjustment' => (float) ($option['price_adjustment'] ?? 0),
                    'is_selected' => (bool) ($option['is_selected'] ?? false),
                    'is_recommended' => (bool) ($option['is_recommended'] ?? false),
                    'description' => esc_js($option['description'] ?? ''),
                );
            }
        }
        
        return array(
            'vehicle_id' => absint($vehicle_id),
            'vehicle_title' => esc_js(get_the_title($vehicle_id)),
            'base_price' => floatval($base_price),
            'subscription_options' => $sanitized_subscription_options,
            'insurance_options' => $sanitized_insurance_options,
            'mileage_options' => $sanitized_mileage_options,
            'watching_count' => absint(get_post_meta($vehicle_id, '_watching_count', true) ?: rand(5, 15)),
            'whatsapp_number' => esc_js(!empty($whatsapp_number) ? $whatsapp_number : '923105054025') // Default number if not set
        );
    }
} 