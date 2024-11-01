<?php

/**
 * This file use to fetch trip wishlist using global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Wishlist;

/**
 * @uses elementor widget namespce
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use \WTWE\Helper\WTWE_Helper;


// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * create class for register widget
 * 
 */
if (!class_exists('WTWE_Trip_Wishlist')) {
    class WTWE_Trip_Wishlist extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
            $prefixed = defined(WP_DEBUG) ? '.min' : '';
            wp_register_style('trip-wishlist', plugins_url('assets/css/trip-wishlist' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
        }

        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-wishlist';
        }
        // Create title of trip-wishlist widget name
        public function get_title()
        {
            return esc_html__('Trip Wishlist', 'wt-widgets-elementor');
        }
        // set icon 
        public function get_icon()
        {
            return 'eicon-facebook-like-box';
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
            return array('trip-wishlist');
        }

        // Register and setup control setting for trip wishlist
        public function _register_controls()
        {
            // Style Tab
            $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'content_typography',
                    'selector' => '{{WRAPPER}} .wtwe-trip-wishlist i',
                ]
            );

            $this->add_control(
                'wishlist_icon_color',
                [
                    'label' => esc_html__('Wishlist Icon Color', 'wt-widgets-elementor'),
                    'type'  => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-trip-wishlist i' => 'color: {{VALUE}}',
                    ],
                ]
            );
            // Add remaining style controls here
            $this->end_controls_section();
        }

        protected function content_template()
        {
            global $post;
            $post_id = get_the_ID();

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php') && class_exists( 'WP_Travel_Wishlists_Core' )) {
                
            ?>
                <div class="wtwe-trip-wishlist-wrapper">
                    <span class="wtwe-trip-wishlist"><?php wp_travel_wishlists_show_button($post_id); ?></span>
                </div>
            <?php
            } else {
                WTWE_Helper::wtwe_get_widget_notice( __( 'WP Travel Wishlist module is not active.', 'wt-widgets-elementor' ), 'info');
            }
        }

        protected function render()
        {
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php' )&& class_exists( 'WP_Travel_Wishlists_Core' )) {
                global $post;
                $trip_id = get_the_ID();
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    if ($post && 'itineraries' === $post->post_type) {

                        ?><div class="wtwe-trip-wishlist-wrapper">
                                <div class="wtwe-trip-wishlist">
                                    <?php wp_travel_wishlists_show_button($trip_id); ?>
                                </div>
                        </div><?php
                            } else {
                                echo esc_html__('This widget works on single trip page only', 'wt-widgests-elementor');
                            }
                        }
                    } 
                }
            }
        }
