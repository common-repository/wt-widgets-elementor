<?php
/**
 * This file is used to fetch the trip review form.
 */
namespace WTWE\Widgets\Single_Page_Trip_Enquiry;

/**
 * @uses elementor widget namespace
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WTWE\Helper\WTWE_Helper;
use Elementor\Group_Control_Typography;


defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Enquiry')) {
    class WTWE_Trip_Enquiry extends Widget_Base {
        public function __construct($data = [], $args = []) {
            parent::__construct($data, $args);
        }

        public function get_name() {
            return 'wp-travel-trip-enquiry';
        }

        public function get_title() {
            return esc_html__('Trip Enquiry', 'wt-widgets-elementor');
        }

        public function get_icon() {
            return 'eicon-mail';
        }

        public function get_categories() {
            return ['wp-travel-single'];
        }

        public function _register_controls()
        {
            $this->start_controls_section(
                'trip_enquiry_style',
                [
                    'label' => esc_html__('Style', 'wt-widgets-elementor'),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'content_typography',
                        'selector' => '{{WRAPPER}} #wp-travel-send-enquiries',
                    ]
                );
                
                $this->add_control(
                    'trip_enquiry_color',
                    [
                        'label' => esc_html__('Color', 'wt-widgets-elementor'),
                        'type'  => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} #wp-travel-send-enquiries' => 'color:{{VALUE}}',
                        ],
                    ]
                );
                // Add remaining style controls here
                $this->end_controls_section();
            } else {
                $this->add_control(
                    'trip_enquiry_color',
                    [
                        'label' => esc_html__('Color', 'wt-widgets-elementor'),
                        'type'  => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} #wp-travel-send-enquiries' => 'color:{{VALUE}}',
                        ],
                    ]
                );
                // Add remaining style controls here
                $this->end_controls_section();
                
            }
           
        }

        protected function render() {

            global $post;
            $trip_id = get_the_ID();
	        $show_trip_dropdown = "itineraries" === $post->post_type ? false : true;

            ?>
                <a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
                    <span class="wp-travel-booking-enquiry">
                        <span class="dashicons dashicons-editor-help"></span>
                        <span><?php echo esc_html__( 'Trip Enquiry', 'wt-widgets-elementor' ); ?></span>
                    </span>
                </a>
                <div id="wptravel-trip-enquiry-widget" class="wptravel-trip-enquiry-widget">
                <?php 
                    if ( function_exists( 'wptravel_get_enquiries_form' ) ) {
                        wptravel_get_enquiries_form( $show_trip_dropdown );
                    } else {
                        wp_travel_get_enquiries_form( $show_trip_dropdown );
                    }
                ?>
                </div>
                <style>
                    #wptravel-trip-enquiry-widget{
                        display: none;
                    }
                </style>
            <?php
        }
    }
}