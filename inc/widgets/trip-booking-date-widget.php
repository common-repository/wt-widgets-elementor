<?php

/**
 * This file is used to fetch the trip review form.
 */

namespace WTWE\Widgets\Single_Page_Trip_Booking_Date;

use Elementor\Widget_Base;
use WTWE\Helper\WTWE_Helper;

defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Booking_Date')) {
    class WTWE_Trip_Booking_Date extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }

        public function get_name()
        {
            return 'wp-travel-trip-booking-date';
        }

        public function get_title()
        {
            return esc_html__('Trip Booking Date', 'wt-widgets-elementor');
        }

        public function get_icon()
        {
            return 'eicon-form-horizontal';
        }

        public function get_categories()
        {
            return ['wp-travel-single'];
        }

        protected function render()
        {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            global $post;

            $settings        = wptravel_get_settings();
            $trip_id         = $post->ID;
            $trip_id         = apply_filters('wp_travel_booking_tab_custom_trip_id', $trip_id);
            $fixed_departure = get_post_meta($trip_id, 'wp_travel_fixed_departure', true);
            $wrapper_id      = isset($tab_key) ? $tab_key . '-booking-form' : 'booking-form'; // temp fixes.
            if (wptravel_is_react_version_enabled()) {
                $wrapper_id = isset($tab_key) ? $tab_key : 'booking';
            }

            $settings_listing = $settings['trip_date_listing'];
            $fixed_departure  = get_post_meta($trip_id, 'wp_travel_fixed_departure', true);
            $wrapper_class    = 'dates' === $settings_listing && 'yes' === $fixed_departure ? 'wp-travel-list-view' : 'wp-travel-calendar-view';

            if (!empty($trip_id) && get_post_type($trip_id) == 'itineraries') {
?>
                <div id="<?php echo esc_attr($wrapper_id); ?>" class="<?php echo esc_attr($wrapper_class); ?>">
                    <?php esc_html_e('Please wait...', 'wp-travel'); ?>
                </div>
            <?php
            } else {
                WTWE_Helper::wtwe_get_widget_notice( __( 'This widget is only visible on the frontend for itineraries.', 'wt-widgets-elementor' ), 'info');
            }
        }
        /**
         * Render the widget output on the editor.
         *
         * Written in JS and used to generate the final HTML.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function _content_template()
        {
            ?>
            <div id="wptravel-trip-booking-date-widget" class="wtwe-trip-booking-date-widget">
                <h5><?php echo esc_html__('Booking Date', 'wt-widgets-elementor'); ?></h5>
            </div>
            <div id="booking" class="wp-travel-calendar-view">
                <div class="wp-travel-booking__header">
                    <h3><?php echo esc_html__('Select Date and Pricing Options for this trip in the Trip Options setting.', 'wt-widgets-elementor'); ?></h3>
                </div>
                <div class="wp-travel-booking__datepicker-wrapper">
                    <div class="react-datepicker-wrapper">
                        <div class="react-datepicker__input-container">
                            <button class="wp-travel-date-picker-btn"><?php echo esc_html__('Select a Date', 'wt-widgets-elementor'); ?><span><i class="far fa-calendar-alt"></i></span></button>
                        </div>
                    </div>
                    <p><?php echo esc_html__('Select a Date to view available pricings and other options.','wt-widgets-elementor'); ?></p>
                </div>
            </div>
<?php
        }
    }
}
