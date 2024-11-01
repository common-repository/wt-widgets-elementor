<?php

/**
 * Breadcrumb widget class.
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
defined('ABSPATH') || exit;

/**
 * Trip Search widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Breadcrumb')) {
	class WTWE_Breadcrumb extends Widget_Base
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

			add_action('elementor/frontend/after_enqueue_scripts', [$this, 'register_breadcrumb_scripts']);
		}

		public function register_breadcrumb_scripts()
		{
			$prefixed = defined(WP_DEBUG) ? '.min' : '';
			wp_register_style('trips-breadcrumb-style', plugins_url('assets/css/trips-breadcrumb' . $prefixed . '.css', WTWE_PLUGIN_FILE), array(), WTWE_VERSION);
			wp_register_script('trips-breadcrumb-script', plugin_dir_url(WTWE_PLUGIN_FILE) . 'assets/js/trips-breadcrumb' . $prefixed . '.js', array('jquery'), WTWE_VERSION, true);
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
			return 'wp-travel-breadcrumb';
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
			return esc_html__('BreadCrumb', 'wt-widgets-elementor');
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
			return 'eicon-date';
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
		 * Enqueue the scripts that the widget depends on.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 *
		 * @return array dependency scripts.
		 */
		// public function get_script_depends() {
		// 	return array( 'jquery', 'trips-breadcrumb-script' );
		// }

		public function get_style_depends()
		{
			return array('trips-breadcrumb-style');
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
		public function get_script_depends()
		{
			return array('jquery', 'trips-breadcrumb-script');
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
		public static function wtwe_handle_content_type($content_type)
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
					$slug = !empty($term->slug) ? $term->slug : '';
					$content[''] = "All";
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
			$this->start_controls_section(
				'general_content',
				array(
					'label' => esc_html__('General', 'wt-widgets-elementor'),
				)
			);

			// Typography settings
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'typography',
					'label' => esc_html__('Typography', 'wt-widgets-elementor'),
					'selector' => '{{WRAPPER}} .wtwe-breadcrumb-wrapper li[itemprop="itemListElement"]',
				)
			);

			// Separator settings
			$this->add_control(
				'breadcrumb_separator',
				array(
					'label' => esc_html__('Breadcrumb Separator', 'wt-widgets-elementor'),
					'type' => Controls_Manager::TEXT, // Change type to TEXT
					'placeholder' => esc_html__('Enter separator text', 'wt-widgets-elementor'), // Optional placeholder text
					'default' => '>', // Default separator text
				)
			);
			

			$this->end_controls_section();

			// Style tab
			$this->start_controls_section(
				'style_settings',
				array(
					'label' => esc_html__('Style', 'wt-widgets-elementor'),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			// Add your style controls here
			$this->add_control(
				'breadcrumb_color',
				array(
					'label' => esc_html__('Breadcrumb Color', 'wt-widgets-elementor'),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wtwe-breadcrumb-wrapper li[itemprop="itemListElement"] a' => 'color: {{VALUE}};',
						'{{WRAPPER}} .wtwe-breadcrumb-wrapper li[itemprop="itemListElement"] ' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'separator_color',
				array(
					'label' => esc_html__('Separator Color', 'wt-widgets-elementor'),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wtwe-breadcrumb-wrapper li.item-separator' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'breadcrumb_spacing',
				array(
					'label' => esc_html__('Spacing', 'wt-widgets-elementor'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array('px', 'em', '%'),
					'selectors' => array(
						'{{WRAPPER}} .wtwe-breadcrumb-wrapper' => '
						 margin-top: {{TOP}}{{UNIT}}; 
						 margin-right: {{RIGHT}}{{UNIT}};
						 margin-bottom: {{BOTTOM}}{{UNIT}}; 
						 margin-left: {{LEFT}}{{UNIT}};',
					),
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
		protected function render()
		{
			if(\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				return;
			}
			$settings = $this->get_settings_for_display();

			// Get the breadcrumb separator from settings
			$separator = !empty($settings['breadcrumb_separator']) ? $settings['breadcrumb_separator'] : '>';

?>
			<div class="wtwe-breadcrumb-wrapper" data-separator="<?php echo esc_attr($separator); ?>">
				<?php
				// Output the breadcrumbs
				wt_widgets_elementor_breadcrumb_trail();
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
			const breadcrumb_separator = settings.breadcrumb_separator; 
			if(breadcrumb_separator != ''){
				const separator = breadcrumb_separator;
			}
			#>

			<div class="wtwe-breadcrumb-wrapper" data-separator=">">
				<nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb">
					<ul class="trail-items" itemscope="" itemtype="http://schema.org/BreadcrumbList">
						<meta name="numberOfItems" content="2">
						<meta name="itemListOrder" content="Ascending">
						<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="trail-item trail-begin">
							<a href="#" rel="home" itemprop="item">
								<span itemprop="name">
									<?php echo esc_html__( 'First item', 'wt-widgets-elementor' );  ?>
								</span>
						</a>
							<meta itemprop="position" content="1">
							<span class="separator">{{{breadcrumb_separator}}}</span>
						</li>
						<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="trail-item trail-end">
							<span itemprop="item"><span itemprop="name">
								<?php echo esc_html__( 'Second Item', 'wt-widgets-elementor' );  ?> 
							</span>
							<meta itemprop="position" content="2">
						</li>
					</ul>
				</nav>
			</div>
		<?php }
	}
}
