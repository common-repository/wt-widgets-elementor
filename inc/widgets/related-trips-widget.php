<?php

/**
 * Related Trips class.
 *
 * @category   Class
 * @package    WPTravelElementorWidgetsExtended
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_Page_Related_Trips;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WTWE\Helper\WTWE_Helper;

/**
 * Exit if access directly.
 */
defined('ABSPATH') || exit;


if (!class_exists('WTWE_Related_Trips')) {
	/**
	 * Class Declaration which extends Widget_Base.
	 */
	class WTWE_Related_Trips extends Widget_Base
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
		}

		/**
		 * Widget Name.
		 */
		public function get_name()
		{
			return 'wp-travel-related-trips';
		}
		/**
		 * Widget Title.
		 */
		public function get_title()
		{
			return 'Related Trips';
		}
		/**
		 * Widget Icon.
		 */
		public function get_icon()
		{
			return 'eicon-link';
		}
		/**
		 * Widget Categories.
		 */
		public function get_categories()
		{
			return ['wp-travel-single'];
		}
		
		protected function content_template()
		{
			?>
			<div class="wtwe-trip-related">
				<h5 class="wtwe-trip-related"><?php echo esc_html__( 'Related Trips', 'wt-widgets-elementor' ); ?></h5>
				<?php \WTWE\Helper\WTWE_Helper::wtwe_get_widget_notice( esc_html__( 'Only visible on Frontend.', 'wt-widgets-elementor' ), 'info'); ?>
			</div>

			<?php
		}
		/**
		 * PHP Render For Widget.
		 */
		protected function render()
		{
			if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				return;
			}
			$settings = $this->get_settings_for_display();
			$trip_id           = get_the_ID();
			if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {
				$related_trip_data = WTWE_Helper::wtwe_get_wp_travel_elementor_related_trips();
				$related_trip_html = !empty($related_trip_data[$trip_id]) ? ($related_trip_data[$trip_id]) : '';
				echo wp_kses_post($related_trip_html); // phpcs:ignore WordPress.Security.EscapeOutput	
			} else {
				WTWE_Helper::wtwe_get_widget_notice( esc_html__( 'Only works on Trip page.', 'wt-widgets-elementor' ), 'info');
			}
		}
	}
}
