<?php

/**
 * Trip Maps Widget File.
 *
 * @package wp-travel-blocks.
 */

namespace WTWE\Widgets\Single_Page_Trip_Maps;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;

/**
 * Exit if access directly.
 */
defined('ABSPATH') || exit;


if (!class_exists('WTWE_Trip_Maps')) {
	/**
	 * Class Declaration which extends Widget_Base.
	 */
	class WTWE_Trip_Maps extends Widget_Base
	{
		/**
		 * Widget Name.
		 */
		public function get_name()
		{
			return 'wp-travel-trip-maps';
		}
		/**
		 * Widget Title.
		 */
		public function get_title()
		{
			return 'Trip Maps';
		}
		/**
		 * Widget Icon.
		 */
		public function get_icon()
		{
			return 'eicon-google-maps';
		}
		/**
		 * Widget Categories.
		 */
		public function get_categories()
		{
			return array('wp-travel-single');
		}
		/**
		 * Settings for widget.
		 */
		// protected function _register_controls()
		// { //phpcs:ignore

			// $this->start_controls_section(
			// 	'section_content_1',
			// 	array(
			// 		'label' => esc_html__( 'WP Travel Settings', 'wt-widgets-elementor' ),
			// 	)
			// );

			// $this->add_control(
			// 	'main_title',
			// 	array(
			// 		'label'   => esc_html__( 'Title', 'wt-widgets-elementor' ),
			// 		'type'    => \Elementor\Controls_Manager::TEXT,
			// 		'default' => 'Map',
			// 	)
			// );

			// $this->add_control(
			// 	'main_title_alignment',
			// 	array(
			// 		'label'     => esc_html__( 'Alignment', 'wt-widgets-elementor' ),
			// 		'type'      => \Elementor\Controls_Manager::CHOOSE,
			// 		'options'   => array(
			// 			'left'   => array(
			// 				'title' => esc_html__( 'Left', 'wt-widgets-elementor' ),
			// 				'icon'  => 'eicon-text-align-left',
			// 			),
			// 			'center' => array(
			// 				'title' => esc_html__( 'Center', 'wt-widgets-elementor' ),
			// 				'icon'  => 'eicon-text-align-center',
			// 			),
			// 			'right'  => array(
			// 				'title' => esc_html__( 'Right', 'wt-widgets-elementor' ),
			// 				'icon'  => 'eicon-text-align-right',
			// 			),
			// 		),
			// 		'default'   => 'center',
			// 		'selectors' => array(
			// 			'{{WRAPPER}} .google_map_title' => 'text-align: {{VALUE}};',
			// 		),
			// 	)
			// );

			// $this->add_control(
			// 	'main_title_color',
			// 	array(
			// 		'label'     => esc_html__( 'Color', 'wt-widgets-elementor' ),
			// 		'type'      => \Elementor\Controls_Manager::COLOR,
			// 		'default'   => '#000',
			// 		'selectors' => array(
			// 			'{{WRAPPER}} .google_map_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .google_map_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .google_map_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .google_map_title' => 'color: {{VALUE}}',
			// 			'{{WRAPPER}} .google_map_title' => 'color: {{VALUE}}',
			// 		),
			// 	)
			// );

			// $this->add_control(
			// 	'important_note',
			// 	array(
			// 		'type' => \Elementor\Controls_Manager::RAW_HTML,
			// 		'raw'  => esc_html__( 'NOTE: This works only on Trips', 'wt-widgets-elementor' ),
			// 	)
			// );

			// $this->end_controls_section();

			// $this->start_controls_section(
			// 	'google_map_title_styles',
			// 	array(
			// 		'label' => esc_html__( 'Title', 'wt-widgets-elementor' ),
			// 		'tab'	=> Controls_Manager::TAB_STYLE,
			// 	)
			// );

			// $this->add_control(
			// 	'google_map_title_hr',
			// 	[
			// 		'type' => Controls_Manager::DIVIDER,
			// 	]
			// );

			// $this->add_group_control(
			// 	Group_Control_Typography::get_type(),
			// 	array(
			// 		'name' => 'google_map_title_typography',
			// 		'selector'	=> '{{ WRAPPER }} .google_map_title',
			// 	)
			// );

			// $this->add_group_control(
			// 	Group_Control_Text_Stroke::get_type(),
			// 	array(
			// 		'name' => 'google_map_title_stroke',
			// 		'selector' => '{{ WRAPPER }} .google_map_title',
			// 	)
			// );

			// $this->add_group_control(
			// 	Group_Control_Text_Shadow::get_type(),
			// 	array(
			// 		'name' => 'google_map_title_shadow',
			// 		'selector' => '{{ WRAPPER }} .google_map_title',
			// 	)
			// );

			// $this->end_controls_section();
		// }
		/**
		 * Return trip_id to get map.
		 */
		public function override_map_trip_id()
		{
			$settings = $this->get_settings_for_display();
			return !empty($settings['trip_id']) ? $settings['trip_id'] : 0;
		}
		/**
		 * PHP Render For Widget.
		 */
		protected function render()
		{
			if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				return;
			}
			wp_enqueue_script('wp-travel-maps');
			add_filter('wp_travel_map_custom_trip_id', array($this, 'wp_travel_map_custom_trip_id'), -1);
			$settings = $this->get_settings_for_display();
			$this->add_inline_editing_attributes('main_title', 'advanced');
			$this->add_render_attribute(
				'main_title',
				array(
					'class' => array('google_map_title'),
				)
			);

			if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
				$trip_id = get_the_ID();
				if (function_exists('wptravel_trip_map')) {
					$google_map_data = wptravel_trip_map($trip_id);
				} else {
					$google_map_data = wp_travel_trip_map($trip_id);
				}
				echo esc_html($google_map_data); // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				\WTWE\Helper\WTWE_Helper::wtwe_get_widget_notice( __( 'Only works on Trip page.', 'wt-widgets-elementor' ), 'info');
			}
		}
		/**
		 * JS Render for widget.
		 */
		protected function content_template()
		{

			?>
			<div class="wtwe-trip-maps">
				<img src="<?php echo esc_url( plugins_url( 'assets/images/dummy-map.png', WTWE_PLUGIN_FILE ) ) ?>" alt="Elementor Placeholder Image" class="wtwe-trip-featured-image" style="width:100%">
			</div>

			<?php
		}
	}
}
