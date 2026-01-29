<?php
/**
 * Custom Taxonomies for Cinema
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Cinema_Taxonomies {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_taxonomies'), 5);
    }
    
    /**
     * Register Custom Taxonomies
     */
    public function register_taxonomies() {
        // Register Genre Taxonomy
        $this->register_genre_taxonomy();
        
        // Register Age Rating Taxonomy
        $this->register_age_rating_taxonomy();
        
        // Register Movie Language Taxonomy
        $this->register_language_taxonomy();
    }
    
    /**
     * Register Genre Taxonomy
     */
    private function register_genre_taxonomy() {
        $labels = array(
            'name'                       => _x('Genres', 'Taxonomy General Name', 'wp-cinema-manager'),
            'singular_name'              => _x('Genre', 'Taxonomy Singular Name', 'wp-cinema-manager'),
            'menu_name'                  => __('Genres', 'wp-cinema-manager'),
            'all_items'                  => __('All Genres', 'wp-cinema-manager'),
            'parent_item'                => __('Parent Genre', 'wp-cinema-manager'),
            'parent_item_colon'          => __('Parent Genre:', 'wp-cinema-manager'),
            'new_item_name'              => __('New Genre Name', 'wp-cinema-manager'),
            'add_new_item'               => __('Add New Genre', 'wp-cinema-manager'),
            'edit_item'                  => __('Edit Genre', 'wp-cinema-manager'),
            'update_item'                => __('Update Genre', 'wp-cinema-manager'),
            'view_item'                  => __('View Genre', 'wp-cinema-manager'),
            'separate_items_with_commas' => __('Separate genres with commas', 'wp-cinema-manager'),
            'add_or_remove_items'        => __('Add or remove genres', 'wp-cinema-manager'),
            'choose_from_most_used'      => __('Choose from the most used', 'wp-cinema-manager'),
            'popular_items'              => __('Popular Genres', 'wp-cinema-manager'),
            'search_items'               => __('Search Genres', 'wp-cinema-manager'),
            'not_found'                  => __('Not Found', 'wp-cinema-manager'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'genres',
        );
        
        register_taxonomy('cinema_genre', array('cinema_movie'), $args);
        
        // Add default genres if they don't exist
        $this->add_default_genres();
    }
    
    /**
     * Register Age Rating Taxonomy
     */
    private function register_age_rating_taxonomy() {
        $labels = array(
            'name'                       => _x('Age Ratings', 'Taxonomy General Name', 'wp-cinema-manager'),
            'singular_name'              => _x('Age Rating', 'Taxonomy Singular Name', 'wp-cinema-manager'),
            'menu_name'                  => __('Age Ratings', 'wp-cinema-manager'),
            'all_items'                  => __('All Ratings', 'wp-cinema-manager'),
            'parent_item'                => __('Parent Rating', 'wp-cinema-manager'),
            'parent_item_colon'          => __('Parent Rating:', 'wp-cinema-manager'),
            'new_item_name'              => __('New Rating Name', 'wp-cinema-manager'),
            'add_new_item'               => __('Add New Rating', 'wp-cinema-manager'),
            'edit_item'                  => __('Edit Rating', 'wp-cinema-manager'),
            'update_item'                => __('Update Rating', 'wp-cinema-manager'),
            'view_item'                  => __('View Rating', 'wp-cinema-manager'),
            'separate_items_with_commas' => __('Separate ratings with commas', 'wp-cinema-manager'),
            'add_or_remove_items'        => __('Add or remove ratings', 'wp-cinema-manager'),
            'choose_from_most_used'      => __('Choose from the most used', 'wp-cinema-manager'),
            'popular_items'              => __('Popular Ratings', 'wp-cinema-manager'),
            'search_items'               => __('Search Ratings', 'wp-cinema-manager'),
            'not_found'                  => __('Not Found', 'wp-cinema-manager'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'age-ratings',
        );
        
        register_taxonomy('cinema_age_rating', array('cinema_movie'), $args);
        
        // Add default age ratings if they don't exist
        $this->add_default_age_ratings();
    }
    
    /**
     * Register Language Taxonomy
     */
    private function register_language_taxonomy() {
        $labels = array(
            'name'                       => _x('Languages', 'Taxonomy General Name', 'wp-cinema-manager'),
            'singular_name'              => _x('Language', 'Taxonomy Singular Name', 'wp-cinema-manager'),
            'menu_name'                  => __('Languages', 'wp-cinema-manager'),
            'all_items'                  => __('All Languages', 'wp-cinema-manager'),
            'parent_item'                => __('Parent Language', 'wp-cinema-manager'),
            'parent_item_colon'          => __('Parent Language:', 'wp-cinema-manager'),
            'new_item_name'              => __('New Language Name', 'wp-cinema-manager'),
            'add_new_item'               => __('Add New Language', 'wp-cinema-manager'),
            'edit_item'                  => __('Edit Language', 'wp-cinema-manager'),
            'update_item'                => __('Update Language', 'wp-cinema-manager'),
            'view_item'                  => __('View Language', 'wp-cinema-manager'),
            'separate_items_with_commas' => __('Separate languages with commas', 'wp-cinema-manager'),
            'add_or_remove_items'        => __('Add or remove languages', 'wp-cinema-manager'),
            'choose_from_most_used'      => __('Choose from the most used', 'wp-cinema-manager'),
            'popular_items'              => __('Popular Languages', 'wp-cinema-manager'),
            'search_items'               => __('Search Languages', 'wp-cinema-manager'),
            'not_found'                  => __('Not Found', 'wp-cinema-manager'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'languages',
        );
        
        register_taxonomy('cinema_language', array('cinema_movie'), $args);
    }
    
    /**
     * Add Default Genres
     */
    private function add_default_genres() {
        $default_genres = array(
            'Action',
            'Adventure',
            'Animation',
            'Comedy',
            'Crime',
            'Documentary',
            'Drama',
            'Fantasy',
            'Horror',
            'Mystery',
            'Romance',
            'Science Fiction',
            'Thriller',
            'Western',
            'Musical',
            'Biography',
            'Family',
            'War',
            'History',
            'Sport',
        );
        
        foreach ($default_genres as $genre) {
            if (!term_exists($genre, 'cinema_genre')) {
                wp_insert_term($genre, 'cinema_genre');
            }
        }
    }
    
    /**
     * Add Default Age Ratings
     */
    private function add_default_age_ratings() {
        $default_ratings = array(
            'G' => 'General Audiences - All ages admitted',
            'PG' => 'Parental Guidance Suggested',
            'PG-13' => 'Parents Strongly Cautioned - Some material may be inappropriate for children under 13',
            'R' => 'Restricted - Under 17 requires accompanying parent or adult guardian',
            'NC-17' => 'Adults Only - No one 17 and under admitted',
            'U' => 'Universal - Suitable for all',
            '12A' => 'Cinema release suitable for 12 years and over',
            '15' => 'Suitable only for 15 years and over',
            '18' => 'Suitable only for adults',
        );
        
        foreach ($default_ratings as $rating => $description) {
            if (!term_exists($rating, 'cinema_age_rating')) {
                wp_insert_term(
                    $rating,
                    'cinema_age_rating',
                    array('description' => $description)
                );
            }
        }
    }
}
