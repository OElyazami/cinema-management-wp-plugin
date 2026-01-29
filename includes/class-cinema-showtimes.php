<?php
/**
 * Showtimes Custom Post Type
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Cinema_Showtimes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'), 5);
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_cinema_showtime', array($this, 'save_meta_data'));
        add_filter('manage_cinema_showtime_posts_columns', array($this, 'custom_columns'));
        add_action('manage_cinema_showtime_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Register Showtimes Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Showtimes', 'Post Type General Name', 'wp-cinema-manager'),
            'singular_name'         => _x('Showtime', 'Post Type Singular Name', 'wp-cinema-manager'),
            'menu_name'             => __('Showtimes', 'wp-cinema-manager'),
            'name_admin_bar'        => __('Showtime', 'wp-cinema-manager'),
            'archives'              => __('Showtime Archives', 'wp-cinema-manager'),
            'attributes'            => __('Showtime Attributes', 'wp-cinema-manager'),
            'parent_item_colon'     => __('Parent Showtime:', 'wp-cinema-manager'),
            'all_items'             => __('Showtimes', 'wp-cinema-manager'),
            'add_new_item'          => __('Add New Showtime', 'wp-cinema-manager'),
            'add_new'               => __('Add New', 'wp-cinema-manager'),
            'new_item'              => __('New Showtime', 'wp-cinema-manager'),
            'edit_item'             => __('Edit Showtime', 'wp-cinema-manager'),
            'update_item'           => __('Update Showtime', 'wp-cinema-manager'),
            'view_item'             => __('View Showtime', 'wp-cinema-manager'),
            'view_items'            => __('View Showtimes', 'wp-cinema-manager'),
            'search_items'          => __('Search Showtime', 'wp-cinema-manager'),
            'not_found'             => __('Not found', 'wp-cinema-manager'),
            'not_found_in_trash'    => __('Not found in Trash', 'wp-cinema-manager'),
        );
        
        $args = array(
            'label'                 => __('Showtime', 'wp-cinema-manager'),
            'description'           => __('Movie Showtimes', 'wp-cinema-manager'),
            'labels'                => $labels,
            'supports'              => array('title'),
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
            'rest_base'             => 'showtimes',
        );
        
        register_post_type('cinema_showtime', $args);
    }
    
    /**
     * Enqueue Admin Scripts
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;
        if (('post.php' === $hook || 'post-new.php' === $hook) && 'cinema_showtime' === $post_type) {
            wp_enqueue_script('jquery');
        }
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'cinema_showtime_details',
            __('Showtime Details', 'wp-cinema-manager'),
            array($this, 'render_showtime_details_metabox'),
            'cinema_showtime',
            'normal',
            'high'
        );
        
        add_meta_box(
            'cinema_showtime_pricing',
            __('Pricing & Availability', 'wp-cinema-manager'),
            array($this, 'render_showtime_pricing_metabox'),
            'cinema_showtime',
            'side',
            'default'
        );
    }
    
    /**
     * Render Showtime Details Meta Box
     */
    public function render_showtime_details_metabox($post) {
        wp_nonce_field('cinema_showtime_meta_box', 'cinema_showtime_meta_box_nonce');
        
        $movie_id = get_post_meta($post->ID, '_cinema_movie_id', true);
        $venue_id = get_post_meta($post->ID, '_cinema_venue_id', true);
        $show_date = get_post_meta($post->ID, '_cinema_show_date', true);
        $show_time = get_post_meta($post->ID, '_cinema_show_time', true);
        $end_time = get_post_meta($post->ID, '_cinema_end_time', true);
        $language = get_post_meta($post->ID, '_cinema_show_language', true);
        $subtitles = get_post_meta($post->ID, '_cinema_subtitles', true);
        $format = get_post_meta($post->ID, '_cinema_format', true);
        
        // Get all movies
        $movies = get_posts(array(
            'post_type' => 'cinema_movie',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        
        // Get all venues
        $venues = get_posts(array(
            'post_type' => 'cinema_venue',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        ?>
        <div class="cinema-metabox-wrapper">
            <style>
                .cinema-metabox-wrapper { padding: 10px 0; }
                .cinema-field-row { margin-bottom: 15px; }
                .cinema-field-row label { display: inline-block; width: 150px; font-weight: 600; vertical-align: top; }
                .cinema-field-row input[type="text"],
                .cinema-field-row input[type="date"],
                .cinema-field-row input[type="time"],
                .cinema-field-row select { width: 60%; padding: 5px; }
                .cinema-field-row select { max-width: 400px; }
            </style>
            
            <div class="cinema-field-row">
                <label for="cinema_movie_id"><?php _e('Movie:', 'wp-cinema-manager'); ?> *</label>
                <select id="cinema_movie_id" name="cinema_movie_id" required>
                    <option value=""><?php _e('Select Movie', 'wp-cinema-manager'); ?></option>
                    <?php foreach ($movies as $movie): ?>
                        <option value="<?php echo esc_attr($movie->ID); ?>" <?php selected($movie_id, $movie->ID); ?>>
                            <?php echo esc_html($movie->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_venue_id"><?php _e('Venue/Hall:', 'wp-cinema-manager'); ?> *</label>
                <select id="cinema_venue_id" name="cinema_venue_id" required>
                    <option value=""><?php _e('Select Venue', 'wp-cinema-manager'); ?></option>
                    <?php foreach ($venues as $venue): ?>
                        <option value="<?php echo esc_attr($venue->ID); ?>" <?php selected($venue_id, $venue->ID); ?>>
                            <?php echo esc_html($venue->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_show_date"><?php _e('Show Date:', 'wp-cinema-manager'); ?> *</label>
                <input type="date" id="cinema_show_date" name="cinema_show_date" value="<?php echo esc_attr($show_date); ?>" required />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_show_time"><?php _e('Start Time:', 'wp-cinema-manager'); ?> *</label>
                <input type="time" id="cinema_show_time" name="cinema_show_time" value="<?php echo esc_attr($show_time); ?>" required />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_end_time"><?php _e('End Time:', 'wp-cinema-manager'); ?></label>
                <input type="time" id="cinema_end_time" name="cinema_end_time" value="<?php echo esc_attr($end_time); ?>" />
                <p class="description" style="margin-left: 150px;"><?php _e('Optional: Automatically calculated from movie duration', 'wp-cinema-manager'); ?></p>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_show_language"><?php _e('Language:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_show_language" name="cinema_show_language" value="<?php echo esc_attr($language); ?>" placeholder="e.g., English" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_subtitles"><?php _e('Subtitles:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_subtitles" name="cinema_subtitles" value="<?php echo esc_attr($subtitles); ?>" placeholder="e.g., Spanish, French" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_format"><?php _e('Format:', 'wp-cinema-manager'); ?></label>
                <select id="cinema_format" name="cinema_format">
                    <option value=""><?php _e('Select Format', 'wp-cinema-manager'); ?></option>
                    <option value="2D" <?php selected($format, '2D'); ?>>2D</option>
                    <option value="3D" <?php selected($format, '3D'); ?>>3D</option>
                    <option value="IMAX" <?php selected($format, 'IMAX'); ?>>IMAX</option>
                    <option value="IMAX 3D" <?php selected($format, 'IMAX 3D'); ?>>IMAX 3D</option>
                    <option value="4DX" <?php selected($format, '4DX'); ?>>4DX</option>
                    <option value="Dolby Cinema" <?php selected($format, 'Dolby Cinema'); ?>>Dolby Cinema</option>
                </select>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Showtime Pricing Meta Box
     */
    public function render_showtime_pricing_metabox($post) {
        $price = get_post_meta($post->ID, '_cinema_price', true);
        $available_seats = get_post_meta($post->ID, '_cinema_available_seats', true);
        $total_seats = get_post_meta($post->ID, '_cinema_total_seats', true);
        $booking_url = get_post_meta($post->ID, '_cinema_booking_url', true);
        $status = get_post_meta($post->ID, '_cinema_status', true);
        ?>
        <div class="cinema-metabox-wrapper">
            <div class="cinema-field-row">
                <label for="cinema_price"><?php _e('Ticket Price:', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_price" name="cinema_price" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" style="width: 100%;" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_total_seats"><?php _e('Total Seats:', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_total_seats" name="cinema_total_seats" value="<?php echo esc_attr($total_seats); ?>" min="0" style="width: 100%;" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_available_seats"><?php _e('Available Seats:', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_available_seats" name="cinema_available_seats" value="<?php echo esc_attr($available_seats); ?>" min="0" style="width: 100%;" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_status"><?php _e('Status:', 'wp-cinema-manager'); ?></label>
                <select id="cinema_status" name="cinema_status" style="width: 100%;">
                    <option value="available" <?php selected($status, 'available'); ?>><?php _e('Available', 'wp-cinema-manager'); ?></option>
                    <option value="filling_fast" <?php selected($status, 'filling_fast'); ?>><?php _e('Filling Fast', 'wp-cinema-manager'); ?></option>
                    <option value="sold_out" <?php selected($status, 'sold_out'); ?>><?php _e('Sold Out', 'wp-cinema-manager'); ?></option>
                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'wp-cinema-manager'); ?></option>
                </select>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_booking_url"><?php _e('Booking URL:', 'wp-cinema-manager'); ?></label>
                <input type="url" id="cinema_booking_url" name="cinema_booking_url" value="<?php echo esc_url($booking_url); ?>" style="width: 100%;" />
                <p class="description"><?php _e('External booking link', 'wp-cinema-manager'); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save Meta Data
     */
    public function save_meta_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['cinema_showtime_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['cinema_showtime_meta_box_nonce'], 'cinema_showtime_meta_box')) {
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
            'cinema_movie_id' => 'absint',
            'cinema_venue_id' => 'absint',
            'cinema_show_date' => 'sanitize_text_field',
            'cinema_show_time' => 'sanitize_text_field',
            'cinema_end_time' => 'sanitize_text_field',
            'cinema_show_language' => 'sanitize_text_field',
            'cinema_subtitles' => 'sanitize_text_field',
            'cinema_format' => 'sanitize_text_field',
            'cinema_price' => 'floatval',
            'cinema_available_seats' => 'absint',
            'cinema_total_seats' => 'absint',
            'cinema_booking_url' => 'esc_url_raw',
            'cinema_status' => 'sanitize_text_field',
        );
        
        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitize_callback, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Auto-generate title if empty
        if (isset($_POST['cinema_movie_id']) && isset($_POST['cinema_show_date']) && isset($_POST['cinema_show_time'])) {
            $movie_id = absint($_POST['cinema_movie_id']);
            $show_date = sanitize_text_field($_POST['cinema_show_date']);
            $show_time = sanitize_text_field($_POST['cinema_show_time']);
            
            if ($movie_id && $show_date && $show_time) {
                $movie = get_post($movie_id);
                if ($movie) {
                    $title = $movie->post_title . ' - ' . date_i18n(get_option('date_format'), strtotime($show_date)) . ' ' . $show_time;
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_title' => $title,
                    ));
                }
            }
        }
    }
    
    /**
     * Custom Admin Columns
     */
    public function custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['movie'] = __('Movie', 'wp-cinema-manager');
        $new_columns['venue'] = __('Venue', 'wp-cinema-manager');
        $new_columns['show_date'] = __('Date & Time', 'wp-cinema-manager');
        $new_columns['format'] = __('Format', 'wp-cinema-manager');
        $new_columns['status'] = __('Status', 'wp-cinema-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Custom Column Content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'movie':
                $movie_id = get_post_meta($post_id, '_cinema_movie_id', true);
                if ($movie_id) {
                    $movie = get_post($movie_id);
                    if ($movie) {
                        echo '<a href="' . get_edit_post_link($movie_id) . '">' . esc_html($movie->post_title) . '</a>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'venue':
                $venue_id = get_post_meta($post_id, '_cinema_venue_id', true);
                if ($venue_id) {
                    $venue = get_post($venue_id);
                    if ($venue) {
                        echo '<a href="' . get_edit_post_link($venue_id) . '">' . esc_html($venue->post_title) . '</a>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'show_date':
                $show_date = get_post_meta($post_id, '_cinema_show_date', true);
                $show_time = get_post_meta($post_id, '_cinema_show_time', true);
                if ($show_date && $show_time) {
                    echo esc_html(date_i18n(get_option('date_format'), strtotime($show_date))) . '<br>';
                    echo '<strong>' . esc_html($show_time) . '</strong>';
                } else {
                    echo '—';
                }
                break;
                
            case 'format':
                $format = get_post_meta($post_id, '_cinema_format', true);
                echo $format ? '<span class="cinema-format-badge">' . esc_html($format) . '</span>' : '—';
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_cinema_status', true);
                $status_labels = array(
                    'available' => __('Available', 'wp-cinema-manager'),
                    'filling_fast' => __('Filling Fast', 'wp-cinema-manager'),
                    'sold_out' => __('Sold Out', 'wp-cinema-manager'),
                    'cancelled' => __('Cancelled', 'wp-cinema-manager'),
                );
                $status_label = isset($status_labels[$status]) ? $status_labels[$status] : $status_labels['available'];
                $status_color = array(
                    'available' => '#46b450',
                    'filling_fast' => '#ffb900',
                    'sold_out' => '#dc3232',
                    'cancelled' => '#999',
                );
                $color = isset($status_color[$status]) ? $status_color[$status] : $status_color['available'];
                echo '<span style="display: inline-block; padding: 3px 8px; background: ' . $color . '; color: white; border-radius: 3px; font-size: 11px;">' . esc_html($status_label) . '</span>';
                break;
        }
    }
}
