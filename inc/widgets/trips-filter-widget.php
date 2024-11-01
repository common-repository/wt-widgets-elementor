<?php

/**
 * This file is used to fetch the trip review form.
 */

namespace WTWE\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WTWE\Helper\WTWE_Helper;

defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Filter')) {
    class WTWE_Trip_Filter extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);

            $prefixed = defined(WP_DEBUG) ? '.min' : '';
            wp_register_style('trips-filter', plugins_url('assets/css/trips-filter' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
        }

        public function get_name()
        {
            return 'wp-travel-trips-filter';
        }

        public function get_title()
        {
            return esc_html__('Trips Filter', 'wt-widgets-elementor');
        }

        public function get_icon()
        {
            return 'eicon-form-horizontal';
        }

        public function get_categories()
        {
            return ['wp-travel'];
        }

        /**
         * Enqueue styles.
         */
        public function get_style_depends()
        {
            return array('trips-filter');
        }

        // Register and setup control setting for trip filter
        public function _register_controls()
        {
            // register controller for trip filter heading
            $this->start_controls_section(
                'trip_filter_keyword',
                array(
                    'label' => esc_html__('Trips Filter', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );

            // Trip Filter Enable Disable
            $this->add_control(
                'enable_trip_filter_keyword',
                array(
                    'label' => esc_html__('Keyword', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );

            // Trip fact
            $this->add_control(
                'enable_trip_filter_fact',
                array(
                    'label' => esc_html__('Fact', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // Trip type
            $this->add_control(
                'enable_trip_filter_trip_type',
                array(
                    'label' => esc_html__('Trip Type', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // location
            $this->add_control(
                'enable_trip_filter_location',
                array(
                    'label' => esc_html__('Location', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // Price
            $this->add_control(
                'enable_trip_filter_price',
                array(
                    'label' => esc_html__('Price', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // Price range
            $this->add_control(
                'enable_trip_filter_price_range',
                array(
                    'label' => esc_html__('Price Range', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // Trip duration from
            $this->add_control(
                'enable_trip_filter_duration',
                array(
                    'label' => esc_html__('Duration', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-wdigets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
        }

        protected function render()
        {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            $settings = $this->get_settings_for_display();

            $check_keyword = isset($settings['enable_trip_filter_keyword']) && $settings['enable_trip_filter_keyword'] === 'yes';
            $check_fact = isset($settings['enable_trip_filter_fact']) && $settings['enable_trip_filter_fact'] === 'yes';
            $check_trip_type = isset($settings['enable_trip_filter_trip_type']) && $settings['enable_trip_filter_trip_type'] === 'yes';
            $check_location = isset($settings['enable_trip_filter_location']) && $settings['enable_trip_filter_location'] === 'yes';
            $check_price = isset($settings['enable_trip_filter_price']) && $settings['enable_trip_filter_price'] === 'yes';
            $check_price_range = isset($settings['enable_trip_filter_price_range']) && $settings['enable_trip_filter_price_range'] === 'yes';
            $check_duration = isset($settings['enable_trip_filter_duration']) && $settings['enable_trip_filter_duration'] === 'yes';

            $keyword_search       = $check_keyword;
            $fact                 = $check_fact;
            $trip_type_filter     = $check_trip_type;
            $trip_location_filter = $check_location;
            $price_orderby        = $check_price;
            $price_range          = $check_price_range;
            $trip_dates           = $check_duration;

            $defaults = array(
                'keyword_search'       => $keyword_search,
                'fact'                 => $fact,
                'trip_type_filter'     => $trip_type_filter,
                'trip_location_filter' => $trip_location_filter,
                'price_orderby'        => $price_orderby,
                'price_range'          => $price_range,
                'trip_dates'           => $trip_dates,
            );
?>
            <div class="wtwe-trips-filter">
                <div id="wptravel-trips-filter-widget" class="wptravel-trips-filter-widget">
                    <?php
                    wptravel_get_search_filter_form(array('widget' => $defaults));
                    ?>
                </div>
            </div>

        <?php
        }

        protected function content_template()
        {
        ?>
        <# 
            var keyword = settings.enable_trip_filter_keyword === "yes";
            var fact = settings.enable_trip_filter_fact === "yes";
            var trip_type_filter = settings.enable_trip_filter_trip_type === "yes";
            var trip_location_filter = settings.enable_trip_filter_location === "yes";
            var check_price = settings.enable_trip_filter_price === "yes";
            var check_price_range = settings.enable_trip_filter_price_range === "yes";
            var check_duration = settings.enable_trip_filter_duration === "yes";
        #>
            <div class="wtwe-trips-filter">
                <div id="wptravel-trips-filter-widget" class="wptravel-trips-filter-widget">
                    <!-- search filter widget HTML -->
                    <div class="wp-travel-itinerary-items">
                        <div>

                        <# if(keyword) { #>
                            <div class="wp-travel-form-field ">
                                <label for="wp-travel-filter-keyword">
                                    Keyword </label>
                                <input type="text" id="wp-travel-filter-keyword" name="keyword" value="" maxlength="100" data-parsley-maxlength="100" class="wp_travel_search_widget_filters_input6685200987f15">
                            </div>
                        <# } #>

                        <# if(fact) { #>
                            <div class="wp-travel-form-field ">
                                <label for="wp-travel-filter-fact">
                                    Fact </label>
                                <input type="text" id="wp-travel-filter-fact" name="fact" value="" maxlength="100" data-parsley-maxlength="100" class="wp_travel_search_widget_filters_input6685200987f15">
                            </div>
                        <# } #>

                        <# if(trip_type_filter) { #>
                            <div class="wp-travel-form-field ">
                                <label for="itinerary_types">
                                    <?php echo esc_html__( 'Trip Type', 'wt-widgets-elementor' ); ?> </label>
                                <select id="itinerary_types" name="itinerary_types" class="wp_travel_search_widget_filters_input6685200987f15">
                                    <option value=""><?php echo esc_html__( 'All', 'wt-widgets-elementor' ); ?></option>
                                    <option value="beach"><?php echo esc_html__( 'Beach', 'wt-widgets-elementor' ); ?></option>
                                    <option value="historical-monuments"><?php echo esc_html__( 'Historical Monuments', 'wt-widgets-elementor' ); ?></option>
                                    <option value="luxury-life"><?php echo esc_html__( 'Luxury Life', 'wt-widgets-elementor' ); ?></option>
                                    <option value="refreshing"><?php echo esc_html__( 'Refreshing', 'wt-widgets-elementor' ); ?></option>
                                    <option value="sea-food"><?php echo esc_html__( 'Sea Food', 'wt-widgets-elementor' ); ?></option>
                                 
                                </select>
                            </div>
                        <# } #>

                        <# if(trip_location_filter) { #>

                            <div class="wp-travel-form-field ">
                                <label for="travel_locations">
                                    Location </label>
                                <select id="travel_locations" name="travel_locations" class="wp_travel_search_widget_filters_input6685200987f15">
                                    <option value=""><?php echo esc_html__( 'All', 'wt-widgets-elementor' ); ?></option>
                                    <option value="america"><?php echo esc_html__( 'America', 'wt-widgets-elementor' ); ?></option>
                                    <option value="asia"><?php echo esc_html__( 'Asia', 'wt-widgets-elementor' ); ?></option>
                                    <option value="dubai"><?php echo esc_html__( 'Dubai', 'wt-widgets-elementor' ); ?></option>
                                    <option value="europe"><?php echo esc_html__( 'Europe', 'wt-widgets-elementor' ); ?></option>
                                    <option value="france"><?php echo esc_html__( 'France', 'wt-widgets-elementor' ); ?></option>
                                  
                                </select>
                            </div>
                        <# } #>

                        <# if(check_price) { #>

                            <div class="wp-travel-form-field ">
                                <label for="wp-travel-price">
                                    Price </label>
                                <select id="wp-travel-price" name="price" class="wp_travel_search_widget_filters_input6685200987f15">
                                    <option value="--">--</option>
                                    <option value="low_high"><?php echo esc_html__( 'Price low to high', 'wt-widgets-elementor' ); ?></option>
                                    <option value="high_low"><?php echo esc_html__( 'Price high to low', 'wt-widgets-elementor' ); ?></option>
                                </select>
                            </div>
                        <# } #>
                        
                        <# if(check_price_range) { #>

                            <div class="wp-travel-form-field wp-trave-price-range">
                                <label for="amount">
                                    Price Range </label>
                                <input type="text" id="amount" class="price-amount"><input type="hidden" class="wp_travel_search_widget_filters_input6685200987f15 wp-travel-filter-price-min " name="min_price" value="0"><input type="hidden" class="wp_travel_search_widget_filters_input6685200987f15 wp-travel-filter-price-max " name="max_price" value="0">
                                <div class="wp-travel-range-slider ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                                    <div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 0%; width: 100%;"></div><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 0%;"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 100%;"></span>
                                </div>
                            </div>
                        <# } #>
                        <# if(check_duration) { #>

                            <div class="wp-travel-form-field wp-travel-trip-duration">
                                <label for="">
                                <?php echo esc_html__( 'Trip Duration', 'wt-widgets-elementor' ); ?> </label>
                                <span class="trip-duration-calender"><?php echo esc_html__( 'From', 'wt-widgets-elementor' ); ?><input value="" class="wp_travel_search_widget_filters_input6685200987f15" type="text" id="datepicker1" name="trip_start"><label for="datepicker1"><span class="calender-icon"></span></label></span><span class="trip-duration-calender">To<input value="" class="wp_travel_search_widget_filters_input6685200987f15" type="text" id="datepicker2" name="trip_end"><label for="datepicker2"><span class="calender-icon"></span></label></span>
                            </div>
                        <# } #>

                            <div class="wp-travel-search">
                                <!-- need class name as wp_travel_search_widget_filters_input and attribute data-index to submit data -->
                                <input class="wp_travel_search_widget_filters_input6685200987f15" type="hidden" name="_nonce" value="b08282538d">
                                <input class="filter-data-index" type="hidden" data-index="6685200987f15">

                                <input class="wp-travel-widget-filter-view-mode" type="hidden" name="view_mode" data-mode="list" value="list">

                                <input type="hidden" class="wp-travel-widget-filter-archive-url" value="#">
                                <input type="submit" id="wp-travel-filter-search-submit" class="button wp-block-button__link button-primary wp-travel-filter-search-submit" value="Search">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

<?php
        }
    }
}
