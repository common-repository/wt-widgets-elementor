<?php

/**
 * Trips By Type Grid class.
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
use WP_Travel_Helpers_Trip_Dates;

use Elementor\Widget_Base;
use Elementor\Utils;
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
 * Trips By Type widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Trips_By_Type_Grid')) {
	class WTWE_Trips_By_Type_Grid extends Widget_Base
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
            wp_register_style( 'trips-by-type-grid', plugins_url( 'assets/css/trips-by-type-grid' . $prefixed . '.css', WTWE_PLUGIN_FILE ), array() );
			wp_register_script( 'match-height-script', plugin_dir_url( WTWE_PLUGIN_FILE ) . 'assets/js/match-height' . $prefixed . '.js', array( 'jquery' ), WTWE_VERSION, true );
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
			return 'wp-travel-trip-by-type-grid';
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
			return esc_html__( 'Trips By Type ( Grid )', 'wt-widgets-elementor' );
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
			return 'eicon-post-list';
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
		
		public function get_script_depends() {
			return array( 'jquery', 'match-height-script' );
		}

		/**
		 * Enqueue styles.
		 */
		public function get_style_depends() {
			return array( 'trips-by-type-grid' );
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
        public function wtwe_handle_content_type( $content_type ) {
            $content = [];
			$terms     = get_terms(
				[
					'taxonomy'   => $content_type,
					'hide_empty' => false,
				]
			);

			if ( is_array( $terms ) && count( $terms ) > 0 ) {
				foreach ( $terms as $key => $term ) {
					$id             = ! empty( $term->term_id ) ? $term->term_id : '';
					$content[ $id ] = ! empty( $term->name ) ? $term->name : '';
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
		protected function _register_controls() {
			$get_trips = get_posts( array(
                'post_type'        => 'itineraries',
                'numberposts'      => -1
            ) );
            
            $trips_ids = [];
            foreach( $get_trips as $trip ){
                $trips_ids[ $trip->ID ] = $trip->post_title;
            }

			$this->start_controls_section(
				'general_content',
				array(
					'label' => esc_html__( 'General', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_responsive_control(
				'trips_type_design_grid',
				[
					'label' => esc_html__( 'Design', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SELECT,
                    'default' => 'design-1',
                    'options' => [
						'design-1' => esc_html__( "Design 1", 'wt-widgets-elementor' ),
						'design-2' => esc_html__( "Design 2", 'wt-widgets-elementor' ),
					]
				]
			);

			$this->add_control(
                'content_type',
                array(
                    'label' => esc_html__( 'Content Type', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'trips_ids',
                    'options' => array(
                        'trips_ids' => esc_html__( 'Trips', 'wt-widgets-elementor' ),
                        'featured_trips' => esc_html__( 'Featured Trips', 'wt-widgets-elementor' ),
                        'itinerary_types' => esc_html__( 'Trip Types', 'wt-widgets-elementor' ),
                        'travel_locations' => esc_html__( 'Trip Destinations', 'wt-widgets-elementor' ),
                        'activity' => esc_html__( 'Activity', 'wt-widgets-elementor' ),
                    ),
                )
            );

			$this->add_control(
                'trips_ids',
                array(
                    'label' => esc_html__( 'Select Trips', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'default' => array_keys(array_slice( $trips_ids, 0, 3, true )),
                    'options' => $trips_ids,
                    'condition' => array(
                        'content_type' => 'trips_ids',
                    ),
                )
            );

            $this->add_control(
                'trips_type',
                array(
                    'label' => esc_html__( 'Select Trips Type', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => esc_html( array_key_first(\WTWE\Widgets\WTWE_Trips_By_Type_Grid::wtwe_handle_content_type( 'itinerary_types' )) ),
                    'options' => \WTWE\Widgets\WTWE_Trips_By_Type_Grid::wtwe_handle_content_type( 'itinerary_types' ),
                    'condition' => array(
                        'content_type' => 'itinerary_types',
                    ),
                )
            );

            $this->add_control(
                'trips_destination',
                array(
                    'label' => esc_html__( 'Select Trips Destination', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => esc_html( array_key_first(\WTWE\Widgets\WTWE_Trips_By_Type_Grid::wtwe_handle_content_type( 'travel_locations' )) ),
                    'options' => \WTWE\Widgets\WTWE_Trips_By_Type_Grid::wtwe_handle_content_type( 'travel_locations' ),
                    'condition' => array(
                        'content_type' => 'travel_locations',
                    ),
                )
            );

            $this->add_control(
                'trips_activity',
                array(
                    'label' => esc_html__( 'Select Trips Activity', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => esc_html( array_key_first(\WTWE\Widgets\WTWE_Trips_By_Type_Grid::wtwe_handle_content_type( 'activity' )) ),
                    'options' => \WTWE\Widgets\WTWE_Trips_By_Type_Grid::wtwe_handle_content_type( 'activity' ),
                    'condition' => array(
                        'content_type' => 'activity',
                    ),
                )
            );

			$this->add_responsive_control(  
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

			$this->add_responsive_control(
                'trips_type_layout',
                array(
                    'label' => esc_html__( 'Layout', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'grid',
                    'options' => [
						'grid' => esc_html__( "Grid", 'wt-widgets-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper' => 'display:{{VALUE}}',
					],
					'condition' => [
						'trips_type_layout' => 'no', // This condition will always be false, so the control will not show
					],
                )
            );
		
			$this->add_responsive_control(
				'trips_type_grid_column',
				[
					'label' => esc_html__( 'Column', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SELECT,
                    'default' => 'repeat(3, 1fr)',
                    'options' => [
						'repeat(1, 1fr)' => esc_html__( "1", 'wt-widgets-elementor' ),
						'repeat(2, 1fr)' => esc_html__( "2", 'wt-widgets-elementor' ),
						'repeat(3, 1fr)' => esc_html__( "3", 'wt-widgets-elementor' ),
						'repeat(4, 1fr)' => esc_html__( "4", 'wt-widgets-elementor' ),
						'repeat(5, 1fr)' => esc_html__( "5", 'wt-widgets-elementor' ),
						'repeat(6, 1fr)' => esc_html__( "6", 'wt-widgets-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper' => 'grid-template-columns:{{VALUE}};align-items:unset;',
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wp-travel-itinerary-items.wptravel-archive-wrapper.grid-view' => 'grid-template-columns:{{VALUE}};align-items:unset;',

					],
					'condition' => [
						'trips_type_layout' => 'grid',
					]
				]
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wp-travel-itinerary-items.wptravel-archive-wrapper.grid-view' => 'gap: {{SIZE}}{{UNIT}};',

					],
				)
			);

            // $this->add_responsive_control(
			// 	'trips_type_design_grid',
			// 	[
			// 		'label' => esc_html__( 'Design', 'wt-widgets-elementor' ),
			// 		'type' => Controls_Manager::SELECT,
            //         'default' => 'design-1',
            //         'options' => [
			// 			'design-1' => esc_html__( "Design 1", 'wt-widgets-elementor' ),
			// 			'design-2' => esc_html__( "Design 2", 'wt-widgets-elementor' ),
			// 		]
			// 	]
			// );

			$this->end_controls_section();


			$this->start_controls_section(
				'card_content',
				array(
					'label' => esc_html__( 'Card', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
					'default' => [
						'size' => 100, 
						'unit' => '%',
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type' => 'width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .view-box' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type' => 'flex-direction: {{VALUE}}',
					),
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				
                )
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_image_content',
				array(
					'label' => esc_html__( 'Card Image', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
						'{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-img-container' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_body_content',
				array(
					'label' => esc_html__( 'Card Body', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
						'{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_sale_badge_content',
				array(
					'label' => esc_html__( 'Sale Badge', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount' => '{{VALUE}}',
					],
				]
			);
			
			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_title_content',
				array(
					'label' => esc_html__( 'Trip Title', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header' => 'flex-direction: {{VALUE}}',
					),
                )
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_rating_content',
				[
					'label' => esc_html__( 'Trip Rating', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
			$this->add_responsive_control(  
                'trip_exerpt_length',
                array(
                    'label' => esc_html__( 'Excerpt Length', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 25,
                    'min' => 1,
                    'max' => 200,
                    'step' => 1,
                ),
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_details_content',
				array(
					'label' => esc_html__( 'Trip Details', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
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
					'default' => 'Starting From',
					'condition' => [
						'show_price_label' => 'yes',
					],
				]
			);

			

			$this->end_controls_section();

			$this->start_controls_section(
				'card_styles',
				array(
					'label' => esc_html__( 'Card', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_margin',
				[
					'label' => esc_html__( 'Margin', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'card_border',
                    'selector' => '{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type',
                )
            );

            $this->add_control(
				'card_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'card_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type',
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type' => 'overflow:{{VALUE}}'
					]
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'card_body_styles',
				array(
					'label' => esc_html__( 'Card Body', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_body_margin',
				[
					'label' => esc_html__( 'Margin', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'card_body_border',
                    'selector' => '{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body',
                )
            );

            $this->add_control(
				'card_body_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'card_body_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_image_styles',
				array(
					'label' => esc_html__( 'Card Image', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_image_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'card_image_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container',
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container' => 'height: {{SIZE}}{{UNIT}};',
					],
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_sale_badge_styles',
				array(
					'label' => esc_html__( 'Sale Badge', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_responsive_control(
				'trip_sale_badge_margin',
				[
					'label' => esc_html__( 'Margin', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'trip_sale_badge_background',
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount',
				]
			);

			$this->add_control(
				'trip_sale_badge_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'trip_sale_badge_hr',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'trip_sale_badge_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name' => 'trip_sale_badge_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'trip_sale_badge_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-img-container .wtwe-trips-by-type-discount',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_title_styles',
				array(
					'label' => esc_html__( 'Trip Title', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_trip_title_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-title' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'card_trip_title_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-title',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name' => 'card_trip_title_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-title',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'card_trip_title_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-title',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_tag_styles',
				array(
					'label' => esc_html__( 'Trip Tag', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_trip_tag_padding',
				[
					'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-trip-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-trip-tag' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'card_trip_tag_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body a.wtwe-trips-by-type-grid-trip-tag',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name' => 'card_trip_tag_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-trip-tag',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'card_trip_tag_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-grid-trip-tag',
				)
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'card_trip_tag_background',
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-inner-body .wtwe-trips-by-type-header .wtwe-trips-by-type-trip-category .wtwe-trips-by-type-grid-trip-tag',
				]
			);

			$this->add_control(
				'card_trip_tag_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-inner-body .wtwe-trips-by-type-header .wtwe-trips-by-type-trip-category .wtwe-trips-by-type-grid-trip-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_rating_styles',
				[
					'label' => esc_html__( 'Trip Rating', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				]
			);

			$this->add_control(
				'card_trip_rating_padding',
				[
					'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'card_trip_rating_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'card_trip_rating_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name' => 'card_trip_rating_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'card_trip_rating_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating',
				)
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'card_trip_rating_background',
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating',
				]
			);

			$this->add_control(
				'card_trip_rating_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em', 'rem'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-header .wtwe-trips-by-type-inner-header .wtwe-trips-by-type-grid-trip-rating' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
				
			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_excerpt_styles',
				array(
					'label' => esc_html__( 'Trip Excerpt', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_trip_excerpt_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#777',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-content .wtwe-trips-by-type-excerpt p' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'card_trip_excerpt_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-content .wtwe-trips-by-type-excerpt p',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name' => 'card_trip_excerpt_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-content .wtwe-trips-by-type-excerpt p',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'card_trip_excerpt_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type-wrapper .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-content .wtwe-trips-by-type-excerpt p',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_details_styles',
				array(
					'label' => esc_html__( 'Trip Details', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'trip_details_pax_icon_color',
				array(
					'label' => 'Pax Icon Color',
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
					'label' => 'Duration Icon Color',
					'type' => Controls_Manager::COLOR,
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'card_trip_price_styles',
				array(
					'label' => esc_html__( 'Trip Price', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_STYLE,
					'condition' => array(
                        'trips_type_design_grid' => 'design-2',
                    ),
				)
			);

			$this->add_control(
				'card_trip_price_label_color',
				array(
					'label' => esc_html__( 'Price Label Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-pricing-text' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label' => esc_html__( 'Price Label Typography', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_label_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-pricing-text',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'label' => esc_html__( 'Price Label Stroke', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_label_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-pricing-text',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'label' => esc_html__( 'Price Label Shadow', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_label_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-pricing-text',
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
						'{{WRAPPER}} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-price .wtwe-trips-by-type-regular-price' => 'color: {{VALUE}}',
					),
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'label' => esc_html__( 'Price Text Typography', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-price .wtwe-trips-by-type-regular-price',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'label' => esc_html__( 'Price Text Stroke', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-price .wtwe-trips-by-type-regular-price',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'label' => esc_html__( 'Price Text Shadow', 'wt-widgets-elementor' ),
					'name' => 'card_trip_price_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-by-type .wtwe-trips-by-type-body .wtwe-trips-by-type-footer .wtwe-trips-by-type-pricing .wtwe-trips-by-type-price .wtwe-trips-by-type-regular-price',
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

			if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				$settings = $this->get_settings_for_display();

			if( $settings['content_type'] == 'trips_ids' ){
                $query = new WP_Query( array(
                    'posts_per_page'   => $settings['trips_count'],
                    'post_type'        => 'itineraries',
                    'post_status'      => 'publish',
                    'post__in' 		   => $settings['trips_ids'],
                ) );
            } else if( $settings['content_type'] == 'featured_trips' ){
                $query = new WP_Query( array(
                    'posts_per_page'   => $settings[ 'trips_count' ],
                    'meta_key'         => 'wp_travel_featured',
                    'meta_value'       => 'yes',
                    'post_type'        => 'itineraries',
                    'post_status'      => 'publish',
                ) );
            } else {
                $query_term = '';

                if( $settings['content_type'] == 'itinerary_types' ){
                    $query_term = $settings['trips_type'];
                } else if($settings['content_type'] == 'travel_locations'){
                    $query_term = $settings['trips_destination'];
                } else if($settings['content_type'] == 'activity'){
                    $query_term = $settings['trips_activity'];
                }
                $query = new WP_Query( array(
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
                            'field'    => 'term_id',
                            'terms'    => $query_term,
                        ),
                    ),
                ) );
            }
			

        ?>
            <?php if($settings['trips_type_design_grid'] == "design-2"): ?>
            <div class="wtwe-trips-by-type-wrapper wtwe-trips-layout-grid design-2">
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
                <div class="wtwe-trips-by-type">
                    <div class="wtwe-trips-by-type-img-container <?php if( $settings[ 'show_image' ] != 'yes' ) echo 'wtwe-hidden' ?>">
						<a href="<?php esc_url( the_permalink($trip_id) ) ?>" class="wtwe-trips-by-type-link">
                        <?php echo wp_kses_post( wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ) ); ?>
						<?php if ( $trip_price > 0 && $regular_price > $trip_price ) { 
							$save = ( 1 - ( (float) $trip_price / (float) $regular_price ) ) * 100;
							$save = number_format( $save, 2, '.', ',' );
							if( $settings['show_trip_minimal_badge'] == 'yes' ) {
						?>
							<div class="wtwe-trips-by-type-discount <?php if( $settings[ 'show_trip_sale_badge' ] != 'yes' ) echo 'wtwe-hidden' ?>">
								<?php echo esc_html( $settings['minimal_badge_custom_text'] ); ?>
							</div>
						<?php } else { ?>
							<div class="wtwe-trips-by-type-discount <?php if( $settings[ 'show_trip_sale_badge' ] != 'yes' ) echo 'wtwe-hidden' ?>">
								<?php echo esc_html( $save ) . esc_html__( '% off', 'wt-widgets-elementor' ); ?>
							</div>
						<?php } } ?>
						</a>
                    </div>
                    <div class="wtwe-trips-by-type-body <?php if( $settings[ 'show_body' ] != 'yes' ) echo 'wtwe-hidden' ?>">
                        <div class="wtwe-trips-by-type-inner-body">
                            <div class="wtwe-trips-by-type-header">
                                <div class="wtwe-trips-by-type-inner-header">
                                    <div class="wtwe-trips-by-type-title-container <?php if( $settings[ 'show_trip_title' ] != 'yes' ) echo 'wtwe-hidden' ?>">
										<a href="<?php esc_url( the_permalink($trip_id) ) ?>" class="wtwe-trips-by-type-link">
											<h2 class="wtwe-trips-by-type-grid-title"><?php the_title(); ?></h2>
										</a>
                                    </div>
                                    <div class="wtwe-trips-by-type-grid-trip-rating <?php if( $settings[ 'show_trip_rating' ] != 'yes' ) echo 'wtwe-hidden' ?>">
                                        <i class="<?php echo esc_attr( $settings['trip_rating_icon']['value'] ); ?> <?php if( $settings[ 'show_trip_rating_icon' ] != 'yes' ) echo 'wtwe-hidden'; ?>" style="font-size:<?php echo esc_html( $settings['trip_rating_icon_size']['size'] ) . esc_html( $settings['trip_rating_icon_size']['unit'] ) ?>; color:<?php echo esc_html( $settings['trip_rating_icon_color'] ) ?>;"></i>
										<?php echo esc_html( wptravel_get_average_rating( $trip_id ) );
										if( $settings[ 'show_trip_max_rating' ] ) echo '/5' ; ?>
                                    </div>
                                </div>
                                <div class="wtwe-trips-by-type-trip-category <?php if( $settings[ 'show_tags' ] != 'yes' ) echo 'wtwe-hidden' ?>">
								<?php  $trip_type_list = get_the_terms( $trip_id, 'itinerary_types', true ); 
								
								if ( is_array( $trip_type_list ) && count( $trip_type_list ) > 0 ) { ?>
								<?php foreach ( $trip_type_list as $tax ) { 
									$term_link = get_term_link( $tax );
									if ( ! is_wp_error( $term_link ) ) {
										echo '<a href="' . esc_url( $term_link ) . '" class="wtwe-trips-by-type-grid-trip-tag">' . esc_html( $tax->name ) . '</a>';
									} else {
										echo '<span class="wtwe-trips-by-type-grid-trip-tag">' . esc_html( $tax->name ) . '</span>';
									}
									?>

								<?php } } ?>
                                </div>
                            </div>
                            <div class="wtwe-trips-by-type-content <?php if( $settings[ 'show_excerpt' ] != 'yes' ) echo 'wtwe-hidden' ?>">
                                <div class="wtwe-trips-by-type-excerpt">
									<?php 
										// esc_html( the_excerpt() );
										$excerpt_length = !empty($settings['trip_exerpt_length']) ? $settings['trip_exerpt_length'] : 25;
										$content = get_the_excerpt($trip_id);
										$trimmed_content = wp_trim_words($content, $excerpt_length, '...');
										?><p>
										 <?php echo esc_html( $trimmed_content ); ?>

										</p>
                                </div>
                            </div>
                        </div>
                        <div class="wtwe-trips-by-type-footer">
							<?php if($settings['show_trip_details'] == 'yes') { ?>
								<div class="wtwe-trips-by-type-details">
									<div class="wtwe-trips-by-type-pax">
										<i class="fa fa-user wtwe-trips-by-type-pax-icon" style="color:<?php echo esc_html( $settings[ 'trip_details_pax_icon_color' ] ) ?>;"></i>
										<div class="wtwe-trips-by-type-details-value">
											<?php echo esc_html( wptravel_get_group_size( $trip_id ) ); ?>
										</div>
									</div>
									<div class="wtwe-trips-by-type-datetime">
									<?php if( $is_fixed_departure ) { ?>
										<i class="fa fa-calendar wtwe-trips-by-type-pax-icon" style="color:<?php echo esc_html( $settings[ 'trip_details_duration_icon_color' ] ) ?>;"></i>
										<div class="wtwe-trips-by-type-details-value">
											<?php echo wp_kses_post( wptravel_get_fixed_departure_date( $trip_id ) ); ?>
										</div>
									<?php } else { ?>
										<i class="fa fa-clock wtwe-trips-by-type-pax-icon" style="color:<?php echo esc_html( $settings[ 'trip_details_duration_icon_color' ] ) ?>;"></i>
										<div class="wtwe-trips-by-type-details-value">
											<?php echo wp_kses_post( wp_travel_get_trip_durations( $trip_id ) ); ?>
										</div>
									<?php } ?>
								</div>
								</div>	
							<?php } ?>
							
							<?php if ( $trip_price > 0 ) { ?>
                            <div class="wtwe-trips-by-type-pricing">
                                <div class="wtwe-trips-by-type-pricing-text <?php if( $settings[ 'show_price_label' ] != 'yes' ) echo 'wtwe-hidden'; ?> ">
                                    <?php echo esc_html( $settings[ 'price_label' ] ); ?>
                                </div>
                                <div class="wtwe-trips-by-type-price ">
									<?php if ( isset( $regular_price ) && $regular_price > $trip_price ) { ?>
									<del class="wtwe-trips-by-type-sale-price">
										<?php echo wp_kses_post( wptravel_get_formated_price_currency( $regular_price ) ); ?>
									</del>

									<div class="wtwe-trips-by-type-regular-price">
										<?php echo wp_kses_post( wptravel_get_formated_price_currency( $trip_price ) ); ?>
									</div>
									<?php
									} else { ?>
									<div class="wtwe-trips-by-type-regular-price">
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
            <?php endif; ?>

            <?php if($settings['trips_type_design_grid'] == "design-1"): ?>
                <div class="wtwe-trips-by-type-wrapper wp-travel-itinerary-items wptravel-archive-wrapper  grid-view wtwe-trips-layout-grid" >
                    <?php
                    while ( $query->have_posts() ) :
                        $query->the_post();
                            wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
                    endwhile;
                    ?>
                </div>
            <?php endif; ?>
        <?php
			}
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
        <# 
            var trips = '';
			if( settings.content_type == "featured_trips" || settings.content_type == "trips_ids") {
				trips = ElementorConfig.wp_travel_trips_by_type[ settings.content_type ];
			} else if( settings.content_type == "itinerary_types" ) {
				trips = ElementorConfig.wp_travel_trips_by_type[ settings.content_type ][ settings.trips_type ];
			} else if( settings.content_type == "travel_locations" ) {
				trips = ElementorConfig.wp_travel_trips_by_type[ settings.content_type ][ settings.trips_destination ];
			} else if( settings.content_type == "activity" ) {
				trips = ElementorConfig.wp_travel_trips_by_type[ settings.content_type ][ settings.trips_activity ];
			}
			var tripImageControlClass = settings.show_image != 'yes' ? 'wtwe-hidden' : '';
			var tripBodyControlClass = settings.show_body != 'yes' ? 'wtwe-hidden' : '';
			var tripSaleBadgeControlClass = settings.show_trip_sale_badge != 'yes' ? 'wtwe-hidden' : '';
			var tripTitleControlClass = settings.show_trip_title != 'yes' ? 'wtwe-hidden' : '';
			var tripRatingControlClass = settings.show_trip_rating != 'yes' ? 'wtwe-hidden' : '';
			var tripRatingIconControlClass = settings.show_trip_rating_icon != 'yes' ? 'wtwe-hidden' : '';
			var tripMaxRatingControlClass = settings.show_trip_max_rating != 'yes' ? '' : '/5';
			var tripTagsControlClass = settings.show_tags != 'yes' ? 'wtwe-hidden' : '';
			var tripExcerptControlClass = settings.show_excerpt != 'yes' ? 'wtwe-hidden' : '';
			var tripDetailsControlClass = settings.show_trip_details != 'yes' ? 'wtwe-hidden' : '';
			var tripPriceLabelControlClass = settings.show_price_label != 'yes' ? 'wtwe-hidden' : '';
			

			if( typeof settings.trips_ids != 'undefined' && settings.content_type == 'trips_ids' ) {
				var trips_ids = settings.trips_ids.map((id) => {
					return parseInt(id);
				})
				trips = trips.filter( trip => trips_ids.includes(trip.trip_id))
			}

        if( settings.trips_type_design_grid == "design-2" ){
        #>
		<div class="wtwe-trips-by-type-wrapper wtwe-trips-layout-grid design-2">
		<#
			Array.isArray(trips) && trips.length > 0 && trips.map(( trip, index ) => {
				if(index+1 <= settings.trips_count) {
			var excerpt = trip.excerpt;			
			var excerptLength = settings.trip_exerpt_length;	
			
       		var trimmedExcerpt = excerpt.split(' ').slice(0, excerptLength).join(' ') + '...';

	
			#>
			<div class="wtwe-trips-by-type">
				<div class="wtwe-trips-by-type-img-container {{ tripImageControlClass }}">
					<a href="{{ trip.permalink }}" class="wtwe-trips-by-type-link">
                        {{{ trip.thumbnail_image }}}
						<#	if ( trip.trip_price > 0 && trip.regular_price > trip.trip_price ) { 
								var save = ( 1 - ( trip.trip_price / trip.regular_price ) ) * 100;
								save = save.toFixed(2);
								if( settings.show_trip_minimal_badge == 'yes' ) {
						#>
							<div class="wtwe-trips-by-type-discount {{ tripSaleBadgeControlClass }}">
								{{{ settings.minimal_badge_custom_text }}}
							</div>
						<# 		} else { #>
							<div class="wtwe-trips-by-type-discount {{ tripSaleBadgeControlClass }}">
								{{{ save }}}% off
							</div>
						<# 		} 
							}
						#>
					</a>
                </div>
				<div class="wtwe-trips-by-type-body {{ tripBodyControlClass }}">
					<div class="wtwe-trips-by-type-inner-body">
						<div class="wtwe-trips-by-type-header">
							<div class="wtwe-trips-by-type-inner-header">
								<div class="wtwe-trips-by-type-title-container {{ tripTitleControlClass }}">
									<a href="{{ trip.permalink }}" class="wtwe-trips-by-type-link">
										<h2 class="wtwe-trips-by-type-grid-title">{{{ trip.title }}}</h2>
									</a>
								</div>
								<div class="wtwe-trips-by-type-grid-trip-rating {{ tripRatingControlClass }}">
									<i class="{{ settings.trip_rating_icon.value }} {{ tripRatingIconControlClass }}"  style="font-size:{{settings.trip_rating_icon_size.size}}{{settings.trip_rating_icon_size.unit}};color:{{settings.trip_rating_icon_color}};"></i>
									{{{ trip.average_rating }}}{{{ tripMaxRatingControlClass }}}
								</div>
							</div>
							<div class="wtwe-trips-by-type-trip-category {{ tripTagsControlClass }}">
							<# trip.tags.forEach( ( tax, index ) => { #>
								<div class="wtwe-trips-by-type-grid-trip-tag">
									{{{ tax }}}
								</div>
							<# }); #>
							</div>
						</div>
						<div class="wtwe-trips-by-type-content {{ tripExcerptControlClass }}">
							<div class="wtwe-trips-by-type-excerpt">
								<p>{{{ trimmedExcerpt }}}</p>
							</div>
						</div>
					</div>
					<div class="wtwe-trips-by-type-footer">
						<div class="wtwe-trips-by-type-details {{ tripDetailsControlClass }}">
							<div class="wtwe-trips-by-type-pax">
								<i class="fa fa-user wtwe-trips-by-type-pax-icon" style="color:{{ settings.trip_details_pax_icon_color }};"></i>
								<div class="wtwe-trips-by-type-details-value">
									{{{ trip.pax }}}
								</div>
							</div>
							<div class="wtwe-trips-by-type-datetime">
								<# if( trip.is_fixed_departure ) { #>
									<i class="fa fa-calendar wtwe-trips-by-type-pax-icon" style="color:{{ settings.trip_details_duration_icon_color }};"></i>
								<# } else { #>
									<i class="fa fa-clock wtwe-trips-by-type-pax-icon" style="color:{{ settings.trip_details_duration_icon_color }};"></i>
								<# } #>
								<div class="wtwe-trips-by-type-details-value">
									{{{ trip.duration }}}
								</div>
							</div>
						</div>
						<# if ( trip.trip_price > 0 ) { #>
						<div class="wtwe-trips-by-type-pricing">
							<div class="wtwe-trips-by-type-pricing-text  {{ tripPriceLabelControlClass }}">
								{{{ settings.price_label }}}
							</div>
							<div class="wtwe-trips-by-type-price ">
								<# if ( trip.regular_price && trip.regular_price > trip.trip_price ) { #>
								<del class="wtwe-trips-by-type-sale-price">
									{{{ trip.regular_price_html }}}
								</del>

								<div class="wtwe-trips-by-type-regular-price">
									{{{ trip.trip_price_html }}}
								</div>
								<# } else { #>
								<div class="wtwe-trips-by-type-regular-price">
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
        <# } #>
		<# if( settings.trips_type_design_grid == "design-1" ){ #>

			<div class="wtwe-trips-by-type-wrapper wp-travel-itinerary-items wptravel-archive-wrapper grid-view design-1 wtwe-trips-layout-grid">
			<#
			Array.isArray(trips) && trips.length > 0 && trips.map(( trip, index ) => {
				if(index+1 <= settings.trips_count) {
			#>
				<div class="view-box">
					<div class="view-image {{ tripImageControlClass }}">
					<a
						href="{{ trip.permalink }}"
						class="image-thumb"
					>
						<div class="image-overlay"></div>
						{{{ trip.thumbnail_image }}}
					</a>
					<div class="offer">
						<span>{{trip.trip_code}}</span>
					</div>
					</div>

					<div class="view-content">
					<div class="left-content">
						<header>
						<h2 class="entry-title">
							<a
							class="heading-link"
							href="{{ trip.permalink }}"
							rel="bookmark"
							title="{{trip.title}}"
							>
							{{{trip.title}}}
							</a>
						</h2>
						</header>
						<div class="trip-icons">
						<div class="wp-travel-trip-time trip-duration">
							<i class="far fa-clock"></i>
							<span class="wp-travel-trip-duration"> {{{trip.duration}}} </span>
						</div>
						<div class="trip-location">
							<i class="fas fa-map-marker-alt"></i>
							<span>
							<a href="{{trip.trip_location}}"
								>{{{trip.trip_location}}}</a
							>
							<i class="fas fa-angle-down"></i>
							<ul>
								<li>
								<a href="{{trip.trip_location}}"
									>{{{trip.trip_location}}}</a
								>
								</li>
							</ul>
							</span>
						</div>
						<div class="group-size">
							<i class="fas fa-users"></i> <span><?php echo esc_html__( '7', 'wt-widgets-elementor' ); ?></span>
						</div>
						</div>
						<div class="trip-desc">
						<p>
							{{{trip.excerpt}}}
						</p>
						</div>
					</div>
					<div class="right-content">
						<div class="footer-wrapper">
						<div class="trip-price">
							<span class="discount"> <span><?php echo esc_html__('10.00%','wt-widgets-elementor'); ?></span> <?php echo esc_html__('Off','wt-widgets-elementor'); ?></span>
							<span class="price-here">
							<span class="wp-travel-trip-currency"><?php echo esc_html__( '$', 'wt-widgets-elementor' ); ?></span
							><span class="wp-travel-trip-price-figure">{{{trip.trip_price}}}</span>
							</span>
							<del>
							<span class="wp-travel-trip-currency"></span
							><span class="wp-travel-regular-price-figure">{{{trip.regular_price_html}}}</span>
							</del>
						</div>
						<div class="trip-rating">
							<div class="wp-travel-average-review">
							<div class="wp-travel-average-review" title="<?php echo esc_attr__('Rated 4 our of 5', 'wt-widgets-elementor'); ?>">
								<a>
								<span style="width: 80%">
									<strong itemprop="ratingValue" class="rating"><?php echo esc_html__( '4', 'wt-widgets-elementor'); ?></strong> <?php echo esc_html__( 'out', 'wt-widgets-elementor'); ?>
									of <span itemprop="bestRating">{{{trip.average_rating}}}</span>
								</span>
								</a>
							</div>
							</div>
							<span class="wp-travel-review-text"> <?php echo esc_html__( '(1 Reviews)', 'wt-widgets-elementor'); ?></span>
						</div>
						</div>

						<a
						class="wp-block-button__link explore-btn"
						href="{{trip.permalink}}"
						><span><?php echo esc_html__( 'Explore', 'wt-widgets-elementor'); ?></span></a
						>
					</div>
					</div>
				</div>

				<# }
			})
		#>
			</div>
		<# } #>

		<?php
		}
	}
}