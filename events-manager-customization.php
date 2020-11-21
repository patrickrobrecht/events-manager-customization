<?php

/**
 * Plugin Name: Events Manager Customization
 * Description: Customization of the Events Manager plugin
 * Version: 1.0.0
 *
 * @package WordPress
 */

/**
 * @param bool $result
 * @param EM_Booking $EM_Booking
 *
 * @return false
 */
function events_manager_customization_em_validation($result, $EM_Booking)
{
    // Make phone number required.
    if (empty($_REQUEST['dbem_phone'])) {
        $EM_Booking->add_error('Bitte gib eine Telefonnummer an.');
        $result = false;
    }

    // Validate street.
    if (empty($_REQUEST['street'])) {
        $EM_Booking->add_error('Bitte gib die Straße an.');
        $result = false;
    }
    $street = events_manager_customization_em_sanitize($_REQUEST['street']);

    // Validate postal code.
    if (empty($_REQUEST['postal_code'])) {
        $EM_Booking->add_error('Bitte gib die PLZ an.');
        $result = false;
    }
    $postalCode = events_manager_customization_em_sanitize($_REQUEST['postal_code']);

    // Validate city.
    if (empty($_REQUEST['city'])) {
        $EM_Booking->add_error('Bitte gib deinen Wohnort an.');
        $result = false;
    }
    $city = events_manager_customization_em_sanitize($_REQUEST['city']);

    // Adjust comment and add to booking meta data.
    $EM_Booking->booking_comment = sprintf('%s, %s %s. %s', $street, $postalCode, $city, $EM_Booking->booking_comment);
    $EM_Booking->booking_meta['registration']['street'] = $street;
    $EM_Booking->booking_meta['registration']['postal_code'] = $postalCode;
    $EM_Booking->booking_meta['registration']['city'] = $city;
    return $result;
}

add_filter('em_booking_validate', 'events_manager_customization_em_validation', 12, 2);

function events_manager_customization_em_sanitize($string)
{
    return wp_kses(wp_unslash($string), []);
}
