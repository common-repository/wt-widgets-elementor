<?php

/**
 * Trips Search class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets;

use WP_Travel;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || exit;

/**
 * Trip Search widget class.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'WTWE_Trips_Search' ) ) {
    class WTWE_Trips_Search extends Widget_Base {
        /**
		 * Class constructor.
		 *
		 * @param array $data Widget data.
		 * @param array $args Widget arguments.
		 */
		public function __construct( $data = array(), $args = null ) {
			parent::__construct( $data, $args );

			add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'register_search_scripts' ] );
		}

		public function register_search_scripts() {
			$prefixed = defined( WP_DEBUG ) ? '.min' : '';
			wp_register_style( 'trips-search-style', plugins_url( 'assets/css/trips-search' . $prefixed . '.css', WTWE_PLUGIN_FILE ), array(), WTWE_VERSION );
			wp_register_script( 'trips-search-script', plugin_dir_url( WTWE_PLUGIN_FILE ) . 'assets/js/trips-search' . $prefixed . '.js', array( 'jquery' ), WTWE_VERSION, true );
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
			return 'wp-travel-trip-search-form';
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
			return esc_html__( 'Trips Search', 'wt-widgets-elementor' );
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
			return 'eicon-site-search';
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
		 * Enqueue the scripts that the widget depends on.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 *
		 * @return array dependency scripts.
		 */
		public function get_script_depends() {
			return array( 'jquery', 'trips-search-script' );
		}

		public function get_style_depends() {
			return array( 'trips-search-style' );
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
        public static function wtwe_handle_content_type( $content_type ) {
            $content = array();
			$terms     = get_terms(
				array(
					'taxonomy'   => $content_type,
					'hide_empty' => false,
				)
			);

			if ( is_array( $terms ) && count( $terms ) > 0 ) {
				foreach ( $terms as $key => $term ) {
					$slug = ! empty( $term->slug ) ? $term->slug : '';
					$content[ '' ] = "All";
					$content[ $slug ] = ! empty( $term->name ) ? $term->name : '';
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
			$this->start_controls_section(
				'general_content',
				array(
					'label' => esc_html__( 'General', 'wt-widgets-elementor' ),
				)
			);

			$this->add_control(
				'trips_search_layout',
				[
					'label' => esc_html__( 'Layout', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'row' => esc_html__( 'Horizontal', 'wt-widgets-elementor' ),
						'column' => esc_html__( 'Vertical', 'wt-widgets-elementor' ),
					],
					'default' => 'row',
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper' => 'flex-direction:{{VALUE}}',
					]
				]
			);

			$this->add_responsive_control(
				'selectors_gap',
				[
					'label' => esc_html__( 'Gap', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', 'em'],
					'default' => [
						'size' => 10,
						'unit' => 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
						'em' => [
							'min' => 0,
							'max' => 10,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'show_trip_search_input',
				[
					'label' => esc_html__( 'Search Input', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);

			$this->end_controls_section();
			
			$this->start_controls_section(
				'trip_type_selector_content',
				array(
					'label' => esc_html__( 'Trip Type Selector', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				),
			);

			$this->add_responsive_control(
				'show_trip_type_select',
				[
					'label' => esc_html__( 'Trip Type Selector', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);

			$this->add_responsive_control(
				'show_trip_type_icon',
				[
					'label' => esc_html__( 'Show Icon', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);

			$this->add_control(
                'trip_type_icon',
                array(
                    'label' => esc_html__( 'Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-gem',
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

			$this->add_control(
				'trip_type_placeholder_text',
				[
					'label' => esc_html__( 'Placeholder Text', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Trip Type', 'wt-widgets-elementor' ),
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'trip_destination_selector_content',
				array(
					'label' => esc_html__( 'Trip Destination Selector', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				),
			);

			$this->add_control(
				'show_trip_destination_select',
				[
					'label' => esc_html__( 'Destination Selector', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);

			$this->add_responsive_control(
				'show_trip_destination_icon',
				[
					'label' => esc_html__( 'Icon', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);
			
            $this->add_control(
                'trip_destination_icon',
                array(
                    'label' => esc_html__( 'Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-map-marker-alt',
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

			$this->add_control(
				'trip_destination_placeholder_text',
				[
					'label' => esc_html__( 'Placeholder Text', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Destination', 'wt-widgets-elementor' ),
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'dropdown_box_content',
				array(
					'label' => esc_html__( 'Dropdown Box', 'wt-widgets-elementor' ),
				),
			);

			$this->add_responsive_control(
				'dropdown_box_max_height',
				[
					'label' => esc_html__( 'Max Height', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', '%', 'vh'],
					'default' => [
						'size' => 400,
						'unit' => 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
						'vh' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-dropdown-box' => 'max-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'search_button_content',
				array(
					'label' => esc_html__( 'Search Button', 'wt-widgets-elementor' ),
				),
			);

			$this->add_control(
				'show_trip_search_button',
				[
					'label' => esc_html__( 'Show Button', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_trip_search_icon',
				[
					'label' => esc_html__( 'Show Icon', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'wt-widgets-elementor' ),
					'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor' ),
					'default' => 'yes',
				]
			);
			
            $this->add_control(
                'trip_search_icon',
                array(
                    'label' => esc_html__( 'Search Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-search',
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
					'condition' => [
						'show_trip_search_icon' => 'yes',
					]
                )
            );

			$this->add_responsive_control(
				'trip_search_icon_position',
				[
					'label' => esc_html__( 'Icon Position', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'row' => esc_html__( 'Before', 'wt-widgets-elementor' ),
						'row-reverse' => esc_html__( 'After', 'wt-widgets-elementor' ),
					],
					'default' => 'row',
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-btn' => 'flex-direction:{{VALUE}};',
					],
				],
			);

			$this->add_responsive_control(
				'search_button_icon_gap',
				[
					'label' => esc_html__( 'Icon Gap', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', 'em'],
					'default' => [
						'size' => 10,
						'unit' => 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
						'em' => [
							'min' => 0,
							'max' => 10,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn' => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'search_button_text',
				[
					'label' => esc_html__( 'Button Text', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Search', 'wt-widgets-elementor' ),
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'selector_style',
				array(
					'label' => esc_html__( 'Selectors', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				),
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'selector_typography',
					'selector'	=> '{{ WRAPPER }} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name' => 'selector_text_stroke',
					'selector' => '{{ WRAPPER }} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'selector_text_shadow',
					'selector' => '{{ WRAPPER }} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper',
				)
			);

			$this->start_controls_tabs(
				'selector_style_tabs'
			);
			
			$this->start_controls_tab(
				'selector_style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'wt-widgets-elementor' ),
				]
			);

			$this->add_control(
				'selectors_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'selectors_border',
                    'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper',
                )
            );

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'selectors_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper',
				]
			);
			
			$this->end_controls_tab();

			$this->start_controls_tab(
				'selectors_style_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'wt-widgets-elementor' ),
				]
			);

			$this->add_control(
				'selectors_border_radius_hover',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'selectors_border_hover',
                    'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper:hover',
                )
            );

			
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'selectors_box_shadow_hover',
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-wrapper:hover',
				]
			);
			
			$this->end_controls_tab();
			
			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->start_controls_section(
				'dropdown_box_style',
				array(
					'label' => esc_html__( 'Dropdown Box', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				),
			);

			$this->add_control(
				'dropdown_box_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-dropdown-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'dropdown_box_border',
                    'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-dropdown-box',
                )
            );

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'dropdown_box_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-select-dropdown .wtwe-select-dropdown-box',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'search_button_style',
				array(
					'label' => esc_html__( 'Search Button', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				),
			);

			$this->start_controls_tabs(
				'search_button_style_tabs'
			);
			
			$this->start_controls_tab(
				'search_button_style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'wt-widgets-elementor' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'search_button_icon_color',
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn',
				]
			);

			$this->add_control(
				'',
				array(
					'label' => 'Background Color',
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'',
					]
				)
			);

			$this->add_control(
				'search_button_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'search_button_border',
                    'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn',
                )
            );

			
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'search_button_box_shadow',
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn',
				]
			);
			
			$this->end_controls_tab();

			$this->start_controls_tab(
				'search_button_style_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'wt-widgets-elementor' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'search_button_icon_color_hover',
					'types' => [ 'classic', 'gradient', 'video' ],
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn:hover',
				]
			);

			$this->add_control(
				'search_button_border_radius_hover',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'search_button_border_hover',
                    'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn:hover',
                )
            );

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'search_button_box_shadow_hover',
					'selector' => '{{WRAPPER}} .wtwe-trips-search-wrapper .wtwe-trips-search-btn:hover',
				]
			);
			
			$this->end_controls_tab();
			
			$this->end_controls_tabs();

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

			$trip_type_options = \WTWE\Widgets\WTWE_Trips_Search::wtwe_handle_content_type( 'itinerary_types' );
			$trip_destinations_options = \WTWE\Widgets\WTWE_Trips_Search::wtwe_handle_content_type( 'travel_locations' );
			// $trip_activity_options = \WTWE\Widgets\WTWE_Trips_Search::wtwe_handle_content_type( 'activity' );

			$unique_id = uniqid();

			?>
			<div class="wtwe-trips-search-wrapper">
				
				<div class="wtwe-select-dropdown selectDropdown <?php if( $settings[ 'show_trip_type_select' ] != 'yes' ) echo 'wtwe-hidden'; ?>">
					<select name="itinerary_types" class="wtwe-search-widget-filters-input<?php echo esc_attr( $unique_id ); ?>" placeholder="Trips Type">
					<?php foreach( $trip_type_options as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
					</select>
					<ul class="wtwe-select-dropdown-box">
					<?php foreach( $trip_type_options as $key => $value ) : ?>
						<li>
							<a><?php echo esc_html( $value ); ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
					<div class="wtwe-select-wrapper wtwe-trips-type-selector">
						<?php if( $settings[ 'show_trip_type_icon' ] == 'yes' ) : ?>
						<i class="<?php echo esc_attr( $settings[ 'trip_type_icon' ][ 'value' ] ); ?>"></i>
						<?php endif; ?>
						<span class="wtwe-trip-type-selector-placeholder"><?php echo esc_html( $settings[ 'trip_type_placeholder_text' ] ); ?></span>
					</div>
				</div>

				<div class="wtwe-select-dropdown selectDropdown <?php if( $settings[ 'show_trip_destination_select' ] != 'yes' ) echo 'wtwe-hidden'; ?>">
					<select name="travel_locations" class="wtwe-trips-type-select wtwe-search-widget-filters-input<?php echo esc_attr( $unique_id ); ?>" placeholder="Destination">
					<?php foreach( $trip_destinations_options as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
					</select>
					<ul class="wtwe-select-dropdown-box">
					<?php foreach( $trip_destinations_options as $key => $value ) : ?>
						<li>
							<a><?php echo esc_html( $value ); ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
					<div class="wtwe-select-wrapper wtwe-trips-destination-selector">
						<?php if( $settings[ 'show_trip_destination_icon' ] == 'yes' ) : ?>
						<i class="<?php echo esc_attr( $settings[ 'trip_destination_icon' ][ 'value' ] ); ?>"></i>
						<?php endif; ?>
						<span class="wtwe-trip-destination-selector-placeholder"><?php echo esc_html( $settings[ 'trip_destination_placeholder_text' ] ); ?></span>
					</div>
				</div>

				<div class="wtwe-select-dropdown filled <?php if( $settings[ 'show_trip_search_input' ] != 'yes' ) echo 'wtwe-hidden'; ?>">
					<input name="wts" type="text" class="wtwe-select-wrapper wtwe-trips-search-input wtwe-search-widget-filters-input<?php echo esc_attr( $unique_id ); ?>" placeholder="E.g. Trekking">
				</div>

				<input name="itinerary_results" value="<?php echo esc_url( site_url() ) . '?post_type=itineraries'; ?>" type="hidden" class="wtwe-itinerary-archive-page-url" />
				<input class="wtwe-search-widget-filters-input<?php echo esc_attr( $unique_id ); ?>" type="hidden" name="_nonce"  value="<?php echo esc_attr( WP_Travel::create_nonce() ); ?>" >
				<input class="wtwe-filter-data-index" type="hidden" data-index="<?php echo esc_attr( $unique_id ); ?>">

				<button class="wtwe-trips-search-btn <?php if( $settings[ 'show_trip_search_button' ] != 'yes' ) echo 'wtwe-hidden'; ?>">
					<?php if( $settings[ 'show_trip_search_icon' ] == 'yes' ) : ?>
					<i class="<?php echo esc_attr( $settings[ 'trip_search_icon' ][ 'value' ] ); ?>"></i>
					<?php endif; ?>
					<span><?php echo esc_html( $settings[ 'search_button_text' ] ); ?></span>
				</button>
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
		}
    }
}