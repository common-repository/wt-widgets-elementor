<?php

/**
 * This file use to fetch trip include using id or global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Exclude;

/**
 * @uses elementor widget namespce
 */

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use WP_Travel_Itinerary;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * create class for register widget
 * 
 */
if (!class_exists('WTWE_Trip_Exclude')) {
    class WTWE_Trip_Exclude extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-exclude';
        }
        // Create title of trip-exclude widget name
        public function get_title()
        {
            return esc_html__('Trip Excludes', 'wt-widgets-elementor');
        }
        // set icon 
        public function get_icon()
        {
            return 'eicon-post-excerpt';
        }
        // set widget under the wp-travel category widgets
        public function get_categories()
        {
            return ['wp-travel-single'];
        }

        // Register and setup control setting for trip outline
        public function _register_controls()
        {

            /**
             * end get trip id section
             * 
             * start exclude style section
             */
            $this->start_controls_section(
                'exclude_style',
                array(
                    'label' => esc_html__('Exclude Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //exclude text color
            $this->add_control(
                'exclude_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor'),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-elementor-widget-trip-exclude' => 'color: {{VALUE}}',
                    ],
                )
            );

            // exclude text typography
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'exclude_content_typography',
                        'label' => 'Typography',
                        'selector' => '{{WRAPPER}} .wp-travel-elementor-widget-trip-exclude',
                    ]
                );
                //exclude text custom css filter
                $this->add_group_control(
                    \Elementor\Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'exclude_custom_css_filters',
                        'label' => 'CSS',
                        'selector' => '{{WRAPPER}} .wp-travel-elementor-widget-trip-exclude',
                    ]
                );
                // exclude text strock
                $this->add_group_control(
                    \Elementor\Group_Control_Text_Stroke::get_type(),
                    [
                        'name' => 'exclude_text_stroke',
                        'selector' => '{{WRAPPER}} .wp-travel-elementor-widget-trip-exclude',
                    ]
                );
            }

            $this->end_controls_section();
        }
        protected function content_template()
        {
?>
            <div class="wtwe-trip-excludes">
                <h5 class="wtwe-trip-excludes"><?php echo esc_html__('Trip Excludes', 'wt-widgets-elementor'); ?></h5>
                <ul class="wp-travel-elementor-widget-trip-exclude" style="list-style: disc;">
                    <li><?php echo esc_html__('Personal Expenses', 'wt-widgets-elementor'); ?></li>
                    <li><?php echo esc_html__('Visa Fees', 'wt-widgets-elementor'); ?></li>
                    <li><?php echo esc_html__('Travel Insurance', 'wt-widgets-elementor'); ?></li>
                    <li><?php echo esc_html__('Airfare', 'wt-widgets-elementor'); ?></li>
                </ul>
            </div>

<?php
        }
        //Show content on frontend trip single page
        protected function render()
        {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            global $post;
            $elementor_setting = $this->get_settings_for_display();
            $trip_id = get_the_id();
            if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
                $exclude_data = get_post_meta(get_the_ID(), 'wp_travel_trip_exclude', true);
                echo '<div class="wp-travel-elementor-widget-trip-exclude" >' . wp_kses_post($exclude_data) . '</div>';
            } else {
                if ($trip_id > 0) {
                    $exclude_data = get_post_meta($trip_id, 'wp_travel_trip_exclude', true);
                    echo '<div class="wp-travel-elementor-widget-trip-exclude" >' . wp_kses_post($exclude_data) . '</div>';
                }
            }
        }
    }
}
