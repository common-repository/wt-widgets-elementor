<?php

/**
 * Meta Trip Type class.
 *
 * @category   Class
 * @package    WPTravelElementorWidgetsExtended
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_Page_Trip_Meta_Type;

use WP_Travel_Helpers_Trip_Dates;
use WTWE_Helper;

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Meta Trip Type widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('Trip_Meta_Type')) {
	class Trip_Meta_Type extends Widget_Base
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
			wp_register_style('meta-trip-type', plugins_url('assets/css/meta-trip-type.min' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
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
			return 'wp-travel-meta-trip-type';
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
			return esc_html__('Single Trip Meta', 'wt-widgets-elementor');
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
			return 'eicon-meta-data';
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
			return array('meta-trip-type');
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
			$this->start_controls_section(
				'section_content',
				[
					'label' => esc_html__('Content', 'wt-widgets-elementor'),
				]
			);

			$this->add_control(
				'content_type',
				[
					'label' => esc_html__('Meta Type', 'wt-widgets-elementor'),
					'type' => Controls_Manager::SELECT,
					'default' => 'itinerary_types',
					'options' => [
						'itinerary_types'	=>	__('Itinerary Type', 'wt-widgets-elementor'),
						'travel_locations'	=>	__('Trip Destination', 'wt-widgets-elementor'),
						'activity'	=>	__('Trip Activity', 'wt-widgets-elementor'),
						'group_size'	=>	__('Group Size', 'wt-widgets-elementor'),
						'review_count'	=>	__('Review Count', 'wt-widgets-elementor'),
						'trip_duration'	=>	__('Trip Duration/Date', 'wt-widgets-elementor'),
						'travel_keywords'	=>	__('Trip Keyword', 'wt-widgets-elementor'),

					],
				]
			);

			$this->add_control(
				'show_meta_content_dropdown',
				[
					'label' => esc_html__('Enable Dropdown', 'wt-widgets-elementor'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('Yes', 'wt-widgets-elementor'),
					'label_off' => esc_html__('No', 'wt-widgets-elementor'),
					'default' => 'no',
					'condition' => [
						'content_type!' => ['trip_duration', 'group_size', 'review_count'],
					]
				]
			);


			$this->end_controls_section();

			$this->start_controls_section(
				'title_content',
				[
					'label' => esc_html__('Title', 'wt-widgets-elementor'),
				]
			);

			$this->add_control(
				'show_meta_trip_type_title',
				[
					'label' => esc_html__('Show Title', 'wt-widgets-elementor'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
					'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
					'default' => 'yes',
				]
			);

			$this->add_control(
				'meta_trip_type_title',
				[
					'label' => esc_html__('Meta Title', 'wt-widgets-elementor'),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__('Meta Title', 'wt-widgets-elementor'),
					'label_block' => true,
					'condition' => [
						'show_meta_trip_type_title' => 'yes',
						'content_type!' => 'trip_duration',
					]
				]
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_control(
					'meta_trip_type_title_html_tag',
					array(
						'label'	=> esc_html__('HTML Tag', 'wt-widgets-elementor'),
						'type'	=> Controls_Manager::SELECT,
						'default' => 'h2',
						'options' => array(
							'h1'	=>	__('H1', 'wt-widgets-elementor'),
							'h2'	=>	__('H2', 'wt-widgets-elementor'),
							'h3'	=>	__('H3', 'wt-widgets-elementor'),
							'h4'	=>	__('H4', 'wt-widgets-elementor'),
							'h5'	=>	__('H5', 'wt-widgets-elementor'),
							'h6'	=>	__('H6', 'wt-widgets-elementor'),
							'span'	=>	__('span', 'wt-widgets-elementor'),
							'div'	=>	__('div', 'wt-widgets-elementor'),
							'p'	=>	__('p', 'wt-widgets-elementor'),
						),
					),
				);
			} else {
				$this->add_control(
					'meta_trip_type_title_html_tag',
					array(
						'label'	=> esc_html__('HTML Tag', 'wt-widgets-elementor'),
						'type'	=> Controls_Manager::SELECT,
						'default' => 'h5',
						'options' => array(
							'h5'	=>	__('H5', 'wt-widgets-elementor'),
						),
					),
				);
			}
			$this->end_controls_section();

			$this->start_controls_section(
				'meta_trip_value_content',
				[
					'label' => esc_html__('Meta Value', 'wt-widgets-elementor'),
				]
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_responsive_control(
					'trip_type_separator',
					array(
						'label' => esc_html__('Separator', 'wt-widgets-elementor'),
						'type' => Controls_Manager::SELECT,
						'default' => '0',
						'options' => [
							'0' => esc_html__('None', 'wt-widgets-elementor'),
							'1' => esc_html__(',', 'wt-widgets-elementor'),
							'2' => esc_html__('-', 'wt-widgets-elementor'),
							'3' => esc_html__('.', 'wt-widgets-elementor'),
							'4' => esc_html__('_', 'wt-widgets-elementor'),
							'5' => esc_html__('*', 'wt-widgets-elementor'),
						],
					),
				);
			} else {
				$this->add_responsive_control(
					'trip_type_separator',
					array(
						'label' => esc_html__('Separator', 'wt-widgets-elementor'),
						'type' => Controls_Manager::SELECT,
						'default' => '1',
						'options' => [
							'0' => esc_html__('None', 'wt-widgets-elementor'),
							'1' => esc_html__(',', 'wt-widgets-elementor'),
						],
					),
				);
			}

			$this->end_controls_section();

			$this->start_controls_section(
				'meta_trip_type_title_styles',
				[
					'label' => esc_html__('Title', 'wt-widgets-elementor'),
					'tab'	=> Controls_Manager::TAB_STYLE,
				]
			);

			if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
				$this->add_control(
					'meta_trip_type_title_color',
					[
						'label' => esc_html__('Text Color', 'wt-widgets-elementor'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-type-title' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'meta_trip_type_title_typography',
						'selector' => '{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-type-title',
					]
				);

				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					[
						'name' => 'meta_trip_type_title_text_stroke',
						'selector' => '{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-type-title',
					]
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					[
						'name' => 'meta_trip_type_title_text_shadow',
						'selector' => '{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-type-title',
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'meta_trip_values_styles',
					[
						'label' => esc_html__('Meta Values', 'wt-widgets-elementor'),
						'tab'	=> Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'meta_trip_type_values_color_title',
					[
						'label' => esc_html__('Title Text Color', 'wt-widgets-elementor'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-type-title' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'meta_trip_type_values_color',
					[
						'label' => esc_html__('Text Color', 'wt-widgets-elementor'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type a' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-type-title span' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-pax' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-reviews' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-duration' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-date' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'meta_trip_type_values_typography',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type a',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-pax',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-reviews',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-duration',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-date',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Text_Stroke::get_type(),
					[
						'name' => 'meta_trip_type_values_text_stroke',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type a',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-pax',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-reviews',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-duration',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-date',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					[
						'name' => 'meta_trip_type_values_text_shadow',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type a',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-pax',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-reviews',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-duration',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-date',
						],
					]
				);

				$this->end_controls_section();
			} else {

				$this->add_control(
					'meta_trip_type_values_color',
					[
						'label' => esc_html__('Text Color', 'wt-widgets-elementor'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .wtwe-meta-trip-type a' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-pax' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-reviews' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-duration' => 'color: {{VALUE}}',
							'{{WRAPPER}} .wtwe-meta-trip-type .wtwe-meta-trip-date' => 'color: {{VALUE}}',
						],
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
			global $post;
			$settings = $this->get_settings_for_display();

			$trip_id = get_the_ID();
			$group_size = wptravel_get_group_size($trip_id);
			$review_count = (int) get_comments_number($trip_id);

			$separators = ['', ', ', ' - ', ' . ', ' _ ', ' * '];
			$content_types = ['itinerary_types', 'travel_locations', 'activity', 'travel_keywords'];
			foreach ($content_types as $content_type) {
				foreach ($separators as $separator) {
					$trip_types_list[$content_type][] = get_the_term_list($trip_id, $content_type, '', $separator, '');
				}
			}

			$this->add_render_attribute(
				'meta_trip_type_title',
				[
					'class' => esc_attr('wtwe-meta-trip-type-title'),
				]
			);

			if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
				$trip_id = get_the_ID();
				$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure($trip_id);
				$date_label = $fixed_departure ? 'Fixed Departure' : 'Duration';
				$empty_group_size_text = __('No Size Limit', 'wt-widgets-elementor');
				$strings               = \WpTravel_Helpers_Strings::get();
				$pax_text              = isset($strings['bookings']['pax']) ? $strings['bookings']['pax'] : __('Pax', 'wt-widgets-elementor');

?>
				<div class="wtwe-meta-trip-type">
					<?php if ($settings['show_meta_trip_type_title'] == 'yes') { ?>
						<<?php echo esc_html($settings['meta_trip_type_title_html_tag']) . ' ' . wp_kses_post($this->get_render_attribute_string('meta_trip_type_title')) ?>>
							<?php if ($settings['content_type'] == 'trip_duration') {
								echo esc_html($date_label);
							} else {
								echo esc_html($settings['meta_trip_type_title']);
							}
							?>
						</<?php echo esc_html($settings['meta_trip_type_title_html_tag']) ?>>
						<?php }

					if ($settings['content_type'] == 'itinerary_types' || $settings['content_type'] == 'travel_locations' || $settings['content_type'] == 'activity' || $settings['content_type'] == 'travel_keywords') {
						// echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
						if ($settings['content_type'] == 'itinerary_types') {
						?>
							<div class="wtwe-meta-travel-location-dropdown-wrapper">
								<?php
								if ($settings['show_meta_content_dropdown'] == 'yes') {
									$itinerary_types_list = get_the_term_list($trip_id, 'itinerary_types', '', $settings['trip_type_separator'], '');
									if (!is_wp_error($itinerary_types_list) && $itinerary_types_list !== false) {
										$itinerary_types_list = explode($settings['trip_type_separator'], $itinerary_types_list);

								?>
										<?php echo wp_kses_post($itinerary_types_list[0]);

										if ($itinerary_types_list !== '' && count($itinerary_types_list) > 1) {
										?><i class="fas fa-angle-down"></i>
										<?php } ?>
										<div class="wtwe-meta-travel-location-dropdown-content">
											<?php
											foreach ($itinerary_types_list as $list) {
												echo wp_kses_post($list);
											}
											?>
										</div>
								<?php
									}
								} else {
									echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
								}
								?>
							</div>
						<?php
						} elseif ($settings['content_type'] == 'travel_locations') {
						?>
							<div class="wtwe-meta-travel-location-dropdown-wrapper">
								<?php
								if ($settings['show_meta_content_dropdown'] == 'yes') {
									$travel_locations_list = get_the_term_list($trip_id, 'travel_locations', '', $settings['trip_type_separator'], '');
									if (!is_wp_error($travel_locations_list) && $travel_locations_list !== false) {
										$travel_locations_list = explode($settings['trip_type_separator'], $travel_locations_list);
								?>
										<?php echo wp_kses_post($travel_locations_list[0]);
										// check if the location is more than one for showing dropdown icon
										if ($travel_locations_list !== '' && count($travel_locations_list) > 1) {
										?><i class="fas fa-angle-down"></i>
										<?php } ?>
										<span class="wtwe-meta-travel-location-dropdown-content">
											<?php
											foreach ($travel_locations_list as $list) {
												echo wp_kses_post($list);
											}
											?>
										</span>
								<?php
									}
								} else {
									echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
								}
								?>
							</div>
						<?php
						} elseif ($settings['content_type'] == 'activity') {
						?>
							<div class="wtwe-meta-travel-location-dropdown-wrapper">
								<?php
								if ($settings['show_meta_content_dropdown'] == 'yes') {
									$activity_list = get_the_term_list($trip_id, 'activity', '', $settings['trip_type_separator'], '');
									if (!is_wp_error($activity_list) && $activity_list !== false) {
										$activity_list = explode($settings['trip_type_separator'], $activity_list);
								?>
										<?php echo wp_kses_post($activity_list[0]);
										if ($activity_list !== '' && count($activity_list) > 1) {
										?><i class="fas fa-angle-down"></i>
										<?php } ?>
										<div class="wtwe-meta-travel-location-dropdown-content">
											<?php
											foreach ($activity_list as $list) {
												echo  wp_kses_post($list);
											}
											?>
										</div>
								<?php
									}
								} else {
									echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
								}
								?>
							</div>
						<?php
						} elseif ($settings['content_type'] == 'travel_keywords') {
						?>
							<div class="wtwe-meta-travel-location-dropdown-wrapper">
								<?php

								if ($settings['show_meta_content_dropdown'] == 'yes') {
									$travel_keywords_list = get_the_term_list($trip_id, 'travel_keywords', '', $settings['trip_type_separator'], '');
									if (!is_wp_error($travel_keywords_list) && $travel_keywords_list !== false) {
										$travel_keywords_list = explode($settings['trip_type_separator'], $travel_keywords_list);
								?>
										<?php echo wp_kses_post($travel_keywords_list[0]);
										if ($travel_keywords_list !== '' && count($travel_keywords_list) > 1) {
										?><i class="fas fa-angle-down"></i>
										<?php } ?>
										<div class="wtwe-meta-travel-location-dropdown-content">
											<?php foreach ($travel_keywords_list as $list) {
													echo wp_kses_post($list);
												}
											?>
										</div>
								<?php
									}
								} else {
									echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
								}
								?>
							</div>
						<?php
						} else {
							echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
						}
					} else if ($settings['content_type'] == 'group_size') {
						if ((int) $group_size && $group_size < 999) {
							
						?>
							<div class="wtwe-meta-trip-pax">
								<?php
								/* translators: %d is the group size text */
								 printf(apply_filters('wp_travel_template_group_size_text', __('%1$d %2$s', 'wt-widgets-elementor')), esc_html($group_size), esc_html(($pax_text))); ?>
							</div>
					<?php
						} else {
							echo esc_html(apply_filters('wp_travel_default_group_size_text', $empty_group_size_text));
						}
					} else if ($settings['content_type'] == 'review_count') {
						echo '<div class="wtwe-meta-trip-reviews">' . esc_html($review_count) . esc_html__(' Reviews', 'wt-widgets-elementor') . '</div>';
					} else if ($settings['content_type'] == 'trip_duration') {
						if ($fixed_departure) {
							echo '<div class="wtwe-meta-trip-date">' . wp_kses_post(wptravel_get_fixed_departure_date($trip_id)) . '</div>';
						} else {
							echo '<div class="wtwe-meta-trip-duration">' . wp_kses_post(wp_travel_get_trip_durations($trip_id)) . '</div>';
						}
					}
					?>
				</div>
			<?php
			} elseif ($trip_id > 0 && get_post_type($trip_id) == 'itineraries') {
				$trip_id = $trip_id;
				$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure($trip_id);
				$date_label = $fixed_departure ? 'Fixed Departure' : 'Duration';
			?>
				<div class="wtwe-meta-trip-type">
					<?php if ($settings['show_meta_trip_type_title'] == 'yes') { ?>
						<<?php echo esc_html($settings['meta_trip_type_title_html_tag']) . ' ' . wp_kses_post($this->get_render_attribute_string('meta_trip_type_title')) ?>>
							<?php if ($settings['content_type'] == 'trip_duration') {
								echo esc_html($date_label);
							} else {
								echo esc_html($settings['meta_trip_type_title']);
							}
							?>
						</<?php echo esc_html($settings['meta_trip_type_title_html_tag']) ?>>
					<?php } ?>
					<?php if ($settings['content_type'] == 'itinerary_types' || $settings['content_type'] == 'travel_locations' || $settings['content_type'] == 'activity') {
						echo wp_kses_post($trip_types_list[$settings['content_type']][$settings['trip_type_separator']]);
					} else if ($settings['content_type'] == 'group_size') {
						echo '<div class="wtwe-meta-trip-pax">' . esc_html($group_size) . '</div>';
					} else if ($settings['content_type'] == 'review_count') {
						echo '<div class="wtwe-meta-trip-reviews">' . esc_html($review_count) . esc_html__(' Reviews', 'wt-widgets-elementor') . '</div>';
					} else if ($settings['content_type'] == 'trip_duration') {
						if ($fixed_departure) {
							echo '<div class="wtwe-meta-trip-date">' . wp_kses_post(wptravel_get_fixed_departure_date($trip_id)) . '</div>';
						} else {
							echo '<div class="wtwe-meta-trip-duration">' . wp_kses_post(wp_travel_get_trip_durations($trip_id)) . '</div>';
						}
					}
					?>
				</div>
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
			<# var getMetaTripHtmlTag = settings.meta_trip_type_title_html_tag; #>
			<div class="wtwe-meta-trip-type">
				<div class="wtwe-meta-trip-type-title">
					<# if ( settings.show_meta_trip_type_title == 'yes' ) { #>
						<# if ( settings.content_type == 'trip_duration' ) { #>
							<h5><?php echo esc_html__( 'Trip Duration', 'wt-widgets-elementor' ); ?></h5>
						<# } else { #>
							<{{ getMetaTripHtmlTag }}>{{{ settings.meta_trip_type_title }}}</{{ getMetaTripHtmlTag }}>
						<# } #>
					<# } #>
		
					<# if ( settings.content_type == 'group_size' ) { #>
						<span>9 pax</span>
					<# } else if ( settings.content_type == 'itinerary_types' ) { #>
						<# if ( settings.show_meta_content_dropdown == 'yes' ) { #>
							<div class="wtwe-meta-trip-type">
								<div class="wtwe-meta-travel-location-dropdown-wrapper">
									<a href="#"><?php echo esc_html__( 'Beach', 'wt-widgets-elementor' ); ?></a>
									<i class="fas fa-angle-down"></i>
									<div class="wtwe-meta-travel-location-dropdown-content">
										<a href="#"><?php echo esc_html__( 'Beach', 'wt-widgets-elementor' ); ?></a>
										<a href=""><?php echo esc_html__( 'Historical Monuments', 'wt-widgets-elementor' ); ?></a>
										<a href=""><?php echo esc_html__( 'Luxury Life', 'wt-widgets-elementor' ); ?></a>
										<a href="#"><?php echo esc_html__( 'Refreshing', 'wt-widgets-elementor' ); ?></a>
									</div>
								</div>
							</div>
						<# } else { #>
							<span><?php echo esc_html__( 'Beach, Historical, Luxury Life', 'wt-widgets-elementor' ); ?></span>
						<# } #>
					<# } else if ( settings.content_type == 'travel_locations' ) { #>
						<# if ( settings.show_meta_content_dropdown == 'yes' ) { #>
							<div class="wtwe-meta-travel-location-dropdown-wrapper">
								<a href="#"><?php echo esc_html__('Paris', 'wt-widgets-elementor'); ?></a>
								<i class="fas fa-angle-down"></i>
								<span class="wtwe-meta-travel-location-dropdown-content">
									<a href="#"><?php echo esc_html__('Nepal', 'wt-widgets-elementor'); ?></a>
									<a href="#"><?php echo esc_html__('India', 'wt-widgets-elementor'); ?></a>
									<a href="#"><?php echo esc_html__('Tokyo', 'wt-widgets-elementor'); ?></a>
									<a href="#"><?php echo esc_html__('Barcelona', 'wt-widgets-elementor'); ?></a>
								</span>
							</div>
						<# } else { #>
							<span><?php echo esc_html__('Paris,Nepal,India', 'wt-widgets-elementor'); ?></span>
						<# } #>
					<# } else if ( settings.content_type == 'activity' ) { #>
						<# if ( settings.show_meta_content_dropdown == 'yes' ) { #>
							<div class="wtwe-meta-trip-type">
								<div class="wtwe-meta-travel-location-dropdown-wrapper">
									<a href="#"><?php echo esc_html__('Casino', 'wt-widgets-elementor'); ?></a>
									<i class="fas fa-angle-down"></i>
									<div class="wtwe-meta-travel-location-dropdown-content">
										<a href="#"><?php echo esc_html__('Casino', 'wt-widgets-elementor'); ?></a>
										<a href="#"><?php echo esc_html__('Fireworks', 'wt-widgets-elementor'); ?></a>
										<a href="#"><?php echo esc_html__('Kayaking', 'wt-widgets-elementor'); ?></a>
										<a href="#"><?php echo esc_html__('Paragliding', 'wt-widgets-elementor'); ?></a>
									</div>
								</div>
							</div>
						<# } else { #>
							<span><?php echo esc_html__('Activity one, Activity two', 'wt-widgets-elementor'); ?></span>
						<# } #>
					<# } else if ( settings.content_type == 'review_count' ) { #>
						<span><?php echo esc_html__('5 reviews', 'wt-widgets-elementor'); ?></span>
					<# } else if ( settings.content_type == 'trip_duration' ) { #>
						<span class="wtwe-meta-trip-duration"><?php echo esc_html__('3 Day(s) 2 Night(s)', 'wt-widgets-elementor'); ?></span>
					<# } else if ( settings.content_type == 'travel_keywords' ) { #>
						<# if ( settings.show_meta_content_dropdown == 'yes' ) { #>
							<div class="wtwe-meta-trip-type">
								<div class="wtwe-meta-travel-location-dropdown-wrapper">
									<a href="#"><?php echo esc_html__( 'Keyword one', 'wt-widgets-elementor' ); ?></a>
									<i class="fas fa-angle-down"></i>
									<div class="wtwe-meta-travel-location-dropdown-content">
										<a href="#"><?php echo esc_html__( 'Keyword one', 'wt-widgets-elementor' ); ?></a>
										<a href="#"><?php echo esc_html__( 'Keyword two<', 'wt-widgets-elementor' ); ?>/a>
										<a href="#"><?php echo esc_html__( 'Keyword three', 'wt-widgets-elementor' ); ?></a>
										<a href="#"><?php echo esc_html__( 'Keyword four', 'wt-widgets-elementor' ); ?></a>
									</div>
								</div>
							</div>
						<# } else { #>
							<span><?php echo esc_html__('Keyword one, Keyword two', 'wt-widgets-elementor'); ?></span>
						<# } #>
					<# } #>
				</div>
			</div>
			<?php
		}
		
	}
}
