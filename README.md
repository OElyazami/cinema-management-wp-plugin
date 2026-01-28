# WP Cinema Manager

A comprehensive WordPress plugin for managing cinema websites. Easily manage movies, showtimes, venues, and everything related to your cinema business.

## Features

### 🎬 Movie Management
- Complete movie information (title, description, director, cast)
- Duration, release date, language, and country
- IMDB ratings and ticket pricing
- Featured images and movie posters
- Trailer video URLs (YouTube, Vimeo)
- Genre classification (Action, Comedy, Drama, etc.)
- Age rating system (G, PG, PG-13, R, NC-17, etc.)
- Language taxonomy

### 🏢 Venue Management
- Multiple cinema venues/halls
- Capacity and seating layout
- Screen type (Standard, IMAX, 3D, 4DX, Dolby Cinema, ScreenX)
- Sound system (Dolby Atmos, DTS, THX, etc.)
- Facilities (Reclining seats, VIP section, Food service, Parking, WiFi)
- Accessibility features
- Complete location details (address, city, phone, email)
- GPS coordinates (latitude/longitude)

### 📅 Showtime Management
- Link movies to specific venues
- Date and time scheduling
- Multiple formats (2D, 3D, IMAX, 4DX)
- Language and subtitle options
- Ticket pricing per showtime
- Seat availability tracking
- Status indicators (Available, Filling Fast, Sold Out, Cancelled)
- External booking URL integration

### 🎯 Admin Features
- Beautiful dashboard with statistics
- Quick actions for common tasks
- Custom admin columns for easy overview
- Sortable and filterable lists
- Bulk edit capabilities
- Settings page for global configuration
- Currency and date format settings

### 🔌 REST API
- Full REST API support for all post types
- Custom endpoints for:
  - Upcoming showtimes
  - Showtimes by movie
  - Showtimes by venue
  - Showtimes by date
  - Now showing movies
  - Coming soon movies
  - Movie search
- Perfect for mobile apps and frontend integrations

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/wp-cinema/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to Cinema > Dashboard to get started

## Usage

### Adding a Movie
1. Go to Cinema > Add New
2. Enter movie title and description
3. Upload featured image (movie poster)
4. Fill in movie details (director, cast, duration, etc.)
5. Select genres and age rating
6. Add trailer URL if available
7. Publish

### Adding a Venue
1. Go to Cinema > Venues > Add New
2. Enter venue name and description
3. Fill in capacity and screen details
4. Select facilities and accessibility features
5. Add location information
6. Publish

### Creating Showtimes
1. Go to Cinema > Showtimes > Add New
2. Select movie and venue
3. Choose date and time
4. Set language, format, and price
5. Configure seat availability
6. Add booking URL (optional)
7. Publish

### Accessing the Dashboard
- Navigate to Cinema > Dashboard
- View statistics and quick actions
- See upcoming showtimes and recent movies

### Configuring Settings
- Go to Cinema > Settings
- Set currency and format preferences
- Configure default ticket prices
- Enable/disable booking features

## REST API Endpoints

### Movies
- `GET /wp-json/wp/v2/movies` - List all movies
- `GET /wp-json/wp/v2/movies/{id}` - Get single movie
- `GET /wp-json/cinema/v1/movies/now-showing` - Get now showing movies
- `GET /wp-json/cinema/v1/movies/coming-soon` - Get coming soon movies
- `GET /wp-json/cinema/v1/movies/search?s=query` - Search movies

### Venues
- `GET /wp-json/wp/v2/venues` - List all venues
- `GET /wp-json/wp/v2/venues/{id}` - Get single venue

### Showtimes
- `GET /wp-json/wp/v2/showtimes` - List all showtimes
- `GET /wp-json/wp/v2/showtimes/{id}` - Get single showtime
- `GET /wp-json/cinema/v1/showtimes/upcoming` - Get upcoming showtimes
- `GET /wp-json/cinema/v1/showtimes/movie/{id}` - Get showtimes for a movie
- `GET /wp-json/cinema/v1/showtimes/venue/{id}` - Get showtimes for a venue
- `GET /wp-json/cinema/v1/showtimes/date/{YYYY-MM-DD}` - Get showtimes by date

### Taxonomies
- `GET /wp-json/wp/v2/genres` - List all genres
- `GET /wp-json/wp/v2/age-ratings` - List all age ratings
- `GET /wp-json/wp/v2/languages` - List all languages

## User Capabilities

The plugin uses standard WordPress post capabilities:
- `edit_posts` - Edit movies, venues, and showtimes
- `publish_posts` - Publish movies, venues, and showtimes
- `delete_posts` - Delete movies, venues, and showtimes
- `manage_options` - Access settings and dashboard

## Customization

### Custom Templates
You can create custom templates in your theme:
- `single-cinema_movie.php` - Single movie template
- `archive-cinema_movie.php` - Movie archive template
- `single-cinema_venue.php` - Single venue template
- `single-cinema_showtime.php` - Single showtime template

### Hooks and Filters
The plugin provides various hooks for customization:
- `cinema_movie_meta_fields` - Modify movie meta fields
- `cinema_venue_meta_fields` - Modify venue meta fields
- `cinema_showtime_meta_fields` - Modify showtime meta fields

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- MySQL 5.6 or higher

## Support

For support, feature requests, or bug reports, please contact the plugin author.

## Changelog

### Version 1.0.0
- Initial release
- Movie management
- Venue management
- Showtime scheduling
- Admin dashboard
- REST API support
- Taxonomies (Genres, Age Ratings, Languages)

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed with ❤️ for cinema owners and managers.
