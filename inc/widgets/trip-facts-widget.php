<?php

/**
 * Trip Facts class.
 *
 * @category   Class
 * @package    WPTravelElementorWidgetsExtended
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_page_Trip_Facts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use \WTWE\Helper\WTWE_Helper;

/**
 * Exit if access directly.
 */
defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Facts')) {
	/**
	 * Class Declaration which extends Widget_Base.
	 */
	class WTWE_Trip_Facts extends Widget_Base
	{
		/**
		 * Widget Name.
		 */
		public function get_name()
		{
			return 'wp-travel-trip-facts';
		}
		/**
		 * Widget Title.
		 */
		public function get_title()
		{
			return 'Trip Facts';
		}
		/**
		 * Widget Icon.
		 */
		public function get_icon()
		{
			return 'eicon-tags';
		}
		/**
		 * Widget Categories.
		 */
		public function get_categories()
		{
			return ['wp-travel-single'];
		}
		/**
		 * Settings for widget.
		 */
		// public function register_controls()
		// { //phpcs:ignore
			// $this->start_controls_section(
			// 	'section_content_1',
			// 	array(
			// 		'label' => esc_html__('WP Travel Settings', 'wt-widgets-elementor'),
			// 	)
			// );

			// $this->add_control(
			// 	'main_title',
			// 	array(
			// 		'label'   => esc_html__('Title', 'wt-widgets-elementor'),
			// 		'type'    => \Elementor\Controls_Manager::TEXT,
			// 		'default' => 'Trip Facts',
			// 	)
			// );

			// $this->add_control(
			// 	'main_title_alignment',
			// 	array(
			// 		'label'     => esc_html__('Alignment', 'wt-widgets-elementor'),
			// 		'type'      => \Elementor\Controls_Manager::CHOOSE,
			// 		'options'   => array(
			// 			'left'   => array(
			// 				'title' => esc_html__('Left', 'wt-widgets-elementor'),
			// 				'icon'  => 'eicon-text-align-left',
			// 			),
			// 			'center' => array(
			// 				'title' => esc_html__('Center', 'wt-widgets-elementor'),
			// 				'icon'  => 'eicon-text-align-center',
			// 			),
			// 			'right'  => array(
			// 				'title' => esc_html__('Right', 'wt-widgets-elementor'),
			// 				'icon'  => 'eicon-text-align-right',
			// 			),
			// 		),
			// 		'default'   => 'center',
			// 		'selectors' => array(
			// 			'{{WRAPPER}} .trip_facts_title' => 'text-align: {{VALUE}};',
			// 		),
			// 	)
			// );

			// $this->add_control(
			// 	'main_title_color',
			// 	array(
			// 		'label'     => esc_html__('Color', 'wt-widgets-elementor'),
			// 		'type'      => \Elementor\Controls_Manager::COLOR,
			// 		'default'   => '#000',
			// 		'selectors' => array(
			// 			'{{WRAPPER}} .trip_facts_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .trip_facts_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .trip_facts_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .trip_facts_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .trip_facts_title' => 'color: {{VALUE}}',
			// 		),
			// 	)
			// );

			// $this->add_control(
			// 	'important_note',
			// 	array(
			// 		'type' => \Elementor\Controls_Manager::RAW_HTML,
			// 		'raw'  => esc_html__('NOTE: This works only on Trips', 'wt-widgets-elementor'),
			// 	)
			// );

			// $this->end_controls_section();

			// $this->start_controls_section(
			// 	'trip_facts_title_styles',
			// 	array(
			// 		'label' => esc_html__('Title', 'wt-widgets-elementor'),
			// 		'tab'	=> Controls_Manager::TAB_STYLE,
			// 	)
			// );

			// $this->add_control(
			// 	'trip_facts_title_hr',
			// 	[
			// 		'type' => Controls_Manager::DIVIDER,
			// 	]
			// );

			// $this->add_group_control(
			// 	Group_Control_Typography::get_type(),
			// 	array(
			// 		'name' => 'trip_facts_title_typography',
			// 		'selector'	=> '{{ WRAPPER }} .trip_facts_title',
			// 	)
			// );

			// $this->add_group_control(
			// 	Group_Control_Text_Stroke::get_type(),
			// 	array(
			// 		'name' => 'trip_facts_title_stroke',
			// 		'selector' => '{{ WRAPPER }} .trip_facts_title',
			// 	)
			// );

			// $this->add_group_control(
			// 	Group_Control_Text_Shadow::get_type(),
			// 	array(
			// 		'name' => 'trip_facts_title_shadow',
			// 		'selector' => '{{ WRAPPER }} .trip_facts_title',
			// 	)
			// );

			// $this->end_controls_section();
		// }
		/**
		 * PHP Render For Widget.
		 */
		protected function render()
		{
			if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				return;
			}
			$settings = $this->get_settings_for_display();
			if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {

				$trip_id        = get_the_ID();
				$trip_fact_data = WTWE_Helper::wtwe_get_wp_travel_elementor_trips_facts_content();
				$html           = !empty($trip_fact_data[$trip_id]) ? ($trip_fact_data[$trip_id]) : '';
				echo wp_kses_post($html); // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				WTWE_Helper::wtwe_get_widget_notice( __( 'Only works on Trip page.', 'wt-widgets-elementor' ), 'info');
			}
		}
		/**
		 * JS Render for widget.
		 */
		protected function content_template()
		{
?>
			<div class="wtwe-trip-facts">
				<h5 class="wtwe-trip-facts"><?php echo esc_html__('Trip Facts', 'wt-widgets-elementor'); ?></h5>
			</div>
			<div class="tour-info">
				<div class="tour-info-box clearfix">
					<div class="tour-info-column ">
						<span class="tour-info-item tour-info-type">
							<i class="fas fa-american-sign-language-interpreting" aria-hidden="true"></i>
							<strong><?php echo esc_html__( 'Fact 1', 'wt-widgets-elementor' );?></strong>:
							<?php echo esc_html__( 'Value', 'wt-widgets-elementor' );?> </span>
						<span class="tour-info-item tour-info-type">
							<i class="fas fa-bowling-ball" aria-hidden="true"></i>
							<strong><?php echo esc_html__( 'Fact 2', 'wt-widgets-elementor' );?></strong>:
							<?php echo esc_html__( 'Value', 'wt-widgets-elementor' );?> </span>
						<span class="tour-info-item tour-info-type">
							<i class="fas fa-asterisk" aria-hidden="true"></i>
							<strong><?php echo esc_html__( 'Fact n', 'wt-widgets-elementor' );?></strong>:
							<?php echo esc_html__( 'Value...', 'wt-widgets-elementor' );?> </span>
					</div>
				</div>
			</div>

<?php
		}
	}
}
