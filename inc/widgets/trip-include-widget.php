<?php

/**
 * This file use to fetch trip include using id or global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Include;

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
if (!class_exists('WTWE_Trip_Include')) {
    class WTWE_Trip_Include extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-include';
        }
        // Create title of trip-include widget name
        public function get_title()
        {
            return esc_html__('Trip Includes', 'wt-widgets-elementor');
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
             * start include style section
             */
            $this->start_controls_section(
                'include_style',
                array(
                    'label' => esc_html__('Include Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //include text color
            $this->add_control(
                'include_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor'),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-elementor-widget-trip-include' => 'color: {{VALUE}}',
                    ],
                )
            );
            // include text typography
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'include_content_typography',
                        'label' => 'Typography',
                        'selector' => '{{WRAPPER}} .wp-travel-elementor-widget-trip-include',
                    ]
                );
                //include text custom css filter
                $this->add_group_control(
                    \Elementor\Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'include_custom_css_filters',
                        'label' => 'CSS',
                        'selector' => '{{WRAPPER}} .wp-travel-elementor-widget-trip-include',
                    ]
                );
                // include text strock
                $this->add_group_control(
                    \Elementor\Group_Control_Text_Stroke::get_type(),
                    [
                        'name' => 'include_text_stroke',
                        'selector' => '{{WRAPPER}} .wp-travel-elementor-widget-trip-include',
                    ]
                );
            }

            $this->end_controls_section();
        }
        protected function content_template()
        {
        
?>
            <div class="wtwe-trip-includes">
                <h5 class="wtwe-trip-includes"><?php echo esc_html__('Trip Includes', 'wt-widgets-elementor'); ?></h5>
                <ul class="wp-travel-elementor-widget-trip-include" style="list-style: disc;">
                    <li><?php echo esc_html__('Activities and excursions', 'wt-widgets-elementor'); ?></li>
                    <li><?php echo esc_html__('Professional tour guide', 'wt-widgets-elementor'); ?></li>
                    <li><?php echo esc_html__('Entrance fees', 'wt-widgets-elementor'); ?></li>
                    <li><?php echo esc_html__('Insurance coverage', 'wt-widgets-elementor'); ?></li>
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
            $trip_id = get_the_ID();
            if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
                $include_data = get_post_meta(get_the_ID(), 'wp_travel_trip_include', true);
                echo '<div class="wp-travel-elementor-widget-trip-include" >' . wp_kses_post($include_data) . '</div>';
            } else {
                if ($trip_id > 0) {
                    $include_data = get_post_meta($trip_id, 'wp_travel_trip_include', true);
                    echo '<div class="wp-travel-elementor-widget-trip-include" >' . wp_kses_post($include_data) . '</div>';
                }
            }
        }
    }
}
