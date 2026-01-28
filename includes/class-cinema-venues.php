<?php
/**
 * Venues Custom Post Type
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Cinema_Venues {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_cinema_venue', array($this, 'save_meta_data'));
        add_filter('manage_cinema_venue_posts_columns', array($this, 'custom_columns'));
        add_action('manage_cinema_venue_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }
    
    /**
     * Register Venues Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Venues', 'Post Type General Name', 'wp-cinema-manager'),
            'singular_name'         => _x('Venue', 'Post Type Singular Name', 'wp-cinema-manager'),
            'menu_name'             => __('Venues', 'wp-cinema-manager'),
            'name_admin_bar'        => __('Venue', 'wp-cinema-manager'),
            'archives'              => __('Venue Archives', 'wp-cinema-manager'),
            'attributes'            => __('Venue Attributes', 'wp-cinema-manager'),
            'parent_item_colon'     => __('Parent Venue:', 'wp-cinema-manager'),
            'all_items'             => __('Venues', 'wp-cinema-manager'),
            'add_new_item'          => __('Add New Venue', 'wp-cinema-manager'),
            'add_new'               => __('Add New', 'wp-cinema-manager'),
            'new_item'              => __('New Venue', 'wp-cinema-manager'),
            'edit_item'             => __('Edit Venue', 'wp-cinema-manager'),
            'update_item'           => __('Update Venue', 'wp-cinema-manager'),
            'view_item'             => __('View Venue', 'wp-cinema-manager'),
            'view_items'            => __('View Venues', 'wp-cinema-manager'),
            'search_items'          => __('Search Venue', 'wp-cinema-manager'),
            'not_found'             => __('Not found', 'wp-cinema-manager'),
            'not_found_in_trash'    => __('Not found in Trash', 'wp-cinema-manager'),
        );
        
        $args = array(
            'label'                 => __('Venue', 'wp-cinema-manager'),
            'description'           => __('Cinema Venues/Halls', 'wp-cinema-manager'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => 'edit.php?post_type=cinema_movie',
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'venues',
        );
        
        register_post_type('cinema_venue', $args);
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'cinema_venue_details',
            __('Venue Details', 'wp-cinema-manager'),
            array($this, 'render_venue_details_metabox'),
            'cinema_venue',
            'normal',
            'high'
        );
        
        add_meta_box(
            'cinema_venue_location',
            __('Location & Contact', 'wp-cinema-manager'),
            array($this, 'render_venue_location_metabox'),
            'cinema_venue',
            'normal',
            'default'
        );
    }
    
    /**
     * Render Venue Details Meta Box
     */
    public function render_venue_details_metabox($post) {
        wp_nonce_field('cinema_venue_meta_box', 'cinema_venue_meta_box_nonce');
        
        $capacity = get_post_meta($post->ID, '_cinema_capacity', true);
        $screen_type = get_post_meta($post->ID, '_cinema_screen_type', true);
        $sound_system = get_post_meta($post->ID, '_cinema_sound_system', true);
        $seats_layout = get_post_meta($post->ID, '_cinema_seats_layout', true);
        $facilities = get_post_meta($post->ID, '_cinema_facilities', true);
        $accessibility = get_post_meta($post->ID, '_cinema_accessibility', true);
        ?>
        <div class="cinema-metabox-wrapper">
            <style>
                .cinema-metabox-wrapper { padding: 10px 0; }
                .cinema-field-row { margin-bottom: 15px; }
                .cinema-field-row label { display: inline-block; width: 150px; font-weight: 600; }
                .cinema-field-row input[type="text"],
                .cinema-field-row input[type="number"],
                .cinema-field-row select,
                .cinema-field-row textarea { width: 60%; padding: 5px; }
                .cinema-field-row textarea { height: 80px; }
                .cinema-checkbox-group { display: inline-block; vertical-align: top; width: 60%; }
                .cinema-checkbox-group label { width: auto; display: inline-block; margin-right: 15px; font-weight: normal; }
            </style>
            
            <div class="cinema-field-row">
                <label for="cinema_capacity"><?php _e('Capacity (seats):', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_capacity" name="cinema_capacity" value="<?php echo esc_attr($capacity); ?>" min="1" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_screen_type"><?php _e('Screen Type:', 'wp-cinema-manager'); ?></label>
                <select id="cinema_screen_type" name="cinema_screen_type">
                    <option value=""><?php _e('Select Type', 'wp-cinema-manager'); ?></option>
                    <option value="Standard" <?php selected($screen_type, 'Standard'); ?>>Standard</option>
                    <option value="IMAX" <?php selected($screen_type, 'IMAX'); ?>>IMAX</option>
                    <option value="3D" <?php selected($screen_type, '3D'); ?>>3D</option>
                    <option value="4DX" <?php selected($screen_type, '4DX'); ?>>4DX</option>
                    <option value="Dolby Cinema" <?php selected($screen_type, 'Dolby Cinema'); ?>>Dolby Cinema</option>
                    <option value="ScreenX" <?php selected($screen_type, 'ScreenX'); ?>>ScreenX</option>
                </select>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_sound_system"><?php _e('Sound System:', 'wp-cinema-manager'); ?></label>
                <select id="cinema_sound_system" name="cinema_sound_system">
                    <option value=""><?php _e('Select System', 'wp-cinema-manager'); ?></option>
                    <option value="Stereo" <?php selected($sound_system, 'Stereo'); ?>>Stereo</option>
                    <option value="Dolby Digital 5.1" <?php selected($sound_system, 'Dolby Digital 5.1'); ?>>Dolby Digital 5.1</option>
                    <option value="Dolby Atmos" <?php selected($sound_system, 'Dolby Atmos'); ?>>Dolby Atmos</option>
                    <option value="DTS" <?php selected($sound_system, 'DTS'); ?>>DTS</option>
                    <option value="THX" <?php selected($sound_system, 'THX'); ?>>THX</option>
                </select>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_seats_layout"><?php _e('Seats Layout:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_seats_layout" name="cinema_seats_layout" value="<?php echo esc_attr($seats_layout); ?>" placeholder="e.g., 10 rows × 20 seats" />
            </div>
            
            <div class="cinema-field-row">
                <label><?php _e('Facilities:', 'wp-cinema-manager'); ?></label>
                <div class="cinema-checkbox-group">
                    <?php
                    $facility_options = array(
                        'reclining_seats' => __('Reclining Seats', 'wp-cinema-manager'),
                        'vip_section' => __('VIP Section', 'wp-cinema-manager'),
                        'food_service' => __('Food Service', 'wp-cinema-manager'),
                        'parking' => __('Parking', 'wp-cinema-manager'),
                        'wifi' => __('WiFi', 'wp-cinema-manager'),
                        'air_conditioning' => __('Air Conditioning', 'wp-cinema-manager'),
                    );
                    
                    $selected_facilities = $facilities ? explode(',', $facilities) : array();
                    
                    foreach ($facility_options as $key => $label) {
                        $checked = in_array($key, $selected_facilities) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="cinema_facilities[]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_accessibility"><?php _e('Accessibility:', 'wp-cinema-manager'); ?></label>
                <textarea id="cinema_accessibility" name="cinema_accessibility"><?php echo esc_textarea($accessibility); ?></textarea>
                <p class="description"><?php _e('Wheelchair access, hearing loops, etc.', 'wp-cinema-manager'); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Venue Location Meta Box
     */
    public function render_venue_location_metabox($post) {
        $address = get_post_meta($post->ID, '_cinema_address', true);
        $city = get_post_meta($post->ID, '_cinema_city', true);
        $state = get_post_meta($post->ID, '_cinema_state', true);
        $zip = get_post_meta($post->ID, '_cinema_zip', true);
        $country = get_post_meta($post->ID, '_cinema_country', true);
        $phone = get_post_meta($post->ID, '_cinema_phone', true);
        $email = get_post_meta($post->ID, '_cinema_email', true);
        $latitude = get_post_meta($post->ID, '_cinema_latitude', true);
        $longitude = get_post_meta($post->ID, '_cinema_longitude', true);
        ?>
        <div class="cinema-metabox-wrapper">
            <div class="cinema-field-row">
                <label for="cinema_address"><?php _e('Address:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_address" name="cinema_address" value="<?php echo esc_attr($address); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_city"><?php _e('City:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_city" name="cinema_city" value="<?php echo esc_attr($city); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_state"><?php _e('State/Province:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_state" name="cinema_state" value="<?php echo esc_attr($state); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_zip"><?php _e('ZIP/Postal Code:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_zip" name="cinema_zip" value="<?php echo esc_attr($zip); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_country"><?php _e('Country:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_country" name="cinema_country" value="<?php echo esc_attr($country); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_phone"><?php _e('Phone:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_phone" name="cinema_phone" value="<?php echo esc_attr($phone); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_email"><?php _e('Email:', 'wp-cinema-manager'); ?></label>
                <input type="email" id="cinema_email" name="cinema_email" value="<?php echo esc_attr($email); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_latitude"><?php _e('Latitude:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_latitude" name="cinema_latitude" value="<?php echo esc_attr($latitude); ?>" placeholder="e.g., 40.7128" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_longitude"><?php _e('Longitude:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_longitude" name="cinema_longitude" value="<?php echo esc_attr($longitude); ?>" placeholder="e.g., -74.0060" />
            </div>
        </div>
        <?php
    }
    
    /**
     * Save Meta Data
     */
    public function save_meta_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['cinema_venue_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['cinema_venue_meta_box_nonce'], 'cinema_venue_meta_box')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save fields
        $fields = array(
            'cinema_capacity' => 'absint',
            'cinema_screen_type' => 'sanitize_text_field',
            'cinema_sound_system' => 'sanitize_text_field',
            'cinema_seats_layout' => 'sanitize_text_field',
            'cinema_accessibility' => 'sanitize_textarea_field',
            'cinema_address' => 'sanitize_text_field',
            'cinema_city' => 'sanitize_text_field',
            'cinema_state' => 'sanitize_text_field',
            'cinema_zip' => 'sanitize_text_field',
            'cinema_country' => 'sanitize_text_field',
            'cinema_phone' => 'sanitize_text_field',
            'cinema_email' => 'sanitize_email',
            'cinema_latitude' => 'sanitize_text_field',
            'cinema_longitude' => 'sanitize_text_field',
        );
        
        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitize_callback, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Handle facilities checkboxes
        if (isset($_POST['cinema_facilities']) && is_array($_POST['cinema_facilities'])) {
            $facilities = array_map('sanitize_text_field', $_POST['cinema_facilities']);
            update_post_meta($post_id, '_cinema_facilities', implode(',', $facilities));
        } else {
            update_post_meta($post_id, '_cinema_facilities', '');
        }
    }
    
    /**
     * Custom Admin Columns
     */
    public function custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['capacity'] = __('Capacity', 'wp-cinema-manager');
        $new_columns['screen_type'] = __('Screen Type', 'wp-cinema-manager');
        $new_columns['city'] = __('City', 'wp-cinema-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Custom Column Content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'capacity':
                $capacity = get_post_meta($post_id, '_cinema_capacity', true);
                echo $capacity ? esc_html($capacity) . ' ' . __('seats', 'wp-cinema-manager') : '—';
                break;
                
            case 'screen_type':
                $screen_type = get_post_meta($post_id, '_cinema_screen_type', true);
                echo $screen_type ? esc_html($screen_type) : '—';
                break;
                
            case 'city':
                $city = get_post_meta($post_id, '_cinema_city', true);
                echo $city ? esc_html($city) : '—';
                break;
        }
    }
}
