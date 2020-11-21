<?php

/**
 * Plugin Name: Events Manager Customization
 * Plugin URI: https://patrick-robrecht.de/wordpress/
 * Description: Customization of the Events Manager plugin
 * Version: 1.0.0
 * Requires at least: 5.5
 * Requires PHP: 7.4
 * Author: Patrick Robrecht
 * Author URI: https://patrick-robrecht.de/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.htm
 *
 * @package WordPress
 */

use EventsManagerCustomization\BookingFormCustomizer;

require __DIR__ . '/src/Field.php';
require __DIR__ . '/src/BookingFormCustomizer.php';

if (!defined('ABSPATH')) {
    die;
}

BookingFormCustomizer::init();
add_action('em_register_form', [BookingFormCustomizer::class, 'addFieldsToBookingForm']);
add_filter('em_booking_validate', [BookingFormCustomizer::class, 'validate'], 12, 2);
add_action('em_bookings_table_cols_template', [BookingFormCustomizer::class, 'addColumnsToBookingsTable'], 10, 2);
add_filter('em_bookings_table_rows_col', [BookingFormCustomizer::class, 'getColumnForBookingTable'], 10, 5);
