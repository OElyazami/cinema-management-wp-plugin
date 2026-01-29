<?php
/**
 * Plugin Name: WP Cinema Manager
 * Plugin URI: https://your-website.com/wp-cinema-manager
 * Description: Complete cinema management system for WordPress. Manage movies, showtimes, venues, and more.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://your-website.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-cinema-manager
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_CINEMA_VERSION', '1.0.0');
define('WP_CINEMA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_CINEMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_CINEMA_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main WP Cinema Manager Class
 */
class WP_Cinema_Manager {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
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
        $this->include_files();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function include_files() {
        $files = array(
            'includes/class-cinema-movies.php',
            'includes/class-cinema-venues.php',
            'includes/class-cinema-showtimes.php',
            'includes/class-cinema-taxonomies.php',
            'includes/class-cinema-admin.php',
            'includes/class-cinema-api.php',
        );
        
        foreach ($files as $file) {
            $filepath = WP_CINEMA_PLUGIN_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            } else {
                // Log error if WordPress debug is enabled
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP Cinema Manager: Missing file - ' . $filepath);
                }
            }
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'), 0); // Priority 0 to run early
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('wp-cinema-manager', false, dirname(WP_CINEMA_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Add theme support for post thumbnails
        add_theme_support('post-thumbnails', array('cinema_movie', 'cinema_venue', 'cinema_showtime'));
        
        // Initialize custom post types
        Cinema_Movies::get_instance();
        Cinema_Venues::get_instance();
        Cinema_Showtimes::get_instance();
        
        // Initialize taxonomies
        Cinema_Taxonomies::get_instance();
        
        // Initialize admin
        if (is_admin()) {
            Cinema_Admin::get_instance();
        }
        
        // Initialize REST API
        Cinema_API::get_instance();
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Trigger init to register post types
        $this->init();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        add_option('wp_cinema_version', WP_CINEMA_VERSION);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function wp_cinema_manager() {
    return WP_Cinema_Manager::get_instance();
}

// Start the plugin
wp_cinema_manager();
