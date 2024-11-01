<?php

/**
 * Book Button class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_Page_Trip_Book_Button;

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

use WTWE\Helper\WTWE_Helper;


// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Trip Tabs widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Book_Button')) {
    class WTWE_Book_Button extends Widget_Base
    {
        /**
         * Class constructor.
         *
         * @param array $data Widget data.
         * @param array $args Widget arguments.
         */
        public function __construct($data = array(), $args = null)
        {
            parent::__construct($data, $args);
            $prefixed = defined(WP_DEBUG) ? '.min' : '';
            wp_register_style('book-button', plugins_url('assets/css/book-button' . $prefixed . '.css', WTWE_PLUGIN_FILE), []);
            wp_register_script('book-button', plugins_url('assets/js/book-button' . $prefixed . '.js', WTWE_PLUGIN_FILE), []);
        }

        /**
         * Retrieve the widget name.
         *
         * @since 1.0.0
         *
         * @access public
         *
         * @return string Widget name
         */
        public function get_name()
        {
            return 'wp-travel-book-button';
        }

        /**
         * Retrieve the widget title.
         *
         * @since 1.0.0
         *
         * @access public
         *
         * @return string Widget title
         */
        public function get_title()
        {
            return esc_html__('Book Button', 'wt-widgets-elementor');
        }

        /**
         * Retrieve the widget icon.
         *
         * @since 1.0.0
         *
         * @access public
         *
         * @return string Widget icon
         */
        public function get_icon()
        {
            return 'eicon-button';
        }

        /**
         * Retrieve the list of categories the widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * Note that currently Elementor supports only one category.
         * When multiple categories passed, Elementor uses the first one.
         *
         * @since 1.0.0
         *
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
            return array('wp-travel-single');
        }

        /**
         * Enqueue styles.
         */
        public function get_style_depends()
        {
            return array('book-button');
        }

        /**
         * Enqueue scripts.
         * 
         * @since 1.0.0
         *
         * @access public
         *
         * @return array scripts array.
         */
        public function get_script_depends()
        {
            return array('book-button');
        }

        /**
         * Register the widget controls.
         *
         * Adds different input fields to allow the user to change and bookize the widget settings.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function _register_controls()
        {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Content', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'button_text',
                [
                    'label' => __('Button Text', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'description' => esc_html__('Button text currently doesn\'t support On-Page Booking.', 'wt-widgets-elementor'),
                    'default' => __('Book Now', 'wt-widgets-elementor'),
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'style_section',
                [
                    'label' => __('Button Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );


            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'label' => esc_html__('Typography', 'wt-widgets-elementor'),
                        'name' => 'book_button_text_typography',
                        'selector'    => '{{ WRAPPER }} .wp-block-button .wp-block-button__link.wtwe-book-button',
                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Stroke::get_type(),
                    array(
                        'label' => esc_html__('Text Stroke', 'wt-widgets-elementor'),
                        'name' => 'book_button_text_stroke',
                        'selector' => '{{ WRAPPER }} .wp-block-button .wp-block-button__link.wtwe-book-button',
                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    array(
                        'label' => esc_html__('Text Shadow', 'wt-widgets-elementor'),
                        'name' => 'book_button_text_shadow',
                        'selector' => '{{ WRAPPER }} .wp-block-button .wp-block-button__link.wtwe-book-button',
                    )
                );

                $this->start_controls_tabs(
                    'style_tabs'
                );

                $this->start_controls_tab(
                    'style_normal_tab',
                    [
                        'label' => esc_html__('Normal', 'wt-widgets-elementor'),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'book_button_background',
                        'types' => ['classic', 'gradient'],
                        'exclude' => ['image'],
                        'selector' => '{{WRAPPER}} .wtwe-book-button',
                    ]
                );

                $this->add_control(
                    'book_button_text_color',
                    [
                        'label' => __('Text Color', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-book-button' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'book_button_border_radius',
                    [
                        'label' => __('Border Radius', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-book-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'book_button_border',
                        'selector' => '{{WRAPPER}} .wtwe-book-button',
                    ]
                );

                $this->end_controls_tab();

                $this->start_controls_tab(
                    'style_hover_tab',
                    [
                        'label' => esc_html__('Hover', 'wt-widgets-elementor'),
                    ]
                );

                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'book_button_background_hover',
                        'types' => ['classic', 'gradient'],
                        'exclude' => ['image'],
                        'selector' => '{{WRAPPER}} .wtwe-book-button:hover',
                    ]
                );

                $this->add_control(
                    'book_button_text_color_hover',
                    [
                        'label' => __('Text Color', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-book-button:hover' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'book_button_hover_animation',
                    [
                        'label' => esc_html__('Hover Animation', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::HOVER_ANIMATION,
                    ]
                );

                $this->add_responsive_control(
                    'book_button_border_radius_hover',
                    [
                        'label' => __('Border Radius', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-book-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->end_controls_tab();

                $this->end_controls_tab();

                $this->end_controls_section();
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
        }

        /**
         * Render the widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function render()
        {
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {

                $settings = $this->get_settings_for_display();

                $elementClass = 'wptravel-blocks-single-trip-book-button wp-block-button__link wtwe-book-button';
                if ( isset( $settings['book_button_hover_animation'] )) {
                    $elementClass .= ' elementor-animation-' . $settings['book_button_hover_animation'];
                }
                $this->add_render_attribute('wrapper', 'class', $elementClass);

                if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
                    if (isset( wptravel_get_settings()['enable_one_page_booking'] ) && wptravel_get_settings()['enable_one_page_booking'] !== '1') { ?>
                        <div id="wptravel-blocks-book-button" class="wtwe-book-trip">
                            <a class="wptravel-blocks-book-btn wp-block-button" id="trip-booking" href="#booking" rel="noopener noreferrer">
                                <button <?php echo esc_attr( $this->get_render_attribute_string('wrapper') ); ?>>
                                    <?php echo esc_html($settings['button_text']); ?>
                                </button>
                            </a>
                        </div>
                    <?php } else { ?>
                        <div id="wptravel-blocks-book-button" class="wtwe-book-trip">
                            <div id="wp-travel-one-page-checkout-enables" class="wp-block-button">
                                <button <?php echo esc_attr( $this->get_render_attribute_string('wrapper') ); ?>><?php echo esc_html__('Book Now', 'wt-widgets-elementor'); ?></button>
                            </div>
                        </div>
            <?php }
                } else {
                    WTWE_Helper::wtwe_get_widget_notice( esc_html__( 'Only works on Trip page.', 'wt-widgets-elementor' ), 'info');
                }
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
            <div id="wptravel-blocks-book-button" class="wtwe-book-trip">
                <a class="wptravel-blocks-book-btn wp-block-button" id="trip-booking" href="" rel="noopener noreferrer">
                    <button class="wtwe-book-button wp-block-button__link">{{{ settings.button_text }}}</button>

                </a>
            </div>
<?php
        }
    }
}
