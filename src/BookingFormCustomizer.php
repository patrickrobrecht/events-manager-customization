<?php

namespace EventsManagerCustomization;

use EM_Booking;
use EM_Bookings_Table;

class BookingFormCustomizer
{
    /**
     * @var Field[]
     */
    private static $fields = [];

    /**
     * @var string[]
     */
    private static $keys = [];

    public static function init(): void
    {
        self::$fields[] = new Field(
            'street',
            __('Street and house number', 'events-manager-customization'),
            'text',
            __('Enter your street and house number please.', 'events-manager-customization')
        );
        self::$fields[] = new Field(
            'postal_code',
            __('Postal code', 'events-manager-customization'),
            'text',
            __('Enter your postal code please.', 'events-manager-customization')
        );
        self::$fields[] = new Field(
            'city',
            __('City', 'events-manager-customization'),
            'text',
            __('Enter your city please.', 'events-manager-customization')
        );

        foreach (self::$fields as $field) {
            self::$keys[] = $field->getKey();
        }
    }

    public static function addFieldsToBookingForm(): void
    {
        foreach (self::$fields as $field) {
            $value = !empty($_REQUEST[$field->getKey()]) ? esc_attr($_REQUEST[$field->getKey()]) : '';
            ?>
            <p>
                <label for="<?php echo esc_attr($field->getKey()); ?>">
                    <?php echo esc_html($field->getLabel()); ?>
                </label>
                <input id="<?php echo esc_attr($field->getKey()); ?>" class="input"
                       name="<?php echo esc_attr($field->getKey()); ?>"
                       type="<?php echo esc_attr($field->getInputType()); ?>"
                       value="<?php echo esc_html($value); ?>" />
            </p>
            <?php
        }
    }

    public static function validate($result, $EM_Booking): bool
    {
        // Make phone number required.
        if (empty($_REQUEST['dbem_phone'])) {
            $EM_Booking->add_error(
                __('Enter your phone number please.', 'events-manager-customization')
            );
            $result = false;
        }

        // Validate and save additional fields.
        foreach (self::$fields as $field) {
            if (empty($_REQUEST[$field->getKey()])) {
                $EM_Booking->add_error($field->getErrorMessageIfMissing());
                $result = false;
            } else {
                $EM_Booking->booking_meta['registration'][$field->getKey()] =
                    wp_kses(wp_unslash($_REQUEST[$field->getKey()]), []);
            }
        }

        return $result;
    }

    /**
     * @param array $template
     * @param EM_Bookings_Table $EM_Bookings_Table
     *
     * @return array
     */
    public static function addColumnsToBookingsTable($template, $EM_Bookings_Table): array
    {
        foreach (self::$fields as $field) {
            $template[$field->getKey()] = $field->getLabel();
        }

        return $template;
    }

    /**
     * @param string $val
     * @param string $col
     * @param EM_Booking $EM_Booking
     * @param EM_Bookings_Table $EM_Bookings_Table
     * @param Object $csv
     *
     * @return string
     */
    public static function getColumnForBookingTable($val, $col, $EM_Booking, $EM_Bookings_Table, $csv): string
    {
        if (in_array($col, self::$keys, true)) {
            $val = $EM_Booking->get_person()->{$col} ?? '';
        }

        return $val;
    }
}
