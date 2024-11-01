<?php

/**
 * This file use to fetch trip overview using id or global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Overview;

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
if (!class_exists('WTWE_Trip_Overviews')) {
    class WTWE_Trip_Overviews extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-overview';
        }
        // Create title of trip-overview widget name
        public function get_title()
        {
            return esc_html__('Trip Overview', 'wt-widgets-elementor');
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

        // Register and setup control setting for trip overview
        public function _register_controls()
        {

            $this->start_controls_section(
                'overview_style',
                array(
                    'label' => esc_html__('Overview Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            $this->add_control(
                'text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor'),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-best-practice' => 'color: {{VALUE}}',
                    ],
                )
            );

            $this->add_control(
                'text-align',
                [
                    'label' => esc_html__('Text Alignment', 'wt-widgets-elementor'),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
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
                        'justify' => [
                            'title' => esc_html__( 'Justify', 'wt-widgets-elementor' ),
                            'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'default' => 'left',
                    'selectors' => [
                        '{{ WRAPPER }} .wp-best-practice' => 'text-align: {{VALUE}}',
                    ],
                ]
            );


            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'content_typography',
                        'label' => esc_html__('Text Font', 'wt-widgets-elementor'),
                        'selector' => '{{WRAPPER}} .wp-best-practice',
                    ]
                );
                $this->add_group_control(
                    \Elementor\Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'custom_css_filters',
                        'label' => esc_html__('CSS', 'wt-widgets-elementor'),
                        'selector' => '{{WRAPPER}} .wp-best-practice',
                    ]
                );
                $this->add_group_control(
                    \Elementor\Group_Control_Text_Stroke::get_type(),
                    [
                        'name' => 'text_stroke',
                        'selector' => '{{WRAPPER}} .wp-best-practice',
                    ]
                );
            }

            $this->end_controls_section();
        }
        //Show content on frontend trip single page
        protected function render()
        {

            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }

            global $post;
            $elementor_setting = $this->get_settings_for_display();
            $textColors = isset($elementor_setting['text_color']) ? $elementor_setting['text_color'] : '#000000';

            if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
                $overview_data  = new WP_Travel_Itinerary(get_post(get_the_ID()));
                $overview_content =  $overview_data->get_content() ? apply_filters('wp_travel_trip_overview_widgets', $overview_data->get_content(), get_the_ID()) : '';
?>
                <div class="wp-best-practice">
                    <?php echo wp_kses_post($overview_content); ?>
                </div>
            <?php

            }
        }

        /**
         * JS Render for widget.
         */
        protected function content_template()
        {
            if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
                $overview_data  = new WP_Travel_Itinerary(get_post(get_the_ID()));
                $overview_content =  $overview_data->get_content() ? apply_filters('wp_travel_trip_overview_widgets', $overview_data->get_content(), get_the_ID()) : '';
            ?>
                <div class="wtwe-trip-overview">
                    <h5 class="wtwe-trip-overview"><?php echo esc_html__('Trip Overview', 'wt-widgets-elementor'); ?></h5>
                </div>

                <div class="wp-best-practice">
                    <?php echo esc_html__('The "Trip Overview Widget" is a widget to provide users with a concise and informative summary of their trips. With this widget, users can effortlessly access essential details about their travel plans, including itineraries, destinations, and key highlights.', 'wt-widgets-elementor'); ?>
                </div>
<?php

            }
        }
    }
}
