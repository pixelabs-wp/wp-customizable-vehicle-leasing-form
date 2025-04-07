<?php
/**
 * Register custom post types
 */
class Leasing_Form_Post_Types {

    /**
     * Constructor
     */
    public function __construct() {
        // Register custom post type
        add_action('init', array($this, 'register_post_types'));
        
        // Add custom columns to admin
        add_filter('manage_vehicle_posts_columns', array($this, 'add_custom_columns'));
        add_action('manage_vehicle_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }

    /**
     * Register the 'vehicle' custom post type
     */
    public function register_post_types() {
        $labels = array(
            'name'               => _x('Vehicles', 'post type general name', 'leasing-form'),
            'singular_name'      => _x('Vehicle', 'post type singular name', 'leasing-form'),
            'menu_name'          => _x('Vehicles', 'admin menu', 'leasing-form'),
            'name_admin_bar'     => _x('Vehicle', 'add new on admin bar', 'leasing-form'),
            'add_new'            => _x('Add New', 'vehicle', 'leasing-form'),
            'add_new_item'       => __('Add New Vehicle', 'leasing-form'),
            'new_item'           => __('New Vehicle', 'leasing-form'),
            'edit_item'          => __('Edit Vehicle', 'leasing-form'),
            'view_item'          => __('View Vehicle', 'leasing-form'),
            'all_items'          => __('All Vehicles', 'leasing-form'),
            'search_items'       => __('Search Vehicles', 'leasing-form'),
            'parent_item_colon'  => __('Parent Vehicles:', 'leasing-form'),
            'not_found'          => __('No vehicles found.', 'leasing-form'),
            'not_found_in_trash' => __('No vehicles found in Trash.', 'leasing-form')
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __('Vehicles available for leasing', 'leasing-form'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'vehicle'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-car',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        );

        register_post_type('vehicle', $args);
        
        // Register taxonomy for vehicle categories
        $category_labels = array(
            'name'              => _x('Vehicle Categories', 'taxonomy general name', 'leasing-form'),
            'singular_name'     => _x('Vehicle Category', 'taxonomy singular name', 'leasing-form'),
            'search_items'      => __('Search Vehicle Categories', 'leasing-form'),
            'all_items'         => __('All Vehicle Categories', 'leasing-form'),
            'parent_item'       => __('Parent Vehicle Category', 'leasing-form'),
            'parent_item_colon' => __('Parent Vehicle Category:', 'leasing-form'),
            'edit_item'         => __('Edit Vehicle Category', 'leasing-form'),
            'update_item'       => __('Update Vehicle Category', 'leasing-form'),
            'add_new_item'      => __('Add New Vehicle Category', 'leasing-form'),
            'new_item_name'     => __('New Vehicle Category Name', 'leasing-form'),
            'menu_name'         => __('Categories', 'leasing-form'),
        );

        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'vehicle-category'),
        );

        register_taxonomy('vehicle_category', array('vehicle'), $category_args);
    }
    
    /**
     * Add custom columns to the vehicle post type admin
     */
    public function add_custom_columns($columns) {
        $new_columns = array();
        
        // Insert columns after title
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'title') {
                $new_columns['vehicle_image'] = __('Vehicle Image', 'leasing-form');
                $new_columns['subscription_plans'] = __('Subscription Plans', 'leasing-form');
                $new_columns['shortcode'] = __('Shortcode', 'leasing-form');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Add content to custom columns
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'vehicle_image':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(60, 60));
                } else {
                    echo '<div style="width:60px;height:60px;background:#f0f0f0;text-align:center;line-height:60px;">No Image</div>';
                }
                break;
                
            case 'subscription_plans':
                $subscription_options = get_post_meta($post_id, '_subscription_options', true);
                if (!empty($subscription_options) && is_array($subscription_options)) {
                    $count = count($subscription_options);
                    echo sprintf(_n('%d plan', '%d plans', $count, 'leasing-form'), $count);
                } else {
                    echo '—';
                }
                break;
                
            case 'shortcode':
                echo '<code>[leasing_form id="' . $post_id . '"]</code>';
                echo '<br><small>' . __('Or simply use <code>[leasing_form]</code> when inside this vehicle post', 'leasing-form') . '</small>';
                break;
        }
    }
} 