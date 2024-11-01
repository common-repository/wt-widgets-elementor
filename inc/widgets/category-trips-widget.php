<?php
/**
 * Category Trips class.
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
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || exit;

/**
 * Category Trips widget class.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'WTWE_Category_Trips' ) ) {
	class WTWE_Category_Trips extends Widget_Base {
		/**
		 * Class constructor.
		 *
		 * @param array $data Widget data.
		 * @param array $args Widget arguments.
		 */
		public function __construct( $data = array(), $args = null ) {
			parent::__construct( $data, $args );
			$prefixed = defined( WP_DEBUG ) ? '.min' : '';
			wp_register_style( 'category-trips', plugins_url( 'assets/css/category-trips' . $prefixed . '.css', WTWE_PLUGIN_FILE ), array() );

            add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_slick_scripts' ) );
            add_action( 'elementor/frontend/after_register_scripts', array( $this, 'enqueue_slick_scripts' ) );
            add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_slick_scripts' ) );

            add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_slick_init_script' ) );
        }

        public function enqueue_slick_scripts() {
            wp_enqueue_style( 'slick-css', plugins_url( 'assets/libs/slick/slick.css', WTWE_PLUGIN_FILE), array() );
            wp_enqueue_style( 'slick-theme', plugins_url( 'assets/libs/slick/slick-theme.css', WTWE_PLUGIN_FILE), array() );
            wp_enqueue_script( 'slick-min-js', plugins_url( 'assets/libs/slick/slick.min.js', WTWE_PLUGIN_FILE), array(), true );
        }

        public function enqueue_slick_init_script() {
			$prefixed = defined( WP_DEBUG ) ? '.min' : '';
            wp_enqueue_script( 'category-trips-slick', plugins_url( 'assets/js/category-trips-slick' . $prefixed . '.js', WTWE_PLUGIN_FILE), array(), true );

			$categoryOptions = array(
				'autoplay' => 'yes',
				'tripsToShow' => 3,
				'infinite' => 'yes',
				'delay' => 500,
				'showArrows' => 'yes',
			);
		
			wp_localize_script('category-trips-slick', 'categoryOptions', apply_filters('wp_travel_elementor_widgets_localize_script', $categoryOptions));
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
			return 'wp-travel-category-trips';
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
			return esc_html__( 'Trip Category Carousel', 'wt-widgets-elementor' );
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
			return 'eicon-posts-carousel';
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
			return array( 'category-trips' );
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
					'tab'	=> Controls_Manager::TAB_CONTENT,
				)
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_control(
					'category_type',
					array(
						'label' => esc_html__( 'Category Type', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'itinerary_types',
						'options' => array(
							'itinerary_types' => esc_html__( 'Trip Types', 'wt-widgets-elementor' ),
							'travel_locations' => esc_html__( 'Trip Destinations', 'wt-widgets-elementor' ),
							'activity' => esc_html__( 'Activity', 'wt-widgets-elementor' ),
						),
					)
				);
            } else {
				$this->add_control(
					'category_type',
					array(
						'label' => esc_html__( 'Category Type', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'travel_locations',
						'options' => array(
							'travel_locations' => esc_html__( 'Trip Destinations', 'wt-widgets-elementor' ),
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

			

			$this->add_control(
				'trips_count',
				array(
					'label'	=> esc_html__( 'Trips Count', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 10,
					'step' => 1,
					'default' => 1,
				)
			);

			$this->add_control(  
                'autoplay',
                array(
                    'label' => esc_html__( 'Autoplay', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'On', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Off', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(  
                'infinite',
                array(
                    'label' => esc_html__( 'Infinite', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'On', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'Off', 'wt-widgets-elementor' ),
                    'default' => 'yes',
                ),
            );

			$this->add_control(  
                'trips_to_show',
                array(
                    'label' => esc_html__( 'Slides to show', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 10,
					'step' => 1,
					'default' => 1,
                ),
            );

			$this->add_control(  
                'delay',
                array(
                    'label' => esc_html__( 'Delay', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::NUMBER,
					'min' => 50,
					'max' => 10000,
					'step' => 100,
					'default' => 2000,
                ),
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'category_card_content',
				array(
					'label' => esc_html__( 'Category Card', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
                'category_trip_icon',
                array(
                    'label' => esc_html__( 'Category Icon', 'wt-widgets-elementor' ),
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
                'category_trip_count_icon',
                array(
                    'label' => esc_html__( 'Trip Count Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-plane',
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

			$this->end_controls_section();

			$this->start_controls_section(
				'arrows_section',
				array(
					'label' => 'Arrows',
					'tab' => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'show_arrows',
				[
					'label' => esc_html__( 'Show Arrows', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'SHOW', 'wt-widgets-elementor' ),
                    'label_off' => esc_html__( 'HIDE', 'wt-widgets-elementor' ),
                    'default' => 'yes',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
                'card_style_section',
                array(
                    'label' => esc_html__( 'Category Card', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );

            $this->add_control(
				'category_card_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors' => [
						'{{WRAPPER}} .wtwe-category-trip-wrapper .wtwe-category-trip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'category_card_width',
				[
					'label' => esc_html__( 'Width', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', '%', 'vw'],
					'range' => [
						'px' => [
							'min' => 200,
							'max' => 500,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wtwe-category-trip' => 'width: {{SIZE}}{{UNIT}} !important;',
					],
				]
			);
	
			$this->add_responsive_control(
				'category_card_gap',
				[
					'label' => esc_html__( 'Gap', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', 'em'],
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
						'{{WRAPPER}} .wtwe-category-trip-wrapper .slick-list .slick-track' => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
                'card_category_text_style',
                array(
                    'label' => esc_html__( 'Category Text', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );

			$this->add_control(
				'card_category_text_color',
				array(
					'label' => esc_html__( 'Text Color', 'wt-widgets-elementor' ),
					'type'	=> Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .wtwe-category-trip .wtwe-trip-content .wtwe-trip-info .wtwe-trip-attr' => 'color: {{VALUE}}',
					),
				),
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'card_category_text_typography',
						'selector'	=> '{{ WRAPPER }} .wtwe-category-trip .wtwe-trip-content .wtwe-trip-info .wtwe-trip-attr',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					array(
						'name' => 'card_category_text_stroke',
						'selector' => '{{ WRAPPER }} .wtwe-category-trip .wtwe-trip-content .wtwe-trip-info .wtwe-trip-attr',
					)
				);
	
				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => 'card_category_text_shadow',
						'selector' => '{{ WRAPPER }} .wtwe-category-trip .wtwe-trip-content .wtwe-trip-info .wtwe-trip-attr',
					)
				);
            } 

			

            $this->end_controls_section();

			$this->start_controls_section(
				'card_category_icon_section',
				array(
					'label' => esc_html__( 'Category Icons', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_responsive_control(
				'category_trip_icon_size',
				[
					'label' => esc_html__( 'Trip Category Icon Size', 'wt-widgets-elementor' ),
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
							'min' => 1,
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
						'size' => 16,
					],
				]
			);

			$this->add_control(
                'category_trip_icon_color',
                array(
                    'label' => esc_html__( 'Trip Category Icon Color', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#38A827',
                )
            );

			$this->add_control(
				'hr',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);

			$this->add_responsive_control(
				'category_trip_count_icon_size',
				[
					'label' => esc_html__( 'Trip Count Icon Size', 'wt-widgets-elementor' ),
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
							'min' => 1,
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
						'size' => 16,
					],
				]
			);

			$this->add_control(
                'category_trip_count_icon_color',
                array(
                    'label' => esc_html__( 'Trip Count Icon Color', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#38A827',
                )
            );

			$this->end_controls_section();

			$this->start_controls_section(
				'arrows_style_section',
				array(
					'label' => esc_html__( 'Arrows', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);
			$this->add_control(
                'arrows_text_color',
                array(
                    'label' => 'Text Color',
                    'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-prev::before, .wtwe-category-trip-wrapper.slick-slider .slick-next::before' => 'color:{{VALUE}};',
					],
                )
            );
			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_control(
					'arrows_padding',
					[
						'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%', 'em', 'rem'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-prev::before, {{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-next::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'arrows_background',
						'types' => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-prev::before, {{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-next::before'
					]
				);
	
				$this->add_control(
					'slider_arrows_border_radius',
					[
						'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-prev::before, {{WRAPPER}} .wtwe-category-trip-wrapper.slick-slider .slick-next::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
            }
			
			$this->end_controls_section();

			$this->start_controls_section(
				'button_style_section',
				array(
					'label' => esc_html__( 'Button', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'button_color',
				array(
					'label' => esc_html__( 'Button Color', 'wt-widgets-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wtwe-category-trip-wrapper .wtwe-category-trip .wtwe-trip-overlay .wtwe-category-action-btn' => 'color: {{VALUE}};',
					),
				)
			);
			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_control(
					'button_background',
					array(
						'label' => esc_html__( 'Button Background', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .wtwe-category-trip-wrapper .wtwe-category-trip .wtwe-trip-overlay .wtwe-category-action-btn' => 'background-color: {{VALUE}};',
						),
					)
				);
	
				$this->add_responsive_control(
					'category_button_padding',
					[
						'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%', 'rem', 'em'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-category-trip-wrapper .wtwe-category-trip .wtwe-trip-overlay .wtwe-category-action-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
	
				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name' => 'border',
						'selector' => '{{WRAPPER}} .wtwe-category-trip-wrapper .wtwe-category-trip .wtwe-trip-overlay .wtwe-category-action-btn',
					)
				);
	
				$this->add_control(
					'category_button_border_radius',
					[
						'label' => esc_html__( 'Border Radius', 'wt-widgets-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%'],
						'selectors' => [
							'{{WRAPPER}} .wtwe-category-trip-wrapper .wtwe-category-trip .wtwe-trip-overlay .wtwe-category-action-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
            }
			

			$this->end_controls_section();
			
		// 	add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_slick_init_script' ) );
		// 	add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_slick_init_script' ) );
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
		public $settings = [];
		
		protected function render() {
			$settings = $this->get_settings_for_display();
			$this->settings = $settings;

			add_filter( 'wp_travel_elementor_widgets_localize_script', function ( $args ) {
				$args[ 'autoplay' ] = isset( $this->settings[ 'autoplay' ] ) ? $this->settings[ 'autoplay' ] : 'false';
				$args[ 'infinite' ] = isset( $this->settings[ 'infinite' ] ) ? $this->settings[ 'infinite' ] : 'false';
				$args[ 'showArrows' ] = isset( $this->settings[ 'show_arrows' ] ) ? $this->settings[ 'show_arrows' ] : 'false';
				$args[ 'tripsToShow' ] = isset( $this->settings[ 'trips_to_show' ] ) ? $this->settings[ 'trips_to_show' ] : 3;
				$args[ 'speed' ] = isset( $this->settings[ 'speed' ] ) ? (int) $this->settings[ 'speed' ] : 500;
				$args[ 'delay' ] = isset( $this->settings[ 'delay' ] ) ? (int) $this->settings[ 'delay' ] : 2000;
				return $args;
			} );

			$terms = get_terms(
				array(
					'taxonomy'   => $settings[ 'category_type' ],
					'hide_empty' => false,
					'number' => $settings[ 'trips_count' ]
				)
			);

			$itinerary_post_type_slug = get_post_type_archive_link('itineraries');

			?>
				
			<div class="wtwe-category-trip-wrapper <?php if( $settings[ 'show_arrows' ] != 'yes' ) echo esc_attr( 'no-arrow' ); ?> ">
				<?php
				foreach( $terms as $key => $term_value ) {
					$term_id = $term_value->term_id;
					$term_name = $term_value->name;
					$thumbnail_id = get_term_meta($term_id, 'wp_travel_trip_type_image_id', true);
					$term_image_url = wp_get_attachment_image_url($thumbnail_id, 'full'); 

					$query_slug = '';
					if ( strpos( $itinerary_post_type_slug, '?' ) == true ) {
						$query_slug = $itinerary_post_type_slug . '&' . $settings[ 'category_type' ]. '=' .  $term_value->slug;
					} else {
						$query_slug = $itinerary_post_type_slug . '?' .  $settings[ 'category_type' ] . '=' . $term_value->slug;
					}
				?>
				<div class="wtwe-category-trip">
					<div class="wtwe-trip-image-container">
						<img src="<?php echo esc_url( $term_image_url ) ?>" />
					</div>
					<div class="wtwe-trip-overlay">
						<a href="<?php echo esc_url( $query_slug )  ?>" class="wp-block-button">
							<button class="wtwe-category-action-btn wp-block-button__link"><?php echo esc_html__( 'View Trips', 'wt-widgets-elementor' ) ?></button>
						</a>
					</div>
					<div class="wtwe-trip-content">
						<div class="wtwe-trip-info">
							<div class="wtwe-trip-attr">
								<i class="<?php echo esc_attr( $settings['category_trip_icon']['value'] ) ?>" style="font-size:<?php echo esc_attr( $settings['category_trip_icon_size']['size']) . esc_attr( $settings['category_trip_icon_size']['unit'] ) ?>; color:<?php echo esc_attr( $settings['category_trip_icon_color'] ) ?>;"></i>
								<span class="wtwe-trip-attr-value"><?php echo esc_html( $term_name ); ?></span>
							</div>
							<div class="wtwe-trip-attr">
								<i class="<?php echo esc_attr( $settings['category_trip_count_icon']['value'] ) ?>" style="font-size:<?php echo esc_attr( $settings['category_trip_count_icon_size']['size'] ) . esc_attr( $settings['category_trip_count_icon_size']['unit'] ) ?>; color:<?php echo esc_attr( $settings['category_trip_count_icon_color'] ) ?>;"></i>
								<span class="wtwe-trip-attr-value"><?php echo esc_html( $term_value->count ) ?><?php echo esc_html__( ' Trips', 'wt-widgets-elementor') ?></span>
							</div>
						</div>
					</div>
				</div>
				<?php
				}
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
			<#
				var terms = ElementorConfig.wp_travel_category_trips[ settings.category_type ];
				var arrow_option = (settings.show_arrows === 'yes'); // Correct way to assign boolean
			#>
				<div class="wtwe-category-trip-wrapper">
					<#
					terms.forEach( ( term, index ) => {
						if( index+1 <= settings.trips_count ) {
					#>
					<div class="wtwe-category-trip">
						<div class="wtwe-trip-image-container">
							<img src="{{ term.term_image_url }}" />
						</div>
						<div class="wtwe-trip-overlay">
							<a href="{{ term.query_slug }}" class="wp-block-button">
								<button class="wtwe-category-action-btn wp-block-button__link"><?php echo esc_html__('View Trips', 'wt-widgets-elementor'); ?></button>
							</a>
						</div>
						<div class="wtwe-trip-content">
							<div class="wtwe-trip-info">
								<div class="wtwe-trip-attr">
									<i class="{{ settings.category_trip_icon.value }}" style="font-size:{{ settings.category_trip_icon_size.size }}{{ settings.category_trip_icon_size.unit }};color:{{ settings.category_trip_icon_color }};"></i>
									<span class="wtwe-trip-attr-value">{{{ term.term_name }}}</span>
								</div>
								<div class="wtwe-trip-attr">
									<i class="{{ settings.category_trip_count_icon.value }}" style="font-size:{{settings.category_trip_count_icon_size.size}}{{settings.category_trip_count_icon_size.unit}};color:{{settings.category_trip_count_icon_color}};"></i>
									<span class="wtwe-trip-attr-value">{{{ term.trip_count }}} <?php echo esc_html__( ' Trips', 'wt-widgets-elementor') ?></span>
								</div>
							</div>
						</div>
					</div>
					<#    }
					})
					#>
				</div>
				<script>
					jQuery(function($) {
						var arrow_option = {{{ arrow_option ? 'true' : 'false' }}};
						$('.wtwe-category-trip-wrapper').slick({
							arrows: arrow_option,
							dots: false,
							infinite: true,
							autoplay: true,
							speed: 300,
							autoplaySpeed: parseInt("{{{ settings.delay }}}"),
							slidesToShow: parseInt("{{{ settings.trips_to_show }}}"),
							slidesToScroll: 1,
						});
					})
				</script>
			<?php
		}
		
		
	}
}