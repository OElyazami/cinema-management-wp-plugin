<?php
/**
 * Movies Custom Post Type
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Cinema_Movies {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'), 5); // Priority 5 to ensure it runs
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_cinema_movie', array($this, 'save_meta_data'));
        add_filter('manage_cinema_movie_posts_columns', array($this, 'custom_columns'));
        add_action('manage_cinema_movie_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }
    
    /**
     * Register Movies Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Movies', 'Post Type General Name', 'wp-cinema-manager'),
            'singular_name'         => _x('Movie', 'Post Type Singular Name', 'wp-cinema-manager'),
            'menu_name'             => __('Cinema', 'wp-cinema-manager'),
            'name_admin_bar'        => __('Movie', 'wp-cinema-manager'),
            'archives'              => __('Movie Archives', 'wp-cinema-manager'),
            'attributes'            => __('Movie Attributes', 'wp-cinema-manager'),
            'parent_item_colon'     => __('Parent Movie:', 'wp-cinema-manager'),
            'all_items'             => __('All Movies', 'wp-cinema-manager'),
            'add_new_item'          => __('Add New Movie', 'wp-cinema-manager'),
            'add_new'               => __('Add New', 'wp-cinema-manager'),
            'new_item'              => __('New Movie', 'wp-cinema-manager'),
            'edit_item'             => __('Edit Movie', 'wp-cinema-manager'),
            'update_item'           => __('Update Movie', 'wp-cinema-manager'),
            'view_item'             => __('View Movie', 'wp-cinema-manager'),
            'view_items'            => __('View Movies', 'wp-cinema-manager'),
            'search_items'          => __('Search Movie', 'wp-cinema-manager'),
            'not_found'             => __('Not found', 'wp-cinema-manager'),
            'not_found_in_trash'    => __('Not found in Trash', 'wp-cinema-manager'),
        );
        
        $args = array(
            'label'                 => __('Movie', 'wp-cinema-manager'),
            'description'           => __('Cinema Movies', 'wp-cinema-manager'),
            'labels'                => $labels,
            'supports'              => array('title', 'thumbnail'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-video-alt3',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'movies',
        );
        
        register_post_type('cinema_movie', $args);
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'cinema_movie_details',
            __('Movie Details', 'wp-cinema-manager'),
            array($this, 'render_movie_details_metabox'),
            'cinema_movie',
            'normal',
            'high'
        );
        
        add_meta_box(
            'cinema_movie_media',
            __('Movie Media', 'wp-cinema-manager'),
            array($this, 'render_movie_media_metabox'),
            'cinema_movie',
            'normal',
            'default'
        );
    }
    
    /**
     * Render Movie Details Meta Box
     */
    public function render_movie_details_metabox($post) {
        wp_nonce_field('cinema_movie_meta_box', 'cinema_movie_meta_box_nonce');
        
        $director = get_post_meta($post->ID, '_cinema_director', true);
        $cast = get_post_meta($post->ID, '_cinema_cast', true);
        $duration = get_post_meta($post->ID, '_cinema_duration', true);
        $release_date = get_post_meta($post->ID, '_cinema_release_date', true);
        $language = get_post_meta($post->ID, '_cinema_language', true);
        $country = get_post_meta($post->ID, '_cinema_country', true);
        $imdb_rating = get_post_meta($post->ID, '_cinema_imdb_rating', true);
        $price = get_post_meta($post->ID, '_cinema_ticket_price', true);
        ?>
        <div class="cinema-metabox-wrapper">
            <div class="cinema-field-row">
                <label for="cinema_director"><?php _e('Director:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_director" name="cinema_director" value="<?php echo esc_attr($director); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_cast"><?php _e('Cast:', 'wp-cinema-manager'); ?></label>
                <textarea id="cinema_cast" name="cinema_cast"><?php echo esc_textarea($cast); ?></textarea>
                <p class="description"><?php _e('Enter cast members separated by commas', 'wp-cinema-manager'); ?></p>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_duration"><?php _e('Duration (minutes):', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_duration" name="cinema_duration" value="<?php echo esc_attr($duration); ?>" min="1" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_release_date"><?php _e('Release Date:', 'wp-cinema-manager'); ?></label>
                <input type="date" id="cinema_release_date" name="cinema_release_date" value="<?php echo esc_attr($release_date); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_language"><?php _e('Language:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_language" name="cinema_language" value="<?php echo esc_attr($language); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_country"><?php _e('Country:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_country" name="cinema_country" value="<?php echo esc_attr($country); ?>" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_imdb_rating"><?php _e('IMDB Rating:', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_imdb_rating" name="cinema_imdb_rating" value="<?php echo esc_attr($imdb_rating); ?>" step="0.1" min="0" max="10" />
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_ticket_price"><?php _e('Ticket Price:', 'wp-cinema-manager'); ?></label>
                <input type="number" id="cinema_ticket_price" name="cinema_ticket_price" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" />
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Movie Media Meta Box
     */
    public function render_movie_media_metabox($post) {
        $trailer_url = get_post_meta($post->ID, '_cinema_trailer_url', true);
        $poster_url = get_post_meta($post->ID, '_cinema_poster_url', true);
        ?>
        <div class="cinema-metabox-wrapper">
            <div class="cinema-field-row">
                <label for="cinema_trailer_url"><?php _e('Trailer URL:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_trailer_url" name="cinema_trailer_url" value="<?php echo esc_url($trailer_url); ?>" placeholder="https://www.youtube.com/watch?v=..." />
                <p class="description"><?php _e('YouTube, Vimeo, or direct video URL', 'wp-cinema-manager'); ?></p>
            </div>
            
            <div class="cinema-field-row">
                <label for="cinema_poster_url"><?php _e('External Poster URL:', 'wp-cinema-manager'); ?></label>
                <input type="text" id="cinema_poster_url" name="cinema_poster_url" value="<?php echo esc_url($poster_url); ?>" />
                <p class="description"><?php _e('Optional: External poster image URL (or use Featured Image)', 'wp-cinema-manager'); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save Meta Data
     */
    public function save_meta_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['cinema_movie_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['cinema_movie_meta_box_nonce'], 'cinema_movie_meta_box')) {
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
            'cinema_director' => 'sanitize_text_field',
            'cinema_cast' => 'sanitize_textarea_field',
            'cinema_duration' => 'absint',
            'cinema_release_date' => 'sanitize_text_field',
            'cinema_language' => 'sanitize_text_field',
            'cinema_country' => 'sanitize_text_field',
            'cinema_imdb_rating' => 'floatval',
            'cinema_ticket_price' => 'floatval',
            'cinema_trailer_url' => 'esc_url_raw',
            'cinema_poster_url' => 'esc_url_raw',
        );
        
        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitize_callback, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Custom Admin Columns
     */
    public function custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumbnail'] = __('Poster', 'wp-cinema-manager');
        $new_columns['title'] = $columns['title'];
        $new_columns['director'] = __('Director', 'wp-cinema-manager');
        $new_columns['duration'] = __('Duration', 'wp-cinema-manager');
        $new_columns['release_date'] = __('Release Date', 'wp-cinema-manager');
        $new_columns['genres'] = __('Genres', 'wp-cinema-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Custom Column Content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 75));
                } else {
                    echo '—';
                }
                break;
                
            case 'director':
                $director = get_post_meta($post_id, '_cinema_director', true);
                echo $director ? esc_html($director) : '—';
                break;
                
            case 'duration':
                $duration = get_post_meta($post_id, '_cinema_duration', true);
                echo $duration ? esc_html($duration) . ' ' . __('min', 'wp-cinema-manager') : '—';
                break;
                
            case 'release_date':
                $date = get_post_meta($post_id, '_cinema_release_date', true);
                echo $date ? esc_html(date_i18n(get_option('date_format'), strtotime($date))) : '—';
                break;
                
            case 'genres':
                $terms = get_the_terms($post_id, 'cinema_genre');
                if ($terms && !is_wp_error($terms)) {
                    $genres = array();
                    foreach ($terms as $term) {
                        $genres[] = $term->name;
                    }
                    echo implode(', ', $genres);
                } else {
                    echo '—';
                }
                break;
        }
    }
}
