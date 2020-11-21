<?php

namespace EventsManagerCustomization;

use EM_Booking;
use EM_Bookings_Table;
use EM_Event;

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

    /**
     * @var array
     */
    private static $options = [];

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

        self::$options['event_require_phone'] =
            __('Require phone number in booking form', 'events-manager-customization');
        self::$options['event_require_postal_address'] =
            __('Require postal address in booking form', 'events-manager-customization');
    }

    public static function addFieldsToBookingForm(): void
    {
        global $EM_Event;
        $postalAddressRequired = get_post_meta($EM_Event->post_id, '_event_require_postal_address', true) === '1';

        if ($postalAddressRequired) {
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
    }

    /**
     * @param bool $result
     * @param EM_Booking $EM_Booking
     *
     * @return bool
     */
    public static function validateFieldsInBookingForm($result, $EM_Booking): bool
    {
        $EM_Event = $EM_Booking->event;

        // Make phone number required.
        $phoneRequired = get_post_meta($EM_Event->post_id, '_event_require_phone', true) === '1';
        if ($phoneRequired && empty($_REQUEST['dbem_phone'])) {
            $EM_Booking->add_error(
                __('Enter your phone number please.', 'events-manager-customization')
            );
            $result = false;
        }

        // Validate and save additional fields.
        $postalAddressRequired = get_post_meta($EM_Event->post_id, '_event_require_postal_address', true) === '1';
        if ($postalAddressRequired) {
            foreach (self::$fields as $field) {
                if (empty($_REQUEST[$field->getKey()])) {
                    $EM_Booking->add_error($field->getErrorMessageIfMissing());
                    $result = false;
                } else {
                    $EM_Booking->booking_meta['registration'][$field->getKey()] =
                        wp_kses(wp_unslash($_REQUEST[$field->getKey()]), []);
                }
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

    /**
     * @param EM_Event $EM_Event
     */
    public static function addEventMetaOptions($EM_Event): void
    {
        ?>
        <div>
            <h4><?php _e('Require phone number in booking form', 'events-manager-customization'); ?></h4>
            <?php
            foreach (self::$options as $optionKey => $optionName) {
                $required = get_post_meta($EM_Event->post_id, '_' . $optionKey, true) === '1';
                ?>
                <div>
                    <input id="<?php echo esc_attr($optionKey); ?>" name="<?php echo esc_attr($optionKey); ?>"
                           value="1" type="checkbox" <?php echo ($required) ? 'checked="checked"' : ''; ?> />
                    <label for="<?php echo esc_attr($optionKey); ?>">
                        <?php echo esc_html($optionName); ?>
                    </label>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * @param bool $result
     * @param EM_Event $EM_Event
     *
     * @return bool
     */
    public static function saveEventMetaOptions($result, $EM_Event): bool
    {
        foreach (array_keys(self::$options) as $optionKey) {
            $required = $_REQUEST[$optionKey] === '1';
            update_post_meta($EM_Event->post_id, '_' . $optionKey, $required ? 1 : 0);
        }

        return $result;
    }
}
