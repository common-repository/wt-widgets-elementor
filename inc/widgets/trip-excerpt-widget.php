<?php

/**
 * This file use to fetch trip excerpt using global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Excerpt;

/**
 * @uses elementor widget namespce
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

use WP_Travel_Itinerary;


// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * create class for register widget
 * 
 */
if (!class_exists('WTWE_Trip_Excerpt')) {
    class WTWE_Trip_Excerpt extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-excerpt';
        }
        // Create title of trip-title widget name
        public function get_title()
        {
            return esc_html__('Trip Excerpt', 'wt-widgets-elementor');
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
  

            // Style Tab
            $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
            // Add remaining style controls here


            $this->add_control(
                'trip_title_style',
                [
                    'label' => esc_html__('Trip Excerpt', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                array(
                    'name' => 'trip_excerpt_heading_typography',
                    'selector'    => '{{ WRAPPER }} .wtwe-trip-excerpt',
                )
            );

            $this->add_control(
                'trip_title_color',
                [
                    'label' => esc_html__('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-trip-excerpt' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'trip_title_alignment',
                [
                    'label' => esc_html__('Alignment', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left', 'wt-widgets-elementor'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center', 'wt-widgets-elementor'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__('Right', 'wt-widgets-elementor'),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-trip-excerpt' => 'text-align: {{VALUE}};',
                    ],
                ]
            );


            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow',
                    'selector' => '{{WRAPPER}} .wtwe-trip-excerpt',
                ]
            );

            // Add remaining style controls here


            $this->end_controls_section();
        }

        protected function content_template()
        { ?>
                <div class="wtwe-trip-excerpt"><?php echo esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'wt-widgets-elementor' ); ?></div>
                <?php
        }
        //Show content on frontend trip single page
        protected function render()
        {
            global $post;
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                if ($post && 'itineraries' === $post->post_type) {
                    // $settings = $this->get_settings_for_display();
                    $excerpt = $post->post_excerpt;
                    if (!$excerpt) {
                        $excerpt = $post->post_content;
                    }

                    $excerpt = apply_filters('the_excerpt', $excerpt);
                    // $excerpt = str_replace(']]>', ']]&gt;', $excerpt);
                    // $excerpt = wp_trim_words($excerpt, 25, '...'); // Limit the excerpt to 25 words
                    echo '<div class="wtwe-trip-excerpt">' . wp_kses_post($excerpt) . '</div>';

                    // echo '<div class="wtwe-trip-excerpt">' . esc_html( wp_html_excerpt($excerpt, $settings['excerpt_length'], '...') ) . '</div>';
                } else {
                    echo "WP Travel not activated";
                }
            }
        }
    }
}
