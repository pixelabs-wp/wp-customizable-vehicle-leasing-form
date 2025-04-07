<?php
/**
 * Handle meta boxes for the vehicle post type
 */
class Leasing_Form_Meta_Boxes {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_vehicle', array($this, 'save_meta_box_data'));
    }

    /**
     * Add meta boxes to the vehicle post type
     */
    public function add_meta_boxes() {
        add_meta_box(
            'vehicle_leasing_options',
            __('Leasing Options', 'leasing-form'),
            array($this, 'render_leasing_options_meta_box'),
            'vehicle',
            'normal',
            'high'
        );
        
        add_meta_box(
            'vehicle_pricing',
            __('Base Pricing', 'leasing-form'),
            array($this, 'render_pricing_meta_box'),
            'vehicle',
            'side',
            'default'
        );
    }
    
    /**
     * Render the leasing options meta box
     */
    public function render_leasing_options_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('vehicle_leasing_options_nonce', 'vehicle_leasing_options_nonce');
        
        // Get saved values
        $subscription_options = get_post_meta($post->ID, '_subscription_options', true);
        $insurance_options = get_post_meta($post->ID, '_insurance_options', true);
        $mileage_options = get_post_meta($post->ID, '_mileage_options', true);
        
        // If no options exist, set defaults
        if (empty($subscription_options)) {
            $subscription_options = $this->get_default_subscription_options();
        }
        
        if (empty($insurance_options)) {
            $insurance_options = $this->get_default_insurance_options();
        }
        
        if (empty($mileage_options)) {
            $mileage_options = $this->get_default_mileage_options();
        }
        
        // Get current watching count
        $watching_count = get_post_meta($post->ID, '_watching_count', true);
        if (!$watching_count) {
            $watching_count = rand(5, 15);
        }
        
        // Start output
        ?>
        <div class="leasing-options-container">
            <div class="leasing-options-section">
                <h3><?php _e('Subscription Length Options', 'leasing-form'); ?></h3>
                <p class="description"><?php _e('Define the subscription length options available for this vehicle.', 'leasing-form'); ?></p>
                
                <div class="leasing-options-list" id="subscription-options-list">
                    <?php 
                    if (!empty($subscription_options) && is_array($subscription_options)) {
                        foreach ($subscription_options as $index => $option) {
                            $this->render_subscription_option($index, $option);
                        }
                    }
                    ?>
                </div>
                
                <button type="button" class="button add-subscription-option">
                    <?php _e('Add Subscription Option', 'leasing-form'); ?>
                </button>
            </div>
            
            <div class="leasing-options-section">
                <h3><?php _e('Insurance Options', 'leasing-form'); ?></h3>
                <p class="description"><?php _e('Define the insurance options available for this vehicle.', 'leasing-form'); ?></p>
                
                <div class="leasing-options-list" id="insurance-options-list">
                    <?php 
                    if (!empty($insurance_options) && is_array($insurance_options)) {
                        foreach ($insurance_options as $index => $option) {
                            $this->render_insurance_option($index, $option);
                        }
                    }
                    ?>
                </div>
                
                <button type="button" class="button add-insurance-option">
                    <?php _e('Add Insurance Option', 'leasing-form'); ?>
                </button>
            </div>
            
            <div class="leasing-options-section">
                <h3><?php _e('Monthly Mileage Options', 'leasing-form'); ?></h3>
                <p class="description"><?php _e('Define the monthly mileage options available for this vehicle.', 'leasing-form'); ?></p>
                
                <div class="leasing-options-list" id="mileage-options-list">
                    <?php 
                    if (!empty($mileage_options) && is_array($mileage_options)) {
                        foreach ($mileage_options as $index => $option) {
                            $this->render_mileage_option($index, $option);
                        }
                    }
                    ?>
                </div>
                
                <button type="button" class="button add-mileage-option">
                    <?php _e('Add Mileage Option', 'leasing-form'); ?>
                </button>
            </div>
            
            <div class="leasing-options-section">
                <h3><?php _e('Other Settings', 'leasing-form'); ?></h3>
                
                <p>
                    <label for="watching_count"><?php _e('Number of people watching this vehicle:', 'leasing-form'); ?></label>
                    <input type="number" id="watching_count" name="watching_count" value="<?php echo esc_attr($watching_count); ?>" min="0" max="100" />
                </p>
                
                <p>
                    <label for="whatsapp_number"><?php _e('WhatsApp Contact Number:', 'leasing-form'); ?></label>
                    <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo esc_attr(get_post_meta($post->ID, '_whatsapp_number', true)); ?>" class="widefat" placeholder="923105054025" />
                    <span class="description"><?php _e('Enter the WhatsApp number for inquiries (international format without + symbol, e.g. 923105054025)', 'leasing-form'); ?></span>
                </p>
            </div>
        </div>
        
        <?php
        // Templates for new options
        $this->render_option_templates();
    }
    
    /**
     * Render the pricing meta box
     */
    public function render_pricing_meta_box($post) {
        // Get base price
        $base_price = get_post_meta($post->ID, '_base_price', true);
        ?>
        <p>
            <label for="base_price"><?php _e('Base Monthly Price ($):', 'leasing-form'); ?></label>
            <input type="number" id="base_price" name="base_price" value="<?php echo esc_attr($base_price); ?>" class="widefat" step="0.01" min="0" />
            <span class="description"><?php _e('The starting monthly price for this vehicle leasing.', 'leasing-form'); ?></span>
        </p>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_box_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['vehicle_leasing_options_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['vehicle_leasing_options_nonce'], 'vehicle_leasing_options_nonce')) {
            return;
        }
        
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save base price
        if (isset($_POST['base_price'])) {
            update_post_meta($post_id, '_base_price', sanitize_text_field($_POST['base_price']));
        }
        
        // Save watching count
        if (isset($_POST['watching_count'])) {
            update_post_meta($post_id, '_watching_count', intval($_POST['watching_count']));
        }
        
        // Save WhatsApp number
        if (isset($_POST['whatsapp_number'])) {
            update_post_meta($post_id, '_whatsapp_number', sanitize_text_field($_POST['whatsapp_number']));
        }
        
        // Save subscription options
        if (isset($_POST['subscription_options']) && is_array($_POST['subscription_options'])) {
            $subscription_options = array();
            
            foreach ($_POST['subscription_options'] as $option) {
                if (!isset($option['months']) || empty($option['months'])) {
                    continue;
                }
                
                $subscription_options[] = array(
                    'months' => intval($option['months']),
                    'base_price' => isset($option['base_price']) ? floatval($option['base_price']) : 0,
                    'price_adjustment' => isset($option['price_adjustment']) ? floatval($option['price_adjustment']) : 0,
                    'is_selected' => isset($option['is_selected']) && $option['is_selected'] === 'yes',
                    'is_recommended' => isset($option['is_recommended']) && $option['is_recommended'] === 'yes',
                    'description' => isset($option['description']) ? sanitize_text_field($option['description']) : '',
                );
            }
            
            update_post_meta($post_id, '_subscription_options', $subscription_options);
        }
        
        // Save insurance options
        if (isset($_POST['insurance_options']) && is_array($_POST['insurance_options'])) {
            $insurance_options = array();
            
            foreach ($_POST['insurance_options'] as $option) {
                if (!isset($option['name']) || empty($option['name'])) {
                    continue;
                }
                
                $insurance_options[] = array(
                    'name' => sanitize_text_field($option['name']),
                    'price_adjustment' => isset($option['price_adjustment']) ? floatval($option['price_adjustment']) : 0,
                    'is_selected' => isset($option['is_selected']) && $option['is_selected'] === 'yes',
                    'is_recommended' => isset($option['is_recommended']) && $option['is_recommended'] === 'yes',
                    'description' => isset($option['description']) ? sanitize_text_field($option['description']) : '',
                );
            }
            
            update_post_meta($post_id, '_insurance_options', $insurance_options);
        }
        
        // Save mileage options
        if (isset($_POST['mileage_options']) && is_array($_POST['mileage_options'])) {
            $mileage_options = array();
            
            foreach ($_POST['mileage_options'] as $option) {
                if (!isset($option['miles']) || empty($option['miles'])) {
                    continue;
                }
                
                $mileage_options[] = array(
                    'miles' => intval($option['miles']),
                    'price_adjustment' => isset($option['price_adjustment']) ? floatval($option['price_adjustment']) : 0,
                    'is_selected' => isset($option['is_selected']) && $option['is_selected'] === 'yes',
                    'is_recommended' => isset($option['is_recommended']) && $option['is_recommended'] === 'yes',
                    'description' => isset($option['description']) ? sanitize_text_field($option['description']) : '',
                );
            }
            
            update_post_meta($post_id, '_mileage_options', $mileage_options);
        }
    }
    
    /**
     * Render a subscription option form
     */
    private function render_subscription_option($index, $option) {
        $months = isset($option['months']) ? $option['months'] : '';
        $base_price = isset($option['base_price']) ? $option['base_price'] : '';
        $price_adjustment = isset($option['price_adjustment']) ? $option['price_adjustment'] : 0;
        $is_selected = isset($option['is_selected']) && $option['is_selected'] ? 'yes' : 'no';
        $is_recommended = isset($option['is_recommended']) && $option['is_recommended'] ? 'yes' : 'no';
        $description = isset($option['description']) ? $option['description'] : '';
        
        ?>
        <div class="leasing-option-item">
            <h4>
                <?php _e('Subscription Option', 'leasing-form'); ?>
                <span class="option-number">#<?php echo ($index + 1); ?></span>
                <button type="button" class="button-link remove-option"><?php _e('Remove', 'leasing-form'); ?></button>
            </h4>
            
            <div class="option-fields">
                <p>
                    <label><?php _e('Months:', 'leasing-form'); ?></label>
                    <input type="number" name="subscription_options[<?php echo $index; ?>][months]" value="<?php echo esc_attr($months); ?>" required min="1" max="60" />
                </p>
                
                <p>
                    <label><?php _e('Base Price ($):', 'leasing-form'); ?></label>
                    <input type="number" name="subscription_options[<?php echo $index; ?>][base_price]" value="<?php echo esc_attr($base_price); ?>" step="0.01" min="0" />
                    <span class="description"><?php _e('The base monthly price for this subscription length', 'leasing-form'); ?></span>
                </p>
                
                <p>
                    <label><?php _e('Price Adjustment ($):', 'leasing-form'); ?></label>
                    <input type="number" name="subscription_options[<?php echo $index; ?>][price_adjustment]" value="<?php echo esc_attr($price_adjustment); ?>" step="0.01" />
                    <span class="description"><?php _e('Additional amount to add to the base price for this option', 'leasing-form'); ?></span>
                </p>
                
                <p>
                    <label><?php _e('Selected by Default:', 'leasing-form'); ?></label>
                    <select name="subscription_options[<?php echo $index; ?>][is_selected]">
                        <option value="no" <?php selected($is_selected, 'no'); ?>><?php _e('No', 'leasing-form'); ?></option>
                        <option value="yes" <?php selected($is_selected, 'yes'); ?>><?php _e('Yes', 'leasing-form'); ?></option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Recommended Option:', 'leasing-form'); ?></label>
                    <select name="subscription_options[<?php echo $index; ?>][is_recommended]">
                        <option value="no" <?php selected($is_recommended, 'no'); ?>><?php _e('No', 'leasing-form'); ?></option>
                        <option value="yes" <?php selected($is_recommended, 'yes'); ?>><?php _e('Yes', 'leasing-form'); ?></option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Description:', 'leasing-form'); ?></label>
                    <input type="text" name="subscription_options[<?php echo $index; ?>][description]" value="<?php echo esc_attr($description); ?>" class="widefat" />
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render an insurance option form
     */
    private function render_insurance_option($index, $option) {
        $name = isset($option['name']) ? $option['name'] : '';
        $price_adjustment = isset($option['price_adjustment']) ? $option['price_adjustment'] : 0;
        $is_selected = isset($option['is_selected']) && $option['is_selected'] ? 'yes' : 'no';
        $is_recommended = isset($option['is_recommended']) && $option['is_recommended'] ? 'yes' : 'no';
        $description = isset($option['description']) ? $option['description'] : '';
        
        ?>
        <div class="leasing-option-item">
            <h4>
                <?php _e('Insurance Option', 'leasing-form'); ?>
                <span class="option-number">#<?php echo ($index + 1); ?></span>
                <button type="button" class="button-link remove-option"><?php _e('Remove', 'leasing-form'); ?></button>
            </h4>
            
            <div class="option-fields">
                <p>
                    <label><?php _e('Name:', 'leasing-form'); ?></label>
                    <input type="text" name="insurance_options[<?php echo $index; ?>][name]" value="<?php echo esc_attr($name); ?>" required class="widefat" />
                </p>
                
                <p>
                    <label><?php _e('Price Adjustment ($):', 'leasing-form'); ?></label>
                    <input type="number" name="insurance_options[<?php echo $index; ?>][price_adjustment]" value="<?php echo esc_attr($price_adjustment); ?>" step="0.01" />
                    <span class="description"><?php _e('Amount to add to the base price for this option', 'leasing-form'); ?></span>
                </p>
                
                <p>
                    <label><?php _e('Selected by Default:', 'leasing-form'); ?></label>
                    <select name="insurance_options[<?php echo $index; ?>][is_selected]">
                        <option value="no" <?php selected($is_selected, 'no'); ?>><?php _e('No', 'leasing-form'); ?></option>
                        <option value="yes" <?php selected($is_selected, 'yes'); ?>><?php _e('Yes', 'leasing-form'); ?></option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Recommended Option:', 'leasing-form'); ?></label>
                    <select name="insurance_options[<?php echo $index; ?>][is_recommended]">
                        <option value="no" <?php selected($is_recommended, 'no'); ?>><?php _e('No', 'leasing-form'); ?></option>
                        <option value="yes" <?php selected($is_recommended, 'yes'); ?>><?php _e('Yes', 'leasing-form'); ?></option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Description:', 'leasing-form'); ?></label>
                    <input type="text" name="insurance_options[<?php echo $index; ?>][description]" value="<?php echo esc_attr($description); ?>" class="widefat" />
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render a mileage option form
     */
    private function render_mileage_option($index, $option) {
        $miles = isset($option['miles']) ? $option['miles'] : '';
        $price_adjustment = isset($option['price_adjustment']) ? $option['price_adjustment'] : 0;
        $is_selected = isset($option['is_selected']) && $option['is_selected'] ? 'yes' : 'no';
        $is_recommended = isset($option['is_recommended']) && $option['is_recommended'] ? 'yes' : 'no';
        $description = isset($option['description']) ? $option['description'] : '';
        
        ?>
        <div class="leasing-option-item">
            <h4>
                <?php _e('Mileage Option', 'leasing-form'); ?>
                <span class="option-number">#<?php echo ($index + 1); ?></span>
                <button type="button" class="button-link remove-option"><?php _e('Remove', 'leasing-form'); ?></button>
            </h4>
            
            <div class="option-fields">
                <p>
                    <label><?php _e('Monthly Miles:', 'leasing-form'); ?></label>
                    <input type="number" name="mileage_options[<?php echo $index; ?>][miles]" value="<?php echo esc_attr($miles); ?>" required min="1" />
                </p>
                
                <p>
                    <label><?php _e('Price Adjustment ($):', 'leasing-form'); ?></label>
                    <input type="number" name="mileage_options[<?php echo $index; ?>][price_adjustment]" value="<?php echo esc_attr($price_adjustment); ?>" step="0.01" />
                    <span class="description"><?php _e('Amount to add to the base price for this option', 'leasing-form'); ?></span>
                </p>
                
                <p>
                    <label><?php _e('Selected by Default:', 'leasing-form'); ?></label>
                    <select name="mileage_options[<?php echo $index; ?>][is_selected]">
                        <option value="no" <?php selected($is_selected, 'no'); ?>><?php _e('No', 'leasing-form'); ?></option>
                        <option value="yes" <?php selected($is_selected, 'yes'); ?>><?php _e('Yes', 'leasing-form'); ?></option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Recommended Option:', 'leasing-form'); ?></label>
                    <select name="mileage_options[<?php echo $index; ?>][is_recommended]">
                        <option value="no" <?php selected($is_recommended, 'no'); ?>><?php _e('No', 'leasing-form'); ?></option>
                        <option value="yes" <?php selected($is_recommended, 'yes'); ?>><?php _e('Yes', 'leasing-form'); ?></option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Description:', 'leasing-form'); ?></label>
                    <input type="text" name="mileage_options[<?php echo $index; ?>][description]" value="<?php echo esc_attr($description); ?>" class="widefat" />
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render JavaScript templates for new options
     */
    private function render_option_templates() {
        ?>
        <script type="text/html" id="tmpl-subscription-option">
            <div class="leasing-option-item">
                <h4>
                    <?php _e('Subscription Option', 'leasing-form'); ?>
                    <span class="option-number">#{{data.index}}</span>
                    <button type="button" class="button-link remove-option"><?php _e('Remove', 'leasing-form'); ?></button>
                </h4>
                
                <div class="option-fields">
                    <p>
                        <label><?php _e('Months:', 'leasing-form'); ?></label>
                        <input type="number" name="subscription_options[{{data.index}}][months]" value="" required min="1" max="60" />
                    </p>
                    
                    <p>
                        <label><?php _e('Base Price ($):', 'leasing-form'); ?></label>
                        <input type="number" name="subscription_options[{{data.index}}][base_price]" value="0" step="0.01" min="0" />
                        <span class="description"><?php _e('The base monthly price for this subscription length', 'leasing-form'); ?></span>
                    </p>
                    
                    <p>
                        <label><?php _e('Price Adjustment ($):', 'leasing-form'); ?></label>
                        <input type="number" name="subscription_options[{{data.index}}][price_adjustment]" value="0" step="0.01" />
                        <span class="description"><?php _e('Additional amount to add to the base price for this option', 'leasing-form'); ?></span>
                    </p>
                    
                    <p>
                        <label><?php _e('Selected by Default:', 'leasing-form'); ?></label>
                        <select name="subscription_options[{{data.index}}][is_selected]">
                            <option value="no"><?php _e('No', 'leasing-form'); ?></option>
                            <option value="yes"><?php _e('Yes', 'leasing-form'); ?></option>
                        </select>
                    </p>
                    
                    <p>
                        <label><?php _e('Recommended Option:', 'leasing-form'); ?></label>
                        <select name="subscription_options[{{data.index}}][is_recommended]">
                            <option value="no"><?php _e('No', 'leasing-form'); ?></option>
                            <option value="yes"><?php _e('Yes', 'leasing-form'); ?></option>
                        </select>
                    </p>
                    
                    <p>
                        <label><?php _e('Description:', 'leasing-form'); ?></label>
                        <input type="text" name="subscription_options[{{data.index}}][description]" value="" class="widefat" />
                    </p>
                </div>
            </div>
        </script>
        
        <script type="text/html" id="tmpl-insurance-option">
            <div class="leasing-option-item">
                <h4>
                    <?php _e('Insurance Option', 'leasing-form'); ?>
                    <span class="option-number">#{{data.index}}</span>
                    <button type="button" class="button-link remove-option"><?php _e('Remove', 'leasing-form'); ?></button>
                </h4>
                
                <div class="option-fields">
                    <p>
                        <label><?php _e('Name:', 'leasing-form'); ?></label>
                        <input type="text" name="insurance_options[{{data.index}}][name]" value="" required class="widefat" />
                    </p>
                    
                    <p>
                        <label><?php _e('Price Adjustment ($):', 'leasing-form'); ?></label>
                        <input type="number" name="insurance_options[{{data.index}}][price_adjustment]" value="0" step="0.01" />
                        <span class="description"><?php _e('Amount to add to the base price for this option', 'leasing-form'); ?></span>
                    </p>
                    
                    <p>
                        <label><?php _e('Selected by Default:', 'leasing-form'); ?></label>
                        <select name="insurance_options[{{data.index}}][is_selected]">
                            <option value="no"><?php _e('No', 'leasing-form'); ?></option>
                            <option value="yes"><?php _e('Yes', 'leasing-form'); ?></option>
                        </select>
                    </p>
                    
                    <p>
                        <label><?php _e('Recommended Option:', 'leasing-form'); ?></label>
                        <select name="insurance_options[{{data.index}}][is_recommended]">
                            <option value="no"><?php _e('No', 'leasing-form'); ?></option>
                            <option value="yes"><?php _e('Yes', 'leasing-form'); ?></option>
                        </select>
                    </p>
                    
                    <p>
                        <label><?php _e('Description:', 'leasing-form'); ?></label>
                        <input type="text" name="insurance_options[{{data.index}}][description]" value="" class="widefat" />
                    </p>
                </div>
            </div>
        </script>
        
        <script type="text/html" id="tmpl-mileage-option">
            <div class="leasing-option-item">
                <h4>
                    <?php _e('Mileage Option', 'leasing-form'); ?>
                    <span class="option-number">#{{data.index}}</span>
                    <button type="button" class="button-link remove-option"><?php _e('Remove', 'leasing-form'); ?></button>
                </h4>
                
                <div class="option-fields">
                    <p>
                        <label><?php _e('Monthly Miles:', 'leasing-form'); ?></label>
                        <input type="number" name="mileage_options[{{data.index}}][miles]" value="" required min="1" />
                    </p>
                    
                    <p>
                        <label><?php _e('Price Adjustment ($):', 'leasing-form'); ?></label>
                        <input type="number" name="mileage_options[{{data.index}}][price_adjustment]" value="0" step="0.01" />
                        <span class="description"><?php _e('Amount to add to the base price for this option', 'leasing-form'); ?></span>
                    </p>
                    
                    <p>
                        <label><?php _e('Selected by Default:', 'leasing-form'); ?></label>
                        <select name="mileage_options[{{data.index}}][is_selected]">
                            <option value="no"><?php _e('No', 'leasing-form'); ?></option>
                            <option value="yes"><?php _e('Yes', 'leasing-form'); ?></option>
                        </select>
                    </p>
                    
                    <p>
                        <label><?php _e('Recommended Option:', 'leasing-form'); ?></label>
                        <select name="mileage_options[{{data.index}}][is_recommended]">
                            <option value="no"><?php _e('No', 'leasing-form'); ?></option>
                            <option value="yes"><?php _e('Yes', 'leasing-form'); ?></option>
                        </select>
                    </p>
                    
                    <p>
                        <label><?php _e('Description:', 'leasing-form'); ?></label>
                        <input type="text" name="mileage_options[{{data.index}}][description]" value="" class="widefat" />
                    </p>
                </div>
            </div>
        </script>
        <?php
    }
    
    /**
     * Get default subscription options
     */
    private function get_default_subscription_options() {
        return array(
            array(
                'months' => 3,
                'base_price' => 1995,
                'price_adjustment' => 50,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'Short term option'
            ),
            array(
                'months' => 6,
                'base_price' => 1945,
                'price_adjustment' => 25,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'Medium term option'
            ),
            array(
                'months' => 9,
                'base_price' => 1895,
                'price_adjustment' => 0,
                'is_selected' => true,
                'is_recommended' => true,
                'description' => 'Standard option'
            ),
            array(
                'months' => 12,
                'base_price' => 1845,
                'price_adjustment' => -25,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'Long term option'
            ),
        );
    }
    
    /**
     * Get default insurance options
     */
    private function get_default_insurance_options() {
        return array(
            array(
                'name' => 'Basic',
                'price_adjustment' => 0,
                'is_selected' => true,
                'is_recommended' => false,
                'description' => 'Third party coverage'
            ),
            array(
                'name' => 'Comprehensive',
                'price_adjustment' => 45,
                'is_selected' => false,
                'is_recommended' => true,
                'description' => 'Full coverage with higher deductible'
            ),
            array(
                'name' => 'Premium',
                'price_adjustment' => 75,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'Full coverage with low deductible'
            ),
        );
    }
    
    /**
     * Get default mileage options
     */
    private function get_default_mileage_options() {
        return array(
            array(
                'miles' => 500,
                'price_adjustment' => -25,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'Low mileage plan'
            ),
            array(
                'miles' => 1000,
                'price_adjustment' => 0,
                'is_selected' => true,
                'is_recommended' => true,
                'description' => 'Standard mileage plan'
            ),
            array(
                'miles' => 1500,
                'price_adjustment' => 35,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'High mileage plan'
            ),
            array(
                'miles' => 2000,
                'price_adjustment' => 65,
                'is_selected' => false,
                'is_recommended' => false,
                'description' => 'Unlimited mileage plan'
            ),
        );
    }
} 