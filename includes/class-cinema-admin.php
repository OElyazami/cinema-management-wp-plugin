<?php
/**
 * Admin Interface Enhancements
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Cinema_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('parent_file', array($this, 'menu_highlighting'));
    }
    
    /**
     * Add Admin Menu
     */
    public function add_admin_menu() {
        // Add Dashboard submenu
        add_submenu_page(
            'edit.php?post_type=cinema_movie',
            __('Cinema Dashboard', 'wp-cinema-manager'),
            __('Dashboard', 'wp-cinema-manager'),
            'manage_options',
            'cinema-dashboard',
            array($this, 'render_dashboard')
        );
        
        // Add Settings submenu
        add_submenu_page(
            'edit.php?post_type=cinema_movie',
            __('Cinema Settings', 'wp-cinema-manager'),
            __('Settings', 'wp-cinema-manager'),
            'manage_options',
            'cinema-settings',
            array($this, 'render_settings')
        );
    }
    
    /**
     * Enqueue Admin Assets
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        // Load on all cinema post type pages
        $cinema_post_types = array('cinema_movie', 'cinema_venue', 'cinema_showtime');
        
        // Check if we're on a cinema page
        $is_cinema_page = false;
        
        if (in_array($post_type, $cinema_post_types)) {
            $is_cinema_page = true;
        }
        
        if (strpos($hook, 'cinema') !== false) {
            $is_cinema_page = true;
        }
        
        if (!$is_cinema_page) {
            return;
        }
        
        wp_enqueue_style(
            'cinema-admin-css',
            WP_CINEMA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WP_CINEMA_VERSION . '.' . time() // Cache busting during development
        );
        
        wp_enqueue_script(
            'cinema-admin-js',
            WP_CINEMA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            WP_CINEMA_VERSION,
            true
        );
    }
    
    /**
     * Menu Highlighting
     */
    public function menu_highlighting($parent_file) {
        global $submenu_file, $current_screen;
        
        if ($current_screen->post_type == 'cinema_venue' || $current_screen->post_type == 'cinema_showtime') {
            $parent_file = 'edit.php?post_type=cinema_movie';
        }
        
        return $parent_file;
    }
    
    /**
     * Render Dashboard
     */
    public function render_dashboard() {
        // Get statistics
        $movie_count = wp_count_posts('cinema_movie')->publish;
        $venue_count = wp_count_posts('cinema_venue')->publish;
        $showtime_count = wp_count_posts('cinema_showtime')->publish;
        
        // Get upcoming showtimes
        $today = date('Y-m-d');
        $upcoming_showtimes = new WP_Query(array(
            'post_type' => 'cinema_showtime',
            'posts_per_page' => 10,
            'meta_query' => array(
                array(
                    'key' => '_cinema_show_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
            ),
            'meta_key' => '_cinema_show_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ));
        
        // Get recent movies
        $recent_movies = new WP_Query(array(
            'post_type' => 'cinema_movie',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        ?>
        <div class="wrap cinema-dashboard">
            <h1><?php _e('Cinema Dashboard', 'wp-cinema-manager'); ?></h1>
            
            <div class="cinema-stats">
                <div class="cinema-stat-box">
                    <div class="cinema-stat-icon dashicons dashicons-video-alt3"></div>
                    <div class="cinema-stat-content">
                        <h3><?php echo esc_html($movie_count); ?></h3>
                        <p><?php _e('Movies', 'wp-cinema-manager'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('edit.php?post_type=cinema_movie'); ?>" class="cinema-stat-link"><?php _e('View All', 'wp-cinema-manager'); ?></a>
                </div>
                
                <div class="cinema-stat-box">
                    <div class="cinema-stat-icon dashicons dashicons-location"></div>
                    <div class="cinema-stat-content">
                        <h3><?php echo esc_html($venue_count); ?></h3>
                        <p><?php _e('Venues', 'wp-cinema-manager'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('edit.php?post_type=cinema_venue'); ?>" class="cinema-stat-link"><?php _e('View All', 'wp-cinema-manager'); ?></a>
                </div>
                
                <div class="cinema-stat-box">
                    <div class="cinema-stat-icon dashicons dashicons-calendar-alt"></div>
                    <div class="cinema-stat-content">
                        <h3><?php echo esc_html($showtime_count); ?></h3>
                        <p><?php _e('Showtimes', 'wp-cinema-manager'); ?></p>
                    </div>
                    <a href="<?php echo admin_url('edit.php?post_type=cinema_showtime'); ?>" class="cinema-stat-link"><?php _e('View All', 'wp-cinema-manager'); ?></a>
                </div>
            </div>
            
            <div class="cinema-dashboard-grid">
                <div class="cinema-dashboard-section">
                    <h2><?php _e('Upcoming Showtimes', 'wp-cinema-manager'); ?></h2>
                    <?php if ($upcoming_showtimes->have_posts()): ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Movie', 'wp-cinema-manager'); ?></th>
                                    <th><?php _e('Venue', 'wp-cinema-manager'); ?></th>
                                    <th><?php _e('Date & Time', 'wp-cinema-manager'); ?></th>
                                    <th><?php _e('Status', 'wp-cinema-manager'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($upcoming_showtimes->have_posts()): $upcoming_showtimes->the_post(); 
                                    $movie_id = get_post_meta(get_the_ID(), '_cinema_movie_id', true);
                                    $venue_id = get_post_meta(get_the_ID(), '_cinema_venue_id', true);
                                    $show_date = get_post_meta(get_the_ID(), '_cinema_show_date', true);
                                    $show_time = get_post_meta(get_the_ID(), '_cinema_show_time', true);
                                    $status = get_post_meta(get_the_ID(), '_cinema_status', true);
                                    
                                    $movie = $movie_id ? get_post($movie_id) : null;
                                    $venue = $venue_id ? get_post($venue_id) : null;
                                ?>
                                <tr>
                                    <td><?php echo $movie ? esc_html($movie->post_title) : '—'; ?></td>
                                    <td><?php echo $venue ? esc_html($venue->post_title) : '—'; ?></td>
                                    <td><?php echo $show_date ? esc_html(date_i18n(get_option('date_format'), strtotime($show_date))) . ' ' . esc_html($show_time) : '—'; ?></td>
                                    <td><?php echo $status ? esc_html(ucfirst(str_replace('_', ' ', $status))) : 'Available'; ?></td>
                                </tr>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p><?php _e('No upcoming showtimes found.', 'wp-cinema-manager'); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="cinema-dashboard-section">
                    <h2><?php _e('Recent Movies', 'wp-cinema-manager'); ?></h2>
                    <?php if ($recent_movies->have_posts()): ?>
                        <ul class="cinema-recent-list">
                            <?php while ($recent_movies->have_posts()): $recent_movies->the_post(); ?>
                                <li>
                                    <a href="<?php echo get_edit_post_link(); ?>">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        <?php endif; ?>
                                        <span><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    <?php else: ?>
                        <p><?php _e('No movies found.', 'wp-cinema-manager'); ?></p>
                    <?php endif; ?>
                    
                    <a href="<?php echo admin_url('post-new.php?post_type=cinema_movie'); ?>" class="button button-primary" style="margin-top: 15px;">
                        <?php _e('Add New Movie', 'wp-cinema-manager'); ?>
                    </a>
                </div>
            </div>
            
            <div class="cinema-quick-actions">
                <h2><?php _e('Quick Actions', 'wp-cinema-manager'); ?></h2>
                <div class="cinema-action-buttons">
                    <a href="<?php echo admin_url('post-new.php?post_type=cinema_movie'); ?>" class="button button-primary button-large">
                        <span class="dashicons dashicons-plus"></span> <?php _e('Add Movie', 'wp-cinema-manager'); ?>
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=cinema_venue'); ?>" class="button button-primary button-large">
                        <span class="dashicons dashicons-plus"></span> <?php _e('Add Venue', 'wp-cinema-manager'); ?>
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=cinema_showtime'); ?>" class="button button-primary button-large">
                        <span class="dashicons dashicons-plus"></span> <?php _e('Add Showtime', 'wp-cinema-manager'); ?>
                    </a>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=cinema_genre&post_type=cinema_movie'); ?>" class="button button-large">
                        <span class="dashicons dashicons-category"></span> <?php _e('Manage Genres', 'wp-cinema-manager'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <style>
            .cinema-dashboard { max-width: 1400px; }
            .cinema-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
            .cinema-stat-box { background: #fff; border: 1px solid #c3c4c7; padding: 20px; display: flex; align-items: center; gap: 15px; position: relative; }
            .cinema-stat-icon { font-size: 48px; width: 48px; height: 48px; color: #2271b1; }
            .cinema-stat-content h3 { margin: 0; font-size: 32px; font-weight: 600; color: #1d2327; }
            .cinema-stat-content p { margin: 5px 0 0; color: #646970; font-size: 14px; }
            .cinema-stat-link { position: absolute; bottom: 10px; right: 15px; font-size: 13px; text-decoration: none; }
            .cinema-dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin: 20px 0; }
            .cinema-dashboard-section { background: #fff; border: 1px solid #c3c4c7; padding: 20px; }
            .cinema-dashboard-section h2 { margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #c3c4c7; }
            .cinema-recent-list { list-style: none; padding: 0; margin: 0; }
            .cinema-recent-list li { padding: 10px 0; border-bottom: 1px solid #f0f0f1; }
            .cinema-recent-list li:last-child { border-bottom: none; }
            .cinema-recent-list a { display: flex; align-items: center; gap: 10px; text-decoration: none; }
            .cinema-recent-list img { width: 50px; height: 75px; object-fit: cover; }
            .cinema-quick-actions { background: #fff; border: 1px solid #c3c4c7; padding: 20px; margin: 20px 0; }
            .cinema-quick-actions h2 { margin-top: 0; }
            .cinema-action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
            .cinema-action-buttons .dashicons { margin-right: 5px; }
        </style>
        <?php
    }
    
    /**
     * Render Settings Page
     */
    public function render_settings() {
        // Handle form submission
        if (isset($_POST['cinema_settings_nonce']) && wp_verify_nonce($_POST['cinema_settings_nonce'], 'cinema_settings')) {
            // Save settings
            update_option('cinema_currency', sanitize_text_field($_POST['cinema_currency']));
            update_option('cinema_currency_symbol', sanitize_text_field($_POST['cinema_currency_symbol']));
            update_option('cinema_date_format', sanitize_text_field($_POST['cinema_date_format']));
            update_option('cinema_time_format', sanitize_text_field($_POST['cinema_time_format']));
            update_option('cinema_booking_enabled', isset($_POST['cinema_booking_enabled']) ? 1 : 0);
            update_option('cinema_default_ticket_price', floatval($_POST['cinema_default_ticket_price']));
            
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'wp-cinema-manager') . '</p></div>';
        }
        
        // Get current settings
        $currency = get_option('cinema_currency', 'USD');
        $currency_symbol = get_option('cinema_currency_symbol', '$');
        $date_format = get_option('cinema_date_format', 'Y-m-d');
        $time_format = get_option('cinema_time_format', 'H:i');
        $booking_enabled = get_option('cinema_booking_enabled', 0);
        $default_price = get_option('cinema_default_ticket_price', 10.00);
        ?>
        <div class="wrap cinema-settings">
            <h1><?php _e('Cinema Settings', 'wp-cinema-manager'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('cinema_settings', 'cinema_settings_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cinema_currency"><?php _e('Currency', 'wp-cinema-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cinema_currency" name="cinema_currency" value="<?php echo esc_attr($currency); ?>" class="regular-text" />
                            <p class="description"><?php _e('e.g., USD, EUR, GBP', 'wp-cinema-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cinema_currency_symbol"><?php _e('Currency Symbol', 'wp-cinema-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cinema_currency_symbol" name="cinema_currency_symbol" value="<?php echo esc_attr($currency_symbol); ?>" class="small-text" />
                            <p class="description"><?php _e('e.g., $, €, £', 'wp-cinema-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cinema_date_format"><?php _e('Date Format', 'wp-cinema-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cinema_date_format" name="cinema_date_format" value="<?php echo esc_attr($date_format); ?>" class="regular-text" />
                            <p class="description"><?php _e('PHP date format (e.g., Y-m-d, m/d/Y)', 'wp-cinema-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cinema_time_format"><?php _e('Time Format', 'wp-cinema-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cinema_time_format" name="cinema_time_format" value="<?php echo esc_attr($time_format); ?>" class="regular-text" />
                            <p class="description"><?php _e('PHP time format (e.g., H:i, h:i A)', 'wp-cinema-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cinema_default_ticket_price"><?php _e('Default Ticket Price', 'wp-cinema-manager'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="cinema_default_ticket_price" name="cinema_default_ticket_price" value="<?php echo esc_attr($default_price); ?>" step="0.01" min="0" class="small-text" />
                            <p class="description"><?php _e('Default price for new showtimes', 'wp-cinema-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cinema_booking_enabled"><?php _e('Enable Booking', 'wp-cinema-manager'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="cinema_booking_enabled" name="cinema_booking_enabled" value="1" <?php checked($booking_enabled, 1); ?> />
                            <label for="cinema_booking_enabled"><?php _e('Enable online ticket booking', 'wp-cinema-manager'); ?></label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Save Settings', 'wp-cinema-manager')); ?>
            </form>
        </div>
        <?php
    }
}
