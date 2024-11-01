<?php

/**
 * Hero Slider class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

use WP_Travel_Helpers_Pricings;


// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Trip Search widget class.
 *
 * @since 1.0.0
 */

if (!class_exists('WTWE_Hero_Slider')) {
    class WTWE_Hero_Slider extends Widget_Base
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
            wp_register_style('hero-slider', plugins_url('assets/css/hero-slider' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
            add_action('elementor/frontend/before_enqueue_scripts', array($this, 'enqueue_slick_scripts'));
            add_action('elementor/frontend/after_register_scripts', array($this, 'enqueue_slick_scripts'));
            add_action('elementor/editor/after_enqueue_styles', array($this, 'enqueue_slick_scripts'));

            add_action('elementor/frontend/after_enqueue_scripts', array($this, 'enqueue_slick_init_script'));
        }

        public function enqueue_slick_scripts()
        {
            wp_enqueue_style('slick-css', plugins_url('assets/libs/slick/slick.css', WTWE_PLUGIN_FILE), array()); // phpcs:ignore
            wp_enqueue_style('slick-theme', plugins_url('assets/libs/slick/slick-theme.css', WTWE_PLUGIN_FILE), array());
            wp_enqueue_script('slick-min-js', plugins_url('assets/libs/slick/slick.min.js', WTWE_PLUGIN_FILE), array(), true);
        }

        public function enqueue_slick_init_script()
        {
            $prefixed = defined(WP_DEBUG) ? '.min' : '';
            wp_enqueue_script('slick-init', plugins_url('assets/js/slick-init' . $prefixed . '.js', WTWE_PLUGIN_FILE), array(), true);
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
            return 'wp-travel-hero-slider';
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
            return esc_html__('Hero Slider', 'wt-widgets-elementor');
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
            return 'eicon-post-slider';
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
            return array('wp-travel');
        }

        /**
         * Enqueue styles.
         */
        public function get_style_depends()
        {
            return array('hero-slider');
        }


        /**
         * Enqueue scripts.
         */
        public function get_script_depends()
        {
            return array('jquery', 'slick-min', 'slick-init');
        }

        /**
         * Get terms from taxonomy
         * 
         * @since 1.0.0
         * 
         * @access public
         * 
         * @return array terms array
         */
        public function wtwe_handle_content_type($content_type)
        {
            $content = array();
            $terms     = get_terms(
                array(
                    'taxonomy'   => $content_type,
                    'hide_empty' => false,
                )
            );

            if (is_array($terms) && count($terms) > 0) {
                foreach ($terms as $key => $term) {
                    $slug             = !empty($term->slug) ? $term->slug : '';
                    $content[$slug] = !empty($term->name) ? $term->name : '';
                }
            }

            return $content;
        }

        /**
         * Register the widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function _register_controls()
        {
            $get_trips = get_posts(array(
                'post_type'        => 'itineraries',
                'numberposts'      => -1
            ));

            $trips_ids = array();
            foreach ($get_trips as $trip) {
                $trips_ids[$trip->ID] = $trip->post_title;
            }

            $this->start_controls_section(
                'slider_content',
                array(
                    'label' => esc_html__('Slider', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::TAB_CONTENT,
                ),
            );
            $this->add_responsive_control(
                'hero_slider_design',
                [
                    'label' => esc_html__('Design', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'design-1',
                    'options' => [
                        'design-1' => esc_html__("Design 1", 'wt-widgets-elementor'),
                        'design-2' => esc_html__("Design 2", 'wt-widgets-elementor'),
                    ]
                ]
            );

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'content_type',
                    array(
                        'label' => esc_html__('Content Type', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'trips_ids',
                        'options' => array(
                            'trips_ids' => esc_html__('Trips', 'wt-widgets-elementor'),
                            'featured_trips' => esc_html__('Featured Trips', 'wt-widgets-elementor'),
                            'itinerary_types' => esc_html__('Trip Types', 'wt-widgets-elementor'),
                            'travel_locations' => esc_html__('Trip Destinations', 'wt-widgets-elementor'),
                            'activity' => esc_html__('Activity', 'wt-widgets-elementor'),
                        ),
                    )
                );
            } else {
                $this->add_control(
                    'content_type',
                    array(
                        'label' => esc_html__('Content Type', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'trips_ids',
                        'options' => array(
                            'trips_ids' => esc_html__('Trips', 'wt-widgets-elementor'),
                        ),
                    )
                );
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

            // $this->add_control(
            //     'content_type',
            //     array(
            //         'label' => esc_html__('Content Type', 'wt-widgets-elementor'),
            //         'type' => Controls_Manager::SELECT,
            //         'default' => 'trips_ids',
            //         'options' => array(
            //             'trips_ids' => esc_html__('Trips', 'wt-widgets-elementor'),
            //             'featured_trips' => esc_html__('Featured Trips', 'wt-widgets-elementor'),
            //             'itinerary_types' => esc_html__('Trip Types', 'wt-widgets-elementor'),
            //             'travel_locations' => esc_html__('Trip Destinations', 'wt-widgets-elementor'),
            //             'activity' => esc_html__('Activity', 'wt-widgets-elementor'),
            //         ),
            //     )
            // );

            $this->add_control(
                'trips_ids',
                array(
                    'label' => esc_html__('Select Trips', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'default' => array_keys(array_slice($trips_ids, 0, 3, true)),
                    'options' => $trips_ids,
                    'condition' => array(
                        'content_type' => 'trips_ids',
                    ),
                )
            );

            $this->add_control(
                'trips_type',
                array(
                    'label' => esc_html__('Select Trips Type', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => array_key_first(\WTWE\Widgets\WTWE_Hero_Slider::wtwe_handle_content_type('itinerary_types')),
                    'options' => \WTWE\Widgets\WTWE_Hero_Slider::wtwe_handle_content_type('itinerary_types'),
                    'condition' => array(
                        'content_type' => 'itinerary_types',
                    ),
                )
            );

            $this->add_control(
                'trips_destination',
                array(
                    'label' => esc_html__('Select Trips Destination', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => array_key_first(\WTWE\Widgets\WTWE_Hero_Slider::wtwe_handle_content_type('travel_locations')),
                    'options' => \WTWE\Widgets\WTWE_Hero_Slider::wtwe_handle_content_type('travel_locations'),
                    'condition' => array(
                        'content_type' => 'travel_locations',
                    ),
                )
            );

            $this->add_control(
                'trips_activity',
                array(
                    'label' => esc_html__('Select Trips Activity', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => array_key_first(\WTWE\Widgets\WTWE_Hero_Slider::wtwe_handle_content_type('activity')),
                    'options' => \WTWE\Widgets\WTWE_Hero_Slider::wtwe_handle_content_type('activity'),
                    'condition' => array(
                        'content_type' => 'activity',
                    ),
                )
            );

            $this->add_responsive_control(
                'trips_count',
                array(
                    'label' => esc_html__('Trips To Show', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 3,
                    'min' => 1,
                    'max' => 20,
                    'step' => 1,
                    'condition' => array(
                        'content_type!' => 'trips_ids'
                    ),
                ),
            );

            $this->add_control(
                'autoplay',
                array(
                    'label' => esc_html__('Autoplay', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('On', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Off', 'wt-widgets-elementor'),
                    'default' => 'yes',
                    'condition' => [
                        'hero_slider_design' => 'design-2',
                    ]
                ),
            );

            $this->add_control(
                'autpplay_slider_speed',
                array(
                    'label' => esc_html__('Autoplay Speed', 'wt-widgets-elementor'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['ms'],
                    'range' => [
                        'ms' => [
                            'min' => 1000,
                            'max' => 10000,
                            'step' => 1000,
                        ],
                    ],
                    'default' => [
                        'unit' => 'ms',
                        'size' => 3000,
                    ],
                    'condition' => [
                        'hero_slider_design' => 'design-2',
                        'autoplay' => 'yes',

                    ]
                ),
            );

            $this->add_responsive_control(
                'slider_container_max_width',
                array(
                    'label' => esc_html__('Container Max Width', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => array(
                        'unit' => 'px',
                    ),
                    'size_units' => array('px', 'rem', 'vh'),
                    'range' => array(
                        'px' => array(
                            'min' => 0,
                            'max' => 4000,
                            'step' => 1,
                        ),
                        '%' => array(
                            'min' => 0,
                            'max' => 500,
                            'step' => 1,
                        ),
                        'rem' => array(
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ),
                        'vh' => array(
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ),
                    ),
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-main-slider .wtwe-widgets-design-2-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',

                    ],
                    'condition' => [
                        'hero_slider_design' => 'design-2',
                    ]
                ),
            );

            $this->add_responsive_control(
                'slider_max_width',
                array(
                    'label' => esc_html__('Content Max Width', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => array(
                        'unit' => 'px',
                    ),
                    'size_units' => array('px', 'rem', 'vh'),
                    'range' => array(
                        'px' => array(
                            'min' => 0,
                            'max' => 4000,
                            'step' => 1,
                        ),
                        '%' => array(
                            'min' => 0,
                            'max' => 500,
                            'step' => 1,
                        ),
                        'rem' => array(
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ),
                        'vh' => array(
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ),
                    ),
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-main-slider .wtwe-hero-slider-wrapper .wtwe-hero-slider-content' => 'max-width: {{SIZE}}{{UNIT}};',

                    ],
                    'condition' => [
                        'hero_slider_design' => 'design-2',
                    ]
                ),
            );

            $this->add_responsive_control(
                'slider_height',
                array(
                    'label' => esc_html__('Height', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => array(
                        'unit' => 'px',
                    ),
                    'size_units' => array('px', 'rem', 'vh'),
                    'range' => array(
                        'px' => array(
                            'min' => 250,
                            'max' => 1200,
                            'step' => 1,
                        ),
                        'rem' => array(
                            'min' => 15,
                            'max' => 50,
                            'step' => 1,
                        ),
                        'vh' => array(
                            'min' => 30,
                            'max' => 100,
                            'step' => 1,
                        ),
                    ),
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.design-1 .slick-track' => 'height: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.design-1 .slick-track img' => 'height: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .slick-track' => 'height: {{SIZE}}{{UNIT}};',

                    ],
                ),
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'slider_content_section',
                array(
                    'label' => esc_html__('Content', 'wt-widgets-elementor'),
                )
            );

            $this->add_control(
                'show_slider_content',
                [
                    'label' => esc_html__('Show Content', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                ]
            );

            $this->add_responsive_control(
                'slider_content_alignment',
                array(
                    'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => array(
                        'left:5rem;right:unset;'   => array(
                            'title' => esc_html__('Left', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-left',
                        ),
                        'left:25%;right:25%;' => array(
                            'title' => esc_html__('Default', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-center',
                        ),
                        'right:5rem;left:unset;'  => array(
                            'title' => esc_html__('Right', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-right',
                        ),
                    ),
                    'default'   => 'left:5rem;right:unset;',
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content' => '{{VALUE}};',
                    ),
                    'condition' => [
                        'hero_slider_design' => 'design-1',
                    ]
                )
            );

            // $this->add_responsive_control(
            //     'slider_content_alignment-design-2',
            //     array(
            //         'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
            //         'type'      => Controls_Manager::CHOOSE,
            //         'options'   => array(
            //             'left:5rem;right:unset;'   => array(
            //                 'title' => esc_html__('Left', 'wt-widgets-elementor'),
            //                 'icon'  => 'fa fa-align-left',
            //             ),
            //             'default:unset;right:unset;' => array(
            //                 'title' => esc_html__('Default', 'wt-widgets-elementor'),
            //                 'icon'  => 'fa fa-align-center',
            //             ),
            //             'right:5rem;left:unset;'  => array(
            //                 'title' => esc_html__('Right', 'wt-widgets-elementor'),
            //                 'icon'  => 'fa fa-align-right',
            //             ),
            //         ),
            //         'default'   => 'left:17%;right:unset;',
            //         'selectors' => array(
            //             '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content' => '{{VALUE}};',
            //         ),
            //         'condition' => array(
            //             'hero_slider_design' => 'design-2',
            //         ),
            //     )
            // );

            $this->add_responsive_control(
                'slider_content_max_width',
                [
                    'label' => esc_html__('Max Width', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px', '%', 'em'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 2000,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 1000,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content' => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => array(
                        'hero_slider_design' => 'design-1',
                    ),
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'title_section',
                array(
                    'label' => esc_html__('Title', 'wt-widgets-elementor'),
                )
            );

            $this->add_control(
                'show_trip_title',
                [
                    'label' => esc_html__('Show Title', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                ]
            );
            $this->add_control(
                'show_trip_location-design-2',
                [
                    'label' => esc_html__('Show Location', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                    'condition' => array(
                        'hero_slider_design' => 'design-2',
                    ),
                ]
            );

            $this->add_control(
                'show_wtwe_hline_2',
                [
                    'label' => esc_html__('Show Title Line', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                    'condition' => array(
                        'hero_slider_design' => 'design-2',
                    ),
                ]
            );

            $this->add_control(
                'hline_background_color',
                [
                    'label' => esc_html__('Line Background Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-widgets-design-2-wrapper .wtwe-hline' => 'background-color: {{VALUE}};',
                    ],
                    'condition' => array(
                        'hero_slider_design' => 'design-2',
                    ),
                ]
            );
            

            $this->add_control(
                'title_alignment',
                array(
                    'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => array(
                        'left'   => array(
                            'title' => esc_html__('Left', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-left',
                        ),
                        'center' => array(
                            'title' => esc_html__('Center', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-center',
                        ),
                        'right'  => array(
                            'title' => esc_html__('Right', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-right',
                        ),
                    ),
                    'default'   => 'left',
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-title' => 'text-align: {{VALUE}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-content .wtwe-hero-slider-title-design-2 h2' => 'text-align: {{VALUE}};',

                    ),
                )
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'excerpt_section',
                array(
                    'label' => esc_html__('Excerpt', 'wt-widgets-elementor'),
                )
            );

            $this->add_control(
                'show_trip_excerpt',
                [
                    'label' => esc_html__('Show Excerpt', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'excerpt_alignment',
                array(
                    'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => array(
                        'left'   => array(
                            'title' => esc_html__('Left', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-left',
                        ),
                        'center' => array(
                            'title' => esc_html__('Center', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-center',
                        ),
                        'right'  => array(
                            'title' => esc_html__('Right', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-right',
                        ),
                    ),
                    'default'   => 'left',
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-body' => 'text-align: {{VALUE}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-content .wtwe-hero-slider-body' => 'text-align: {{VALUE}};',

                    ),
                    'condition' => array(
                        'show_trip_excerpt' => 'yes',
                    ),
                )
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'button_section',
                array(
                    'label' => esc_html__('Button', 'wt-widgets-elementor'),
                )
            );

            $this->add_control(
                'show_trip_button',
                [
                    'label' => esc_html__('Show Button', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'show_trip_prices',
                [
                    'label' => esc_html__('Show Trip Price', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'button_alignment',
                array(
                    'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => array(
                        'flex-start'   => array(
                            'title' => esc_html__('Left', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-left',
                        ),
                        'center' => array(
                            'title' => esc_html__('Center', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-center',
                        ),
                        'flex-end'  => array(
                            'title' => esc_html__('Right', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-right',
                        ),
                    ),
                    'default'   => 'flex-start',
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-hero-slider .wtwe-hero-slider-actions' => 'justify-content: {{VALUE}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-price-buy-btn' => 'justify-content: {{VALUE}};',

                    ),
                )
            );

            $this->add_control(
                'button_text',
                array(
                    'label' => 'Button Text',
                    'type' => Controls_Manager::TEXT,
                    'default' => 'Explore',
                    'label_block' => true,
                )
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'arrows_section',
                [
                    'label' => esc_html__('Arrows', 'wt-widgets-elementor'),
                    'condition' => [
                        'hero_slider_design' => 'design-1',
                    ],
                ]
            );

            $this->add_control(
                'show_slider_arrows',
                [
                    'label' => esc_html__('Show Arrows', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    'default' => 'yes',
                    'condition' => [
                    'hero_slider_design' => 'design-1',
                    ]
                ],
                
            );

            $this->add_control(
                'slider_default_arrows',
                [
                    'label' => esc_html__('Default Arrows Position', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                    'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                    // 'default' => 'no',
                    'condition' => [
                        'show_slider_arrows' => 'yes',
                        'hero_slider_design' => 'design-1',
                    ]
                ]
            );

            $this->add_control(
                'arrows_alignment',
                array(
                    'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => array(
                        'left: 5rem;right:unset;'   => array(
                            'title' => esc_html__('Left', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-left',
                        ),
                        'right: 5rem;left:unset;'  => array(
                            'title' => esc_html__('Right', 'wt-widgets-elementor'),
                            'icon'  => 'fa fa-align-right',
                        ),
                    ),
                    'default'   => 'right: 5rem;left:unset;',
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-prev, .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-next ' => '{{VALUE}};',
                    ),
                    'condition' => [
                        'show_slider_arrows' => 'yes',
                        'hero_slider_design' => 'design-1',
                    ]
                )
            );

            $this->end_controls_section();

            // Style Tab

            $this->start_controls_section(
                'title_style_section',
                array(
                    'label' => esc_html__('Title', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'name' => 'title_typography',
                        'selector' => '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-title, 
                        {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.design-2 .wtwe-hero-slider-content .wtwe-hero-slider-title-design-2 h2',
                    ),
                );


                $this->add_group_control(
                    Group_Control_Text_Stroke::get_type(),
                    [
                        'name' => 'title_text_stroke',
                        'selector' => '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-title, 
                        {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-title-design-2 h2',
                    ]
                );


                $this->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    array(
                        'name' => 'title_text_shadow',
                        'selector' => '{{ WRAPPER }} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-title,
                        {{ WRAPPER }} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-title-design-2 h2',
                    )
                );
            }

            $this->add_control(
                'title_text_color',
                array(
                    'label' => esc_html('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-title' => 'color:{{VALUE}};',


                    ],
                    'condition' => array(
                        'hero_slider_design' => 'design-1',
                    ),
                )
            );
            $this->add_control(
                'title_text_color-design-2',
                array(
                    'label' => esc_html('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-title-design-2 h2' => 'color:{{VALUE}};',
                    ],
                    'condition' => array(
                        'hero_slider_design' => 'design-2',
                    ),
                )
            );
            $this->end_controls_section();

            $this->start_controls_section(
                'excerpt_style_section',
                array(
                    'label' => 'Excerpt',
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'name' => 'excerpt_typography_design_two',
                        'selector'    => '{{ WRAPPER }} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-body p',
                        'condition' => array(
                            'hero_slider_design' => 'design-2',
                        ),
                    )
                );
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'name' => 'excerpt_typography_design_one',
                        'selector'    => '{{ WRAPPER }} .wtwe-hero-slider-wrapper.design-1 .wtwe-hero-slider-body p',
                        'condition' => array(
                            'hero_slider_design' => 'design-1',
                        ),

                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Stroke::get_type(),
                    array(
                        'name' => 'excerpt_text_stroke',
                        'selector' => '{{ WRAPPER }} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-body p,
                                       {{ WRAPPER }} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-body p',
                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    array(
                        'name' => 'excerpt_text_shadow',
                        'selector' => '{{ WRAPPER }} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-body p,
                                       {{ WRAPPER }} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-body p',
                    )
                );
            }

            $this->add_control(
                'excerpt_text_color',
                array(
                    'label' => esc_html('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-body p' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-body p' => 'color:{{VALUE}};',
                    ],
                )
            );

            $this->end_controls_section();



            // button styles

            $this->start_controls_section(
                'button_style_section',
                array(
                    'label' => esc_html('Button', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );

            $this->add_control(
                'button_text_color',
                array(
                    'label' => esc_html('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-widgets-design-2-wrapper .wtwe-slider-action-button' => 'color: {{VALUE}};',
                    ),
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'button_padding',
                    [
                        'label' => esc_html__('Padding', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%', 'em', 'rem'],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'button_text_typography',
                        'selector' => '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button,
                                       {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2 .wtwe-slider-action-button',
                    ]
                );


                $this->add_group_control(
                    Group_Control_Text_Stroke::get_type(),
                    array(
                        'name' => 'button_text_stroke',
                        'selector' => '{{ WRAPPER }} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button,
                                       {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2 .wtwe-slider-action-button',
                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    array(
                        'name' => 'button_text_shadow',
                        'selector' => '{{ WRAPPER }} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button,
                        {{ WRAPPER }} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2',
                    )
                );

                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'button_background',
                        'types' => ['classic', 'gradient', 'video'],
                        'selector' => '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button,
                                       {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2 ',
                    ]
                );

                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    array(
                        'name' => 'button_border_type',
                        'selector' => '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button,
                                        {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2',
                    )
                );

                $this->add_control(
                    'button_border_radius',
                    [
                        'label' => esc_html__('Border Radius', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper .wtwe-hero-slider .wtwe-hero-slider-content .wtwe-hero-slider-actions .wtwe-slider-action-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper .wtwe-hero-slider-content .wtwe-hero-slider-actions-design2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
            }
            $this->end_controls_section();

            $this->start_controls_section(
                'arrows_style_section',
                [
                    'label' => esc_html__('Arrows', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'hero_slider_design' => 'design-1',
                    ],
                ]
            );
      
            $this->add_control(
                'arrows_text_color',
                array(
                    'label' => esc_html('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, 
                        {{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-next::before' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, 
                        {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-next::before' => 'color:{{VALUE}};',
                    ],

                )
            );

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'arrows_padding',
                    [
                        'label' => esc_html__('Padding', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%', 'em', 'rem'],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, 
                            {{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-next::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, 
                            {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-next::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );



                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'arrows_background',
                        'types' => ['classic', 'gradient'],
                        'selector' => '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, 
                                      {{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-next::before,
                                      {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before,
                                      {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-next::before'
                    ]
                );


                $this->add_control(
                    'slider_arrows_border_radius',
                    [
                        'label' => esc_html__('Border Radius', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, {{WRAPPER}} .wtwe-hero-slider-section .wtwe-hero-slider-wrapper.slick-slider .slick-next::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            '{{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-prev::before, 
                            {{WRAPPER}} .wtwe-hero-slider-section-design-2 .wtwe-hero-slider-wrapper.slick-slider .slick-next::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
            }



            $this->end_controls_section();
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
            $settings = $this->get_settings_for_display();

            if ($settings['content_type'] == 'trips_ids') {
                $query = new \WP_Query(array(
                    'posts_per_page'   => $settings['trips_count'],
                    'offset'           => 0,
                    'orderby'          => 'date',
                    'order'            => 'ASC',
                    'post_type'        => 'itineraries',
                    'post_status'      => 'publish',
                    'post__in' => $settings['trips_ids'],
                ));
            } else if ($settings['content_type'] == 'featured_trips') {
                $query = new \WP_Query(array(
                    'posts_per_page'   => $settings['trips_count'],
                    'meta_key'         => 'wp_travel_featured',
                    'meta_value'       => 'yes',
                    'orderby'          => 'date',
                    'order'            => 'ASC',
                    'post_type'        => 'itineraries',
                    'post_status'      => 'publish',
                ));
            } else {
                $query_term = '';

                if ($settings['content_type'] == 'itinerary_types') {
                    $query_term = $settings['trips_type'];
                } else if ($settings['content_type'] == 'travel_locations') {
                    $query_term = $settings['trips_destination'];
                } else if ($settings['content_type'] == 'activity') {
                    $query_term = $settings['trips_activity'];
                }
                $query = new \WP_Query(array(
                    'posts_per_page'   => $settings['trips_count'],
                    'offset'           => 0,
                    'orderby'          => 'date',
                    'order'            => 'ASC',
                    'post_type'        => 'itineraries',
                    'post_status'      => 'publish',
                    // 'suppress_filters' => true,
                    'tax_query'        => array(
                        array(
                            'taxonomy' => $settings['content_type'],
                            'field'    => 'slug',
                            'terms'    => $query_term,
                        ),
                    ),
                ));
            }
?>
            <?php if ($settings['hero_slider_design'] == "design-1") : ?>
                <div class="wtwe-hero-slider-section ">
                    <div class="wtwe-main-slider">
                        <div class="wtwe-hero-slider-wrapper design-1 <?php if ($settings['show_slider_arrows'] != 'yes') echo 'no-arrows'; ?> <?php if ($settings['slider_default_arrows'] == 'yes') echo 'default-arrows-pos'; ?>">
                            <?php
                            if ($query->have_posts()) {
                                while ($query->have_posts()) {
                                    $query->the_post();
                            ?>

                                    <div class="wtwe-hero-slider">
                                        <img src="<?php esc_url(the_post_thumbnail_url()); ?>" />
                                        <?php if ($settings['show_slider_content'] == 'yes') { ?>
                                            <div class="wtwe-hero-slider-content">
                                                <?php if ($settings['show_trip_title'] == 'yes') { ?>
                                                    <div class="wtwe-hero-slider-title ">
                                                        <?php the_title(); ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($settings['show_trip_excerpt'] == 'yes') { ?>
                                                    <div class="wtwe-hero-slider-body">
                                                        <p><?php the_excerpt(); ?></p>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($settings['show_trip_button'] == 'yes') { ?>
                                                    <div class="wtwe-hero-slider-actions">
                                                        <a href="<?php esc_url(the_permalink()); ?>" class="wtwe-slider-action-button">
                                                            <?php echo esc_html($settings['button_text']); ?>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                            <?php
                                }

                                wp_reset_postdata();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            $autoplay_speed = $settings['autpplay_slider_speed']['size'] ?? 3000;
            if ($settings['hero_slider_design'] == "design-2") : ?>
                <div class="wtwe-hero-slider-section-design-2">

                    <div class="wtwe-main-slider">
                        <div class="wtwe-hero-slider-wrapper design-2 slick-slider" data-slick='{
                        "slidesToShow": 1, 
                        "slidesToScroll": 1, 
                        "infinite": true, 
                        "speed": 1000, 
                        "autoplaySpeed": <?php echo esc_js($autoplay_speed); ?>, 
                        "dots": false, 
                        "arrows": false, 
                        "autoplay": <?php echo ($settings['autoplay'] == 'yes') ? 'true' : 'false'; ?>, 
                        "draggable": true, 
                        "fade": true 
                    }'>

                            <?php
                            if ($query->have_posts()) {
                                while ($query->have_posts()) {
                                    $query->the_post();

                                    $trip_id = get_the_ID();
                                    $args = $args_regular = array('trip_id' => $trip_id);
                                    $trip_price = WP_Travel_Helpers_Pricings::get_price($args);
                                    $args_regular = $args;
                                    $args_regular['is_regular_price'] = true;
                                    $regular_price = WP_Travel_Helpers_Pricings::get_price($args_regular);
                            ?>
                                    <div class="wtwe-hero-slider-item">
                                        <div class="wtwe-overlay"></div>
                                        <div class="wtwe-hero-slider-image">
                                            <img src="<?php esc_url(the_post_thumbnail_url()); ?>" alt="<?php the_title_attribute(); ?>" />
                                        </div>
                                        <div class="wtwe-widgets-design-2-wrapper" style="max-width: <?php echo esc_attr($settings['slider_container_max_width']['size']) . esc_attr($settings['slider_container_max_width']['unit']); ?>;">
                                            <?php if ($settings['show_slider_content'] == 'yes') { ?>
                                                <div class="wtwe-hero-slider-content">
                                                    <!-- location -->
                                                    <?php if ($settings['show_trip_location-design-2'] == 'yes') { ?>
                                                        <div class="wtwe-hero-slider-location">

                                                            <?php
                                                            ob_start();
                                                            wptravel_single_trip_location(get_the_ID());
                                                            preg_match_all('/<span><a.*?>(.*?)<\/a><\/span>/', ob_get_clean(), $matches);
                                                            if (!empty($matches)) {
                                                            ?>
                                                                <p><?php echo esc_html($matches[1][0]); ?></p>
                                                            <?php
                                                            } else {
                                                                echo esc_html__('No location assigned', 'wt-widgets-elementor');
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php if ($settings['show_wtwe_hline_2'] === 'yes') : ?>
                                                            <div class="wtwe-hline"></div>
                                                        <?php endif; ?>

                                                    <?php } ?>

                                                    <?php if ($settings['show_trip_title'] == 'yes') { ?>
                                                        <div class="wtwe-hero-slider-title-design-2">
                                                            <h2><?php the_title(); ?> </h2>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if ($settings['show_trip_excerpt'] == 'yes') { ?>
                                                        <div class="wtwe-hero-slider-body">
                                                            <p><?php the_excerpt(); ?></p>
                                                        </div>
                                                    <?php } ?>

                                                    <!-- trip price button -->
                                                    <div class="wtwe-price-buy-btn">
                                                        <?php if ($settings['show_trip_prices'] == 'yes') { ?>
                                                            <div class="wtwe-trips-by-type-pricing">
                                                                <div class="wtwe-trips-by-type-pricing">
                                                                    <div class="wtwe-trips-by-type-pricing-text ">

                                                                    </div>
                                                                    <div class="wtwe-trips-by-type-price ">
                                                                        <?php if (isset($regular_price) && $regular_price > $trip_price) { ?>

                                                                            <div class="wtwe-trips-by-type-regular-price">
                                                                                <del class="wtwe-trips-by-type-sale-price">
                                                                                    <?php echo wp_kses_post(wptravel_get_formated_price_currency($regular_price)); ?>
                                                                                </del>
                                                                                <?php echo wp_kses_post(wptravel_get_formated_price_currency($trip_price)); ?>
                                                                            </div>
                                                                        <?php
                                                                        } else { ?>
                                                                            <div class="wtwe-trips-by-type-regular-price">
                                                                                <?php echo wp_kses_post(wptravel_get_formated_price_currency($trip_price)); ?>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>



                                                        <?php if ($settings['show_trip_button'] == 'yes') { ?>
                                                            <div class="wtwe-hero-slider-actions-design2">
                                                                <a href="<?php esc_url(the_permalink()); ?>" class="wtwe-slider-action-button">
                                                                    <?php echo esc_html($settings['button_text']); ?>
                                                                </a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                </div>
                                            <?php } ?>
                                        </div>

                                    </div>
                            <?php
                                }
                                wp_reset_postdata();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

<?php
        }
    }
}
