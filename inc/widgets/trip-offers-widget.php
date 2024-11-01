<?php

/**
 * This file use to fetch trip offer banner using id or global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Offers;

/**
 * @uses elementor widget namespce
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use WP_Travel_Helpers_Trips;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * create class for register widget
 * 
 */
if (!class_exists('WTWE_Trip_Offers')) {
    class WTWE_Trip_Offers extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
            $prefixed = defined(WP_DEBUG) ? '.min' : '';
            wp_register_style('trip-offers', plugins_url('assets/css/trip-offers' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-offers';
        }
        // Create title of trip-offers widget name
        public function get_title()
        {
            return esc_html__('Trip Offers', 'wt-widgets-elementor');
        }
        // set icon 
        public function get_icon()
        {
            return 'eicon-woo-cart';
        }
        // set widget under the wp-travel category widgets
        public function get_categories()
        {
            return ['wp-travel-single'];
        }
        /**
         * Enqueue styles.
         */
        public function get_style_depends()
        {
            return array('trip-offers');
        }
        // Register and setup control setting for trip outline
        public function _register_controls()
        {
            // Content Tab
            $this->start_controls_section(
                'section_content',
                [
                    'label' => esc_html__('Content', 'wt-widgets-elementor'),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'trip_offer_round_off',
                [
                    'label' => esc_html__('Round off Decimals', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('No', 'wt-widgets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );


            $this->end_controls_section();


            // Style Tab
            $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Style', 'wt-widgets-elementor'),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'trip_offer_color',
                [
                    'label' => esc_html__('Color', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors'  => [
                        '{{WRAPPER}} .wp-travel-offer span' => 'color: {{VALUE}};',

                    ]
                ]
            );

            $this->add_control(
                'trip_offer_bg_color',
                [
                    'label' => esc_html__('Backgorund Color', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors'  => [
                        '{{WRAPPER}} .wp-travel-offer span' => 'background-color: {{VALUE}};',

                    ]
                ]
            );

            $this->add_control(
                'trip_offers',
                [
                    'label'     => esc_html__('Trip Offer', 'wt-widgets-elementor'),
                    'type'      => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'name'      => 'trip_offers_heading_typography',
                        'selector'  => '{{WRAPPER}} .wtwe-trip-offers-wrapper .wp-travel-offer span',
                    )
                );
            } else {
                // WP Travel Pro is not active, display information to activate it
                $this->add_control(
                    'inactive_message',
                    [
                        'label' => esc_html__('WP Travel Pro is not active', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => esc_html__('Please activate the WP Travel Pro plugin to access the style options.', 'wt-widgets-elementor'),
                        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                    ]
                );
            }


            $this->add_responsive_control(
                'width',
                [
                    'label' => esc_html__('Width', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px', '%', 'em', 'rem', 'vw'],
                    'range' => [
                        '%' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                        'px' => [
                            'min' => 1,
                            'max' => 1000,
                        ],
                        'vw' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-trip-offers-wrapper .wp-travel-offer span, 
                        {{WRAPPER}} .wtwe-trip-offers-wrapper .wp-travel-offer' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 100,
                    ],
                    'tablet_default' => [
                        'unit' => 'px',
                        'size' => 100,
                    ],
                    'mobile_default' => [
                        'unit' => 'px',
                        'size' => 100,
                    ],
                ]
            );




            $this->add_responsive_control(
                'height',
                [
                    'label'          => esc_html__('Height', 'wt-widgets-elementor'),
                    'type'           => Controls_Manager::SLIDER,
                    'size_units'     => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                    'range'          => [
                        '%'  => [
                            'min' => 1,
                            'max' => 100,
                        ],
                        'px' => [
                            'min' => 1,
                            'max' => 1000,
                        ],
                        'vw' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                    ],
                    'selectors'      => [
                        '{{WRAPPER}} .wtwe-trip-offers-wrapper .wp-travel-offer span' => 'height: {{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .wtwe-trip-offers-wrapper .wp-travel-offer' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'default'        => [
                        'unit'  => 'px',
                        'size'  => 50,
                    ],
                    'tablet_default' => [
                        'unit'  => 'px',
                        'size'  => 50,
                    ],
                    'mobile_default' => [
                        'unit'  => 'px',
                        'size'  => 50,
                    ],
                ]
            );
            // Add remaining style controls here
            $this->end_controls_section();
        }


        //Show content on frontend trip single page
        protected function render()
        {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }

            global $post;
            $settings = $this->get_settings_for_display();
            $trip_id = get_the_ID();
            $sale_price = \WP_Travel_Helpers_Pricings::get_price(['trip_id' => $trip_id]);
            $regular_price = \WP_Travel_Helpers_Pricings::get_price(['trip_id' => $trip_id, 'is_regular_price' => true]);
            $is_sale = WP_Travel_Helpers_Trips::is_sale_enabled(['trip_id' => $trip_id, 'from_price_sale_enable' => true]);
            $enable_sale = WP_Travel_Helpers_Trips::is_sale_enabled(array('trip_id' => $trip_id));

            if (!empty($trip_id) && $trip_id > 0 && get_post_type($trip_id) == 'itineraries') {

                if ($enable_sale) {
                    $discount = ((float)$regular_price - (float)$sale_price) / (float)$regular_price * 100;
?>
                    <div class="wtwe-trip-offers-wrapper">
                        <div class="wptravel-trip-offer-widget wp-travel-offer">
                            <?php if (!empty($settings['trip_offer_round_off']) && $settings['trip_offer_round_off'] === 'yes') : ?>
                                <span>
                                    <?php echo esc_html(round($discount, 0)) . esc_html__(' % off', 'wt-widgets-elementor'); ?>
                                </span>
                            <?php else : ?>
                                <span>
                                    <?php echo esc_html(round($discount, 2)) . esc_html__(' % off', 'wt-widgets-elementor'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

            <?php
                }
            }
        }

        protected function content_template()
        {
            ?>
            <# var tripOfferRoundOff=settings.trip_offer_round_off==='yes' ; #>
                <div class="wtwe-trip-offers-wrapper">
                    <div class="wptravel-trip-offer-widget wp-travel-offer">
                        <# if (tripOfferRoundOff) { #>
                            <span>
                                <?php echo __('10 % off', 'wt-widgets-elementor'); ?>
                            </span>
                            <# } else { #>
                                <span>
                                    <?php echo __('10.5 % off', 'wt-widgets-elementor'); ?>
                                </span>
                                <# } #>
                    </div>
                </div>
    <?php
        }
    }
}
