<?php
/**
 * REST API Endpoints
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Cinema_API {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('rest_api_init', array($this, 'register_api_routes'));
        add_action('rest_api_init', array($this, 'add_meta_to_rest_api'));
    }
    
    /**
     * Register API Routes
     */
    public function register_api_routes() {
        // Get upcoming showtimes
        register_rest_route('cinema/v1', '/showtimes/upcoming', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_upcoming_showtimes'),
            'permission_callback' => '__return_true',
        ));
        
        // Get showtimes by movie
        register_rest_route('cinema/v1', '/showtimes/movie/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_showtimes_by_movie'),
            'permission_callback' => '__return_true',
        ));
        
        // Get showtimes by venue
        register_rest_route('cinema/v1', '/showtimes/venue/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_showtimes_by_venue'),
            'permission_callback' => '__return_true',
        ));
        
        // Get showtimes by date
        register_rest_route('cinema/v1', '/showtimes/date/(?P<date>[\d-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_showtimes_by_date'),
            'permission_callback' => '__return_true',
        ));
        
        // Get now showing movies
        register_rest_route('cinema/v1', '/movies/now-showing', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_now_showing_movies'),
            'permission_callback' => '__return_true',
        ));
        
        // Get coming soon movies
        register_rest_route('cinema/v1', '/movies/coming-soon', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_coming_soon_movies'),
            'permission_callback' => '__return_true',
        ));
        
        // Search movies
        register_rest_route('cinema/v1', '/movies/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_movies'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Add Meta Fields to REST API
     */
    public function add_meta_to_rest_api() {
        // Movie meta fields
        $movie_meta_fields = array(
            'director' => 'string',
            'cast' => 'string',
            'duration' => 'integer',
            'release_date' => 'string',
            'language' => 'string',
            'country' => 'string',
            'imdb_rating' => 'number',
            'ticket_price' => 'number',
            'trailer_url' => 'string',
            'poster_url' => 'string',
        );
        
        foreach ($movie_meta_fields as $field => $type) {
            register_rest_field('cinema_movie', $field, array(
                'get_callback' => function($object) use ($field) {
                    return get_post_meta($object['id'], '_cinema_' . $field, true);
                },
                'update_callback' => function($value, $object) use ($field) {
                    return update_post_meta($object->ID, '_cinema_' . $field, $value);
                },
                'schema' => array(
                    'type' => $type,
                    'context' => array('view', 'edit'),
                ),
            ));
        }
        
        // Venue meta fields
        $venue_meta_fields = array(
            'capacity' => 'integer',
            'screen_type' => 'string',
            'sound_system' => 'string',
            'seats_layout' => 'string',
            'facilities' => 'string',
            'accessibility' => 'string',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'zip' => 'string',
            'country' => 'string',
            'phone' => 'string',
            'email' => 'string',
            'latitude' => 'string',
            'longitude' => 'string',
        );
        
        foreach ($venue_meta_fields as $field => $type) {
            register_rest_field('cinema_venue', $field, array(
                'get_callback' => function($object) use ($field) {
                    return get_post_meta($object['id'], '_cinema_' . $field, true);
                },
                'update_callback' => function($value, $object) use ($field) {
                    return update_post_meta($object->ID, '_cinema_' . $field, $value);
                },
                'schema' => array(
                    'type' => $type,
                    'context' => array('view', 'edit'),
                ),
            ));
        }
        
        // Showtime meta fields
        $showtime_meta_fields = array(
            'movie_id' => 'integer',
            'venue_id' => 'integer',
            'show_date' => 'string',
            'show_time' => 'string',
            'end_time' => 'string',
            'show_language' => 'string',
            'subtitles' => 'string',
            'format' => 'string',
            'price' => 'number',
            'available_seats' => 'integer',
            'total_seats' => 'integer',
            'booking_url' => 'string',
            'status' => 'string',
        );
        
        foreach ($showtime_meta_fields as $field => $type) {
            register_rest_field('cinema_showtime', $field, array(
                'get_callback' => function($object) use ($field) {
                    $value = get_post_meta($object['id'], '_cinema_' . $field, true);
                    
                    // If it's movie_id or venue_id, also return the post object
                    if ($field === 'movie_id' && $value) {
                        return array(
                            'id' => (int) $value,
                            'title' => get_the_title($value),
                            'link' => get_permalink($value),
                        );
                    } elseif ($field === 'venue_id' && $value) {
                        return array(
                            'id' => (int) $value,
                            'title' => get_the_title($value),
                            'link' => get_permalink($value),
                        );
                    }
                    
                    return $value;
                },
                'update_callback' => function($value, $object) use ($field) {
                    // If it's an array with id, extract the id
                    if (is_array($value) && isset($value['id'])) {
                        $value = $value['id'];
                    }
                    return update_post_meta($object->ID, '_cinema_' . $field, $value);
                },
                'schema' => array(
                    'type' => ($field === 'movie_id' || $field === 'venue_id') ? 'object' : $type,
                    'context' => array('view', 'edit'),
                ),
            ));
        }
    }
    
    /**
     * Get Upcoming Showtimes
     */
    public function get_upcoming_showtimes($request) {
        $today = date('Y-m-d');
        $per_page = $request->get_param('per_page') ?: 20;
        $page = $request->get_param('page') ?: 1;
        
        $args = array(
            'post_type' => 'cinema_showtime',
            'posts_per_page' => $per_page,
            'paged' => $page,
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
        );
        
        $query = new WP_Query($args);
        return $this->format_showtime_response($query);
    }
    
    /**
     * Get Showtimes by Movie
     */
    public function get_showtimes_by_movie($request) {
        $movie_id = $request->get_param('id');
        
        $args = array(
            'post_type' => 'cinema_showtime',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_cinema_movie_id',
                    'value' => $movie_id,
                    'compare' => '=',
                ),
            ),
            'meta_key' => '_cinema_show_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        
        $query = new WP_Query($args);
        return $this->format_showtime_response($query);
    }
    
    /**
     * Get Showtimes by Venue
     */
    public function get_showtimes_by_venue($request) {
        $venue_id = $request->get_param('id');
        
        $args = array(
            'post_type' => 'cinema_showtime',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_cinema_venue_id',
                    'value' => $venue_id,
                    'compare' => '=',
                ),
            ),
            'meta_key' => '_cinema_show_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        
        $query = new WP_Query($args);
        return $this->format_showtime_response($query);
    }
    
    /**
     * Get Showtimes by Date
     */
    public function get_showtimes_by_date($request) {
        $date = $request->get_param('date');
        
        $args = array(
            'post_type' => 'cinema_showtime',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_cinema_show_date',
                    'value' => $date,
                    'compare' => '=',
                    'type' => 'DATE',
                ),
            ),
            'meta_key' => '_cinema_show_time',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        
        $query = new WP_Query($args);
        return $this->format_showtime_response($query);
    }
    
    /**
     * Get Now Showing Movies
     */
    public function get_now_showing_movies($request) {
        $today = date('Y-m-d');
        
        // Get all movie IDs that have showtimes today or in the future
        $showtimes = new WP_Query(array(
            'post_type' => 'cinema_showtime',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => '_cinema_show_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
            ),
        ));
        
        $movie_ids = array();
        foreach ($showtimes->posts as $showtime_id) {
            $movie_id = get_post_meta($showtime_id, '_cinema_movie_id', true);
            if ($movie_id && !in_array($movie_id, $movie_ids)) {
                $movie_ids[] = $movie_id;
            }
        }
        
        if (empty($movie_ids)) {
            return array();
        }
        
        $args = array(
            'post_type' => 'cinema_movie',
            'posts_per_page' => -1,
            'post__in' => $movie_ids,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        
        return get_posts($args);
    }
    
    /**
     * Get Coming Soon Movies
     */
    public function get_coming_soon_movies($request) {
        $today = date('Y-m-d');
        
        $args = array(
            'post_type' => 'cinema_movie',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_cinema_release_date',
                    'value' => $today,
                    'compare' => '>',
                    'type' => 'DATE',
                ),
            ),
            'meta_key' => '_cinema_release_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        
        return get_posts($args);
    }
    
    /**
     * Search Movies
     */
    public function search_movies($request) {
        $search = $request->get_param('s');
        $genre = $request->get_param('genre');
        
        $args = array(
            'post_type' => 'cinema_movie',
            'posts_per_page' => -1,
            's' => $search,
        );
        
        if ($genre) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'cinema_genre',
                    'field' => 'slug',
                    'terms' => $genre,
                ),
            );
        }
        
        return get_posts($args);
    }
    
    /**
     * Format Showtime Response
     */
    private function format_showtime_response($query) {
        $showtimes = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                $movie_id = get_post_meta($post_id, '_cinema_movie_id', true);
                $venue_id = get_post_meta($post_id, '_cinema_venue_id', true);
                
                $showtimes[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'movie' => $movie_id ? array(
                        'id' => (int) $movie_id,
                        'title' => get_the_title($movie_id),
                        'link' => get_permalink($movie_id),
                    ) : null,
                    'venue' => $venue_id ? array(
                        'id' => (int) $venue_id,
                        'title' => get_the_title($venue_id),
                        'link' => get_permalink($venue_id),
                    ) : null,
                    'show_date' => get_post_meta($post_id, '_cinema_show_date', true),
                    'show_time' => get_post_meta($post_id, '_cinema_show_time', true),
                    'end_time' => get_post_meta($post_id, '_cinema_end_time', true),
                    'language' => get_post_meta($post_id, '_cinema_show_language', true),
                    'subtitles' => get_post_meta($post_id, '_cinema_subtitles', true),
                    'format' => get_post_meta($post_id, '_cinema_format', true),
                    'price' => get_post_meta($post_id, '_cinema_price', true),
                    'available_seats' => get_post_meta($post_id, '_cinema_available_seats', true),
                    'total_seats' => get_post_meta($post_id, '_cinema_total_seats', true),
                    'status' => get_post_meta($post_id, '_cinema_status', true),
                    'booking_url' => get_post_meta($post_id, '_cinema_booking_url', true),
                );
            }
            wp_reset_postdata();
        }
        
        return $showtimes;
    }
}
