/**
 * Cinema Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Auto-calculate end time based on movie duration
        if ($('#cinema_movie_id').length && $('#cinema_show_time').length) {
            $('#cinema_movie_id, #cinema_show_time').on('change', function() {
                calculateEndTime();
            });
        }
        
        // Auto-populate venue capacity to showtime total seats
        if ($('#cinema_venue_id').length && $('#cinema_total_seats').length) {
            $('#cinema_venue_id').on('change', function() {
                var venueId = $(this).val();
                if (venueId) {
                    // This would require AJAX call to get venue capacity
                    // For now, we'll leave it as manual entry
                }
            });
        }
        
        // Validate showtime form
        if ($('body').hasClass('post-type-cinema_showtime')) {
            $('form#post').on('submit', function(e) {
                var movieId = $('#cinema_movie_id').val();
                var venueId = $('#cinema_venue_id').val();
                var showDate = $('#cinema_show_date').val();
                var showTime = $('#cinema_show_time').val();
                
                if (!movieId || !venueId || !showDate || !showTime) {
                    e.preventDefault();
                    alert('Please fill in all required fields: Movie, Venue, Date, and Time.');
                    return false;
                }
            });
        }
        
        // Add color coding to status badges
        styleStatusBadges();
        
    });
    
    /**
     * Calculate end time based on movie duration and start time
     */
    function calculateEndTime() {
        var movieId = $('#cinema_movie_id').val();
        var showTime = $('#cinema_show_time').val();
        
        if (!movieId || !showTime) {
            return;
        }
        
        // This would require AJAX call to get movie duration
        // For now, we'll skip auto-calculation
        // In production, implement AJAX call to fetch movie meta
    }
    
    /**
     * Style status badges in admin columns
     */
    function styleStatusBadges() {
        $('.cinema-format-badge').each(function() {
            var format = $(this).text().toLowerCase();
            var colors = {
                '3d': '#e74c3c',
                'imax': '#3498db',
                '4dx': '#9b59b6',
                'dolby cinema': '#f39c12',
                '2d': '#95a5a6'
            };
            
            if (colors[format]) {
                $(this).css('background-color', colors[format]);
            }
        });
    }
    
    /**
     * Initialize date picker (optional - requires jQuery UI)
     */
    function initDatePickers() {
        if ($.fn.datepicker) {
            $('input[type="date"]').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }
    }
    
})(jQuery);
