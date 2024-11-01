<?php

/**
 * Featured Trips class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets;

use WP_Query;
use WP_Travel_Helpers_Trips;
use WP_Travel_Helpers_Pricings;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Featured Trips widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Featured_Trips')) {
	class WTWE_Featured_Trips extends Widget_Base
	{
		/**
		 * Class constructor.
		 *
		 * @param array $data Widget data.
		 * @param array $args Widget arguments.
		 */
		public function __construct($data = array(), $args = null) {
			parent::__construct($data, $args);
			$prefixed = defined( WP_DEBUG ) ? '.min' : '';
            // wp_register_style( 'featured-trips', plugins_url( 'assets/css/featured-trips.css', WTWE_PLUGIN_FILE ), array() );
            // wp_enqueue_style( 'featured-trips' );
			wp_register_style( 'featured-trips', plugins_url( 'assets/css/featured-trips' . $prefixed . '.css', WTWE_PLUGIN_FILE ), array() );
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
		public function get_name() {
			return 'wp-travel-featured-trips';
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
		public function get_title() {
			return esc_html__( 'Featured Trips', 'wt-widgets-elementor' );
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
		public function get_icon() {
			return 'eicon-info-box';
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
		public function get_categories() {
			return array( 'wp-travel' );
		}

		/**
		 * Enqueue styles.
		 */
		public function get_style_depends() {
			return array( 'featured-trips' );
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
		public function get_script_depends() {
			return array( 'featured-trips' );
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
        protected function _register_controls() {
			$trip_type = array();
			$terms     = get_terms(
				array(
					'taxonomy'   => 'itinerary_types',
					'hide_empty' => false,
				)
			);

			if ( is_array( $terms ) && count( $terms ) > 0 ) {
				foreach ( $terms as $key => $term ) {
					$id               = ! empty( $term->term_id ) ? $term->term_id : '';
					$trip_type[ $id ] = ! empty( $term->name ) ? $term->name : '';
				}
			}

			$this->start_controls_section(
				'general_content',
				array(
					'label' => esc_html__( 'General', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(  
                'trips_count',
                array(
                    'label' => esc_html__( 'Trips To Show', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 3,
                    'min' => 1,
                    'max' => 20,
                    'step' => 1,
                ),
			);

			$this->add_control(
				'featured_trips_order',
				[
					'label'	=>	esc_html__( 'Order', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'ASC',
                    'options' => array(
						'ASC' => esc_html__( 'Ascending', 'wt-widgets-elementor' ),
						'DESC' => esc_html__( 'Descending', 'wt-widgets-elementor' ),
                    ),
				],
			);

			$this->add_responsive_control(
				'trips_gap',
				array(
					'label'	=> esc_html__( 'Cards spacing', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::SLIDER,
					'default' => array(
						'size' => 20,
						'unit' => 'px',
					),
					'size_units' => array( 'px', 'rem' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
						'rem' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
					),
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
					],
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_content',
				array(
					'label' => esc_html__( 'Card', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(
				'card_width',
				[
					'label' => esc_html__( 'Width', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', '%', 'vw'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2048,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(  
                'card_direction',
                array(
                    'label' => esc_html__( 'Direction', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
					'default' => 'row',
                    'options' => array(
						'row' => esc_html__( 'Row', 'wt-widgets-elementor' ),
						'column' => esc_html__( 'Column', 'wt-widgets-elementor' ),
                        'row-reverse' => esc_html__( 'Row Reverse', 'wt-widgets-elementor' ),
                        'column-reverse' => esc_html__( 'Column Reverse', 'wt-widgets-elementor' ),
                    ),
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips' => 'flex-direction: {{VALUE}}',
					),
                )
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_image_content',
				array(
					'label' => esc_html__( 'Card Image', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_image',
                array(
                    'label' => esc_html__( 'Show Image', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_responsive_control(
				'image_width',
				[
					'label' => esc_html__( 'Width', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', '%', 'vw'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2048,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-img-container' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_sale_badge_content',
				array(
					'label' => esc_html__( 'Sale Badge', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_trip_sale_badge',
                array(
                    'label' => esc_html__( 'Show Sale Badge', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(  
                'show_trip_minimal_badge',
                array(
                    'label' => esc_html__( 'Minimal Badge', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'On', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Off', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(
				'minimal_badge_custom_text',
				[
					'label' => esc_html__( 'Custom Text', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => 'Sale',
				]
			);

			$this->add_responsive_control(  
                'trip_sale_badge_location',
                [
                    'label' => esc_html__( 'Badge Location', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
					'default' => 'top:0;left:0;',
                    'options' => [
						'top:0;right:0;' => esc_html__( 'Top Right', 'wt-widgets-elementor' ),
						'bottom:0;right:0;' => esc_html__( 'Bottom Right', 'wt-widgets-elementor' ),
						'top:0;left:0;' => esc_html__( 'Top Left', 'wt-widgets-elementor' ),
						'bottom:0;left:0' => esc_html__( 'Bottom Left', 'wt-widgets-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount' => '{{VALUE}}',
					],
				]
			);
			
			$this->end_controls_section();

			$this->start_controls_section(
				'card_body_content',
				array(
					'label' => esc_html__( 'Card Body', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_body',
                array(
                    'label' => esc_html__( 'Show Body', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_responsive_control(
				'body_width',
				[
					'label' => esc_html__( 'Width', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', '%', 'vw'],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 2048,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
						'vw' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_title_content',
				array(
					'label' => esc_html__( 'Trip Title', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_trip_title',
                array(
                    'label' => esc_html__( 'Show Trip Title', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_responsive_control(  
                'card_trip_title_position',
                array(
                    'label' => esc_html__( 'Title Position', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
					'default' => 'row',
                    'options' => array(
						'row-reverse' => esc_html__( 'Right', 'wt-widgets-elementor' ),
						'row' => esc_html__( 'Left', 'wt-widgets-elementor' ),
                    ),
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header' => 'flex-direction: {{VALUE}}',
					),
                )
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_rating_content',
				[
					'label' => esc_html__( 'Trip Rating', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_responsive_control(  
                'show_trip_rating',
                array(
                    'label' => esc_html__( 'Show Trip Rating', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(
                'trip_rating_icon',
                array(
                    'label' => esc_html__( 'Rating Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fa fa-star',
                        'library' => 'fa-solid',
                    ],
                    'recommended' => [
                        'fa-solid' => [
                            'circle',
                            'dot-circle',
                            'square-full',
                        ],
                        'fa-regular' => [
                            'circle',
                            'dot-circle',
                            'square-full',
                        ],
                    ],
                )
            );

			$this->add_responsive_control(  
                'show_trip_rating_icon',
                array(
                    'label' => esc_html__( 'Show Rating Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(
				'trip_rating_icon_color',
				array(
					'label' => esc_html__( 'Icon Color', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
				)
			);

			$this->add_responsive_control(
                'trip_rating_icon_size',
                [
                    'label' => esc_html__( 'Size', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px', '%', 'rem', 'vw'],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 300,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 1,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'rem' => [
                            'min' => 10,
                            'max' => 20,
                            'step' => 1,
                        ],
                        'vw' => [
                            'min' => 1,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 13,
                    ],
                ]
            );

			$this->add_responsive_control(  
                'show_trip_max_rating',
                array(
                    'label' => esc_html__( 'Show Trip Max Rating (out of 5)', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
					'label_block' => true,
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_tag_content',
				array(
					'label' => esc_html__( 'Trip Tag', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_tags',
                array(
                    'label' => esc_html__( 'Show Tags', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_excerpt_content',
				array(
					'label' => esc_html__( 'Trip Excerpt', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_excerpt',
                array(
                    'label' => esc_html__( 'Show Excerpt', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_details_content',
				array(
					'label' => esc_html__( 'Trip Details', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_trip_details',
                array(
                    'label' => esc_html__( 'Show Trip Details', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_responsive_control(  
                'show_trip_pax',
                array(
                    'label' => esc_html__( 'Show Trip Pax', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_responsive_control(  
                'show_trip_duration',
                array(
                    'label' => esc_html__( 'Show Trip Duration', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_price_content',
				array(
					'label' => esc_html__( 'Trip Price', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(  
                'show_price',
                array(
                    'label' => esc_html__( 'Show Price', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_responsive_control(  
                'show_price_label',
                array(
                    'label' => esc_html__( 'Show Price Label', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(
				'price_label',
				[
					'label' => esc_html__( 'Price Label', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => esc_html__( 'Starting From', 'wt-widgets-elementor' ),
					'condition' => [
						'show_price_label' => 'yes',
					],
				]
			);

			$this->add_responsive_control(  
                'card_price_position',
                array(
                    'label' => esc_html__( 'Price Position', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
					'default' => 'row',
                    'options' => array(
						'row' => esc_html__( 'Right', 'wt-widgets-elementor' ),
						'row-reverse' => esc_html__( 'Left', 'wt-widgets-elementor' ),
                    ),
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-footer' => 'flex-direction: {{VALUE}}',
					),
                )
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_styles',
				array(
					'label' => esc_html__( 'Card', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_margin',
				[
					'label' => esc_html__( 'Margin', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'card_padding',
				[
					'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'card_border',
                    'selector' => '{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips',
                )
            );

            $this->add_control(
				'card_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'card_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips',
				]
			);

			$this->add_responsive_control(  
                'card_overflow',
                array(
                    'label' => esc_html__( 'Overflow', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'visible',
					'options' => [
						'visible' => esc_html__( 'Visible', 'wt-widgets-elementor' ),
						'hidden' => esc_html__( 'Hidden', 'wt-widgets-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips' => 'overflow:{{VALUE}}'
					]
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'card_body_styles',
				array(
					'label' => esc_html__( 'Card Body', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_body_margin',
				[
					'label' => esc_html__( 'Margin', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'card_body_padding',
				[
					'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'card_body_border',
                    'selector' => '{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body',
                )
            );

            $this->add_control(
				'card_body_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'card_body_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_image_styles',
				array(
					'label' => esc_html__( 'Card Image', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_image_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'card_image_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container',
				]
			);

			$this->add_responsive_control(
				'card_image_height',
				array(
					'label'	=> esc_html__( 'Height', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::SLIDER,
					'default' => array(
						'size' => 300,
						'unit' => 'px',
					),
					'size_units' => array( 'px', '%', 'rem' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
						'rem' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
					),
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container' => 'height: {{SIZE}}{{UNIT}};',
					],
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_sale_badge_styles',
				array(
					'label' => esc_html__( 'Sale Badge', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_responsive_control(
				'trip_sale_badge_margin',
				[
					'label' => esc_html__( 'Margin', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'trip_sale_badge_padding',
				[
					'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'trip_sale_badge_background',
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount',
				]
			);

			$this->add_control(
				'trip_sale_badge_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'trip_sale_badge_hr',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);
			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'trip_sale_badge_typography',
						'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					array(
						'name' => 'trip_sale_badge_stroke',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => 'trip_sale_badge_shadow',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-img-container .wtwe-featured-trips-discount',
					)
				);
            } 
			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_title_styles',
				array(
					'label' => esc_html__( 'Trip Title', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_trip_title_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-title' => 'color: {{VALUE}}',
					),
				),
			);
			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'card_trip_title_typography',
						'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-title',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					array(
						'name' => 'card_trip_title_stroke',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-title',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => 'card_trip_title_shadow',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-title',
					)
				);
            }
			

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_tag_styles',
				array(
					'label' => esc_html__( 'Trip Tag', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_trip_tag_padding',
				[
					'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-trip-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'card_trip_tag_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-trip-tag' => 'color: {{VALUE}}',
					),
				),
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {

            } else {
                $this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'card_trip_tag_typography',
						'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-trip-tag',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					array(
						'name' => 'card_trip_tag_stroke',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-trip-tag',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => 'card_trip_tag_shadow',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-trip-tag',
					)
				);
	
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'card_trip_tag_background',
						'types' => [ 'classic', 'gradient', 'video' ],
						'selector' => '{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-inner-body .wtwe-featured-trips-header .wtwe-featured-trips-trip-category .wtwe-featured-trips-trip-tag',
					]
				);
	
				$this->add_control(
					'card_trip_tag_border_radius',
					[
						'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%', 'em', 'rem'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-inner-body .wtwe-featured-trips-header .wtwe-featured-trips-trip-category .wtwe-featured-trips-trip-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
            }

			

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_rating_styles',
				[
					'label' => esc_html__( 'Trip Rating', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'card_trip_rating_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating' => 'color: {{VALUE}}',
					),
				),
			);
			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_control(
					'card_trip_rating_padding',
					[
						'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%', 'em', 'rem'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
	
				
	
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'card_trip_rating_typography',
						'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					array(
						'name' => 'card_trip_rating_stroke',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => 'card_trip_rating_shadow',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating',
					)
				);
	
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'card_trip_rating_background',
						'types' => [ 'classic', 'gradient', 'video' ],
						'selector' => '{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating',
					]
				);
	
				$this->add_control(
					'card_trip_rating_border_radius',
					[
						'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%', 'em', 'rem'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-header .wtwe-featured-trips-inner-header .wtwe-featured-trips-trip-rating' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
            } 
				
			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_excerpt_styles',
				array(
					'label' => esc_html__( 'Trip Excerpt', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_trip_excerpt_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#777',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-content .wtwe-featured-trips-excerpt p' => 'color: {{VALUE}}',
					),
				),
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'card_trip_excerpt_typography',
						'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-content .wtwe-featured-trips-excerpt p',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					array(
						'name' => 'card_trip_excerpt_stroke',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-content .wtwe-featured-trips-excerpt p',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => 'card_trip_excerpt_shadow',
						'selector' => '{{ WRAPPER }} .wtwe-featured-trips-wrapper .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-content .wtwe-featured-trips-excerpt p',
					)
				);
            }
			

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_details_styles',
				array(
					'label' => esc_html__( 'Trip Details', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'trip_details_pax_icon_color',
				array(
					'label' => esc_html__( 'Pax Icon Color', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::COLOR,
				)
			);

			$this->add_control(
				'hr',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);

			$this->add_control(
				'trip_details_duration_icon_color',
				array(
					'label' => esc_html__( 'Duration Icon Color', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::COLOR,
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_price_styles',
				array(
					'label' => esc_html__( 'Trip Price', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'card_trip_price_label_color',
				array(
					'label' => esc_html__( 'Price Label Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-pricing-text' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label' => esc_html__( 'Price Label Typography', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_label_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-pricing-text',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'label' => esc_html__( 'Price Label Stroke', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_label_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-pricing-text',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'label' => esc_html__( 'Price Label Shadow', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_label_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-pricing-text',
				)
			);

			$this->add_control(
				'price_hr',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);

			$this->add_control(
				'card_trip_price_color',
				array(
					'label' => esc_html__( 'Price Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-price .wtwe-featured-trips-regular-price' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label' => esc_html__( 'Price Text Typography', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-price .wtwe-featured-trips-regular-price',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'label' => esc_html__( 'Price Text Stroke', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-price .wtwe-featured-trips-regular-price',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'label' => esc_html__( 'Price Text Shadow', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-featured-trips .wtwe-featured-trips-body .wtwe-featured-trips-footer .wtwe-featured-trips-pricing .wtwe-featured-trips-price .wtwe-featured-trips-regular-price',
				)
			);		

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
		protected function render() {
			$settings = $this->get_settings_for_display();

			$query = new WP_Query( array(
				'posts_per_page'   => $settings[ 'trips_count' ],
				'meta_key'         => 'wp_travel_featured',
				'meta_value'       => 'yes',
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
				'order'			   => $settings[ 'featured_trips_order' ],
			) );

        ?>
            <div class="wtwe-featured-trips-wrapper">
				<?php 
				if ( $query->have_posts() ) { 
					while ( $query->have_posts() ) {
						$query->the_post();

						$trip_id = get_the_ID();
						$args = $args_regular = array( 'trip_id' => $trip_id );
						$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );
						$args_regular = $args;
						$args_regular['is_regular_price'] = true;
						$regular_price = WP_Travel_Helpers_Pricings::get_price( $args_regular );
						$enable_sale = WP_Travel_Helpers_Trips::is_sale_enabled(
							array(
								'trip_id'                => $trip_id,
								'from_price_sale_enable' => true,
							)
						);
						$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );

				?>
                <div class="wtwe-featured-trips">
                    <div class="wtwe-featured-trips-img-container <?php if( $settings['show_image'] != 'yes' ) echo 'wtwe-hidden'?>">
						<a href="<?php esc_url( the_permalink($trip_id) ) ?>" class="wtwe-featured-trip-link">
                        	<?php echo wp_kses_post( wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ) ); ?>
						</a>
						<?php if ( $trip_price > 0 && $regular_price > $trip_price ) { 
							$save = ( 1 - ( (float)$trip_price / (float)$regular_price ) ) * 100;
							$save = number_format( $save, 2, '.', ',' );
							if( $settings['show_trip_minimal_badge'] == 'yes' ) {
						?>
							<div class="wtwe-featured-trips-discount <?php if( $settings[ 'show_trip_sale_badge' ] != 'yes' ) echo 'wtwe-hidden' ?>">
								<?php echo esc_html( $settings['minimal_badge_custom_text'] ); ?>
							</div>
						<?php } else { ?>
							<div class="wtwe-featured-trips-discount ">
								<?php echo esc_html( $save ) . esc_html__( '% off', 'wt-widgets-elementor' ); ?>
							</div>
						<?php } } ?>
                    </div>
                    <div class="wtwe-featured-trips-body <?php if( $settings['show_body'] != 'yes' ) echo 'wtwe-hidden'?>">
                        <div class="wtwe-featured-trips-inner-body">
                            <div class="wtwe-featured-trips-header">
                                <div class="wtwe-featured-trips-inner-header">
                                    <div class="wtwe-featured-trips-title-container <?php if( $settings['show_trip_title'] != 'yes' ) echo 'wtwe-hidden'?>">
										<a href="<?php esc_url( the_permalink($trip_id) ) ?>" class="wtwe-featured-trip-link">
                                        	<h2 class="wtwe-featured-trips-title"><?php esc_html( the_title() ); ?></h2>
										</a>
                                    </div>
                                    <div class="wtwe-featured-trips-trip-rating <?php if( $settings[ 'show_trip_rating' ] != 'yes' ) echo 'wtwe-hidden' ?>">
                                        <i class="fa fa-star <?php if( $settings[ 'show_trip_rating_icon' ] != 'yes' ) echo 'wtwe-hidden' ?>" style="font-size:<?php echo esc_attr($settings['trip_rating_icon_size']['size']) ?>px"></i>
                                        <?php echo esc_html( wptravel_get_average_rating( $trip_id ) );
										if( $settings[ 'show_trip_max_rating' ] ) echo esc_html( '/5' ) ; ?>
                                    </div>
                                </div>
                                <div class="wtwe-featured-trips-trip-category">
								<?php foreach ( get_the_terms( $trip_id, 'itinerary_types' ) as $tax ) { ?>
									<div class="wtwe-featured-trips-trip-tag <?php if( $settings[ 'show_tags' ] != 'yes' ) echo 'wtwe-hidden' ?>"><?php echo esc_html( $tax->name ); ?></div>
								<?php } ?>
                                </div>
                            </div>
                            <div class="wtwe-featured-trips-content">
                                <div class="wtwe-featured-trips-excerpt <?php if( $settings[ 'show_excerpt' ] != 'yes' ) echo 'wtwe-hidden' ?>">
									<?php esc_html( the_excerpt() ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="wtwe-featured-trips-footer">
                            <div class="wtwe-featured-trips-details <?php if( $settings[ 'show_trip_details' ] != 'yes' ) echo 'wtwe-hidden' ?>">
								<?php if( $settings[ 'show_trip_pax' ] == 'yes' ) { ?>
                                <div class="wtwe-featured-trips-pax">
                                    <i class="fa fa-user wtwe-featured-trips-pax-icon"></i>
                                    <div class="wtwe-featured-trips-details-value">
										<?php echo esc_html( wptravel_get_group_size( $trip_id ) ); ?>
									</div>
                                </div>
								<?php }
								
								if( $settings[ 'show_trip_duration' ] == 'yes' ) { ?>
								<div class="wtwe-featured-trips-datetime">
								<?php if( $is_fixed_departure ) { ?>
									<i class="fa fa-calendar wtwe-featured-trips-pax-icon"></i>
									<div class="wtwe-featured-trips-details-value">
										<?php echo wp_kses_post( wptravel_get_fixed_departure_date( $trip_id ) ); ?>
									</div>
								<?php } else { ?>
									<i class="fa fa-clock wtwe-featured-trips-pax-icon"></i>
									<div class="wtwe-featured-trips-details-value">
										<?php echo wp_kses_post( wp_travel_get_trip_durations( $trip_id ) ); ?>
									</div>
								<?php } ?>
								</div>
								<?php } ?>
                            </div>
							<?php if ( $trip_price > 0 && $settings[ 'show_price' ] == 'yes' ) { ?>
                            <div class="wtwe-featured-trips-pricing">
								<?php if($settings[ 'show_price_label' ] == 'yes') { ?>
                                <div class="wtwe-featured-trips-pricing-text <?php echo $settings[ 'card_price_position' ] == 'row-reverse' ? 'wtwe-price-left' : '' ?>">
                                    <?php echo esc_html( $settings[ 'price_label' ] ); ?>
                                </div>
								<?php } ?>
                                <div class="wtwe-featured-trips-price <?php echo $settings[ 'card_price_position' ] == 'row-reverse' ? 'wtwe-price-left' : '' ?>">
									<?php if ( isset( $regular_price ) && $regular_price > $trip_price ) { ?>
									<del class="wtwe-featured-trips-sale-price">
										<?php echo wp_kses_post( wptravel_get_formated_price_currency( $regular_price ) ); ?>
									</del>

									<div class="wtwe-featured-trips-regular-price">
										<?php echo wp_kses_post( wptravel_get_formated_price_currency( $trip_price ) ); ?>
									</div>
									<?php
									} else { ?>
									<div class="wtwe-featured-trips-regular-price">
										<?php echo wp_kses_post( wptravel_get_formated_price_currency( $trip_price ) ); ?>
									</div>
									<?php } ?>
                                </div>
                            </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
				<?php
					}
				}
				wp_reset_postdata();
				?>
            </div>
        <?php
		}

		/**
		 * Render the widget output in the editor.
		 *
		 * Written as a Backbone JavaScript template and used to generate the live preview.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 */
		protected function content_template() {
            ?>
            <div class="wtwe-featured-trips-wrapper">
            <#
                var tripsByTerm = ElementorConfig.wp_travel_featured_trips[ settings.featured_trips_order ];
                var tripImageControlClass = settings.show_image != 'yes' ? 'wtwe-hidden' : '';
				var tripSaleBadgeControlClass = settings.show_trip_sale_badge != 'yes' ? 'wtwe-hidden' : '';
                var tripBodyControlClass = settings.show_body != 'yes' ? 'wtwe-hidden' : '';
                var tripTitleControlClass = settings.show_trip_title != 'yes' ? 'wtwe-hidden' : '';
                var tripRatingControlClass = settings.show_trip_rating != 'yes' ? 'wtwe-hidden' : '';
                var tripRatingIconControlClass = settings.show_trip_rating_icon != 'yes' ? 'wtwe-hidden' : '';
                var tripMaxRatingControlClass = settings.show_trip_max_rating != 'yes' ? '' : '/5';
                var tripTagsControlClass = settings.show_tags != 'yes' ? 'wtwe-hidden' : '';
                var tripExcerptControlClass = settings.show_excerpt != 'yes' ? 'wtwe-hidden' : '';
                var tripDetailsControlClass = settings.show_trip_details != 'yes' ? 'wtwe-hidden' : '';
                var tripPriceControlClass = settings.card_price_position == 'row-reverse' ? 'wtwe-price-left' : '';
                
                Array.isArray(tripsByTerm) && tripsByTerm.length > 0 && tripsByTerm.forEach(( trip, index ) => {
					if(index+1 <= settings.trips_count) {
                #>
                <div class="wtwe-featured-trips">
                    <div class="wtwe-featured-trips-img-container {{ tripImageControlClass }}">
						<a href="{{ trip.permalink }}" class="wtwe-featured-trip-link">
							{{{ trip.thumbnail_image }}}
						</a>
						<#	if ( trip.trip_price > 0 && trip.regular_price > trip.trip_price ) { 
								var save = ( 1 - ( trip.trip_price / trip.regular_price ) ) * 100;
								save = save.toFixed(2);
								if( settings.show_trip_minimal_badge == 'yes' ) {
						#>
							<div class="wtwe-featured-trips-discount {{ tripSaleBadgeControlClass }}">
								{{{ settings.minimal_badge_custom_text }}}
							</div>
						<# 		} else { #>
							<div class="wtwe-featured-trips-discount {{ tripSaleBadgeControlClass }}">
								{{{ save }}}% off
							</div>
						<# 		} 
							}
						#>
					</div>
                    <div class="wtwe-featured-trips-body {{ tripBodyControlClass }}">
                        <div class="wtwe-featured-trips-inner-body">
                            <div class="wtwe-featured-trips-header">
                                <div class="wtwe-featured-trips-inner-header">
                                    <div class="wtwe-featured-trips-title-container {{ tripTitleControlClass }}">
										<a href="{{ trip.permalink }}" class="wtwe-featured-trip-link">
											<h2 class="wtwe-featured-trips-title">{{{ trip.title }}}</h2>
										</a>
                                    </div>
                                    <div class="wtwe-featured-trips-trip-rating {{ tripRatingControlClass }}">
                                        <i class="{{ settings.trip_rating_icon.value }} {{ tripRatingIconControlClass }}"  style="font-size:{{settings.trip_rating_icon_size.size}}{{settings.trip_rating_icon_size.unit}};color:{{settings.trip_rating_icon_color}};"></i>
                                        {{{ trip.average_rating }}}{{{ tripMaxRatingControlClass }}}
                                    </div>
                                </div>
                                <div class="wtwe-featured-trips-trip-category {{ tripTagsControlClass }}">
                                <# trip.tags.forEach( ( tax, index ) => { #>
                                    <div class="wtwe-featured-trips-trip-tag">
                                        {{{ tax }}}
                                    </div>
                                <# }); #>
                                </div>
                            </div>
                            <div class="wtwe-featured-trips-content {{ tripExcerptControlClass }}">
                                <div class="wtwe-featured-trips-excerpt">
                                    {{{ trip.excerpt }}}
                                </div>
                            </div>
                        </div>
                        <div class="wtwe-featured-trips-footer">
                            <div class="wtwe-featured-trips-details {{ tripDetailsControlClass }}">
								<# if( settings.show_trip_pax == 'yes' ) { #>
                                <div class="wtwe-featured-trips-pax">
                                    <i class="fa fa-user wtwe-featured-trips-pax-icon" style="color:{{ settings.trip_details_pax_icon_color }};"></i>
                                    <div class="wtwe-featured-trips-details-value">
                                        {{{ trip.pax }}}
                                    </div>
                                </div>
								<# }

								if( settings.show_trip_duration == 'yes' ) { #>
                                <div class="wtwe-featured-trips-datetime">
                                    <# if( trip.is_fixed_departure ) { #>
                                        <i class="fa fa-calendar wtwe-featured-trips-pax-icon" style="color:{{ settings.trip_details_duration_icon_color }};"></i>
                                    <# } else { #>
                                        <i class="fa fa-clock wtwe-featured-trips-pax-icon" style="color:{{ settings.trip_details_duration_icon_color }};"></i>
                                    <# } #>
                                    <div class="wtwe-featured-trips-details-value">
                                        {{{ trip.duration }}}
                                    </div>
                                </div>
								<# } #>
                            </div>
                            <# if ( trip.trip_price > 0 && settings.show_price == 'yes' ) { #>
                            <div class="wtwe-featured-trips-pricing">
								<# if( settings.show_price_label == 'yes' ) { #>
                                <div class="wtwe-featured-trips-pricing-text {{ tripPriceControlClass }}">
                                    {{{ settings.price_label }}}
                                </div>
								<# } #>
                                <div class="wtwe-featured-trips-price {{ tripPriceControlClass }}">
                                    <# if ( trip.regular_price && trip.regular_price > trip.trip_price ) { #>
                                    <del class="wtwe-featured-trips-sale-price">
                                        {{{ trip.regular_price_html }}}
                                    </del>
    
                                    <div class="wtwe-featured-trips-regular-price">
                                        {{{ trip.trip_price_html }}}
                                    </div>
                                    <# } else { #>
                                    <div class="wtwe-featured-trips-regular-price">
                                        {{{ trip.trip_price_html }}}
                                    </div>
                                    <# } #>
                                </div>
                            </div>
                            <# } #>
                        </div>
                    </div>
                </div>
                <# }
				})
            #>
            </div>
            <?php
            }
	}
}