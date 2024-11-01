<?php

/**
 * Trip Tabs class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_Page_Trip_Tabs;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Trip Tabs widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Trip_Tabs')) {
    class WTWE_Trip_Tabs extends Widget_Base
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
            wp_register_style('frontend-trip-tabs', plugins_url('assets/css/trip-tabs' . $prefixed . '.css', WTWE_PLUGIN_FILE), []);
            wp_register_script('frontend-trip-tabs', plugins_url('assets/js/frontend-trip-tabs' . $prefixed . '.js', WTWE_PLUGIN_FILE), ['jquery', 'slick-min']);
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
            return 'wp-travel-trip-tabs';
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
            return esc_html__('Trip Tabs', 'wt-widgets-elementor');
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
            return 'eicon-tabs';
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
         * 
         * @since 1.0.0
         *
         * @access public
         *
         * @return array stylesheet array.
         */
        public function get_style_depends()
        {
            return array('frontend-trip-tabs');
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
        public function get_script_depends()
        {
            return ['frontend-trip-tabs', 'slick-min', 'jquery'];
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
        // protected function _register_controls()
        // {
            // $this->start_controls_section(
            //     'content_section',
            //     array(
            //         'label' => esc_html__('Content', 'wt-widgets-elementor'),
            //         'tab' => Controls_Manager::TAB_CONTENT,
            //     )
            // );

            // $this->end_controls_section();
        // }

        protected function content_template()
        {
?>
            <div class="wtwe-trip-tabs">
                <h5 class="wtwe-trip-tabs"><?php echo esc_html__( 'Trip Tabs', 'wt-widgets-elementor' ); ?></h5>
            </div>
            <?php
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode()) {

                $trip_id = get_the_ID();
                if (is_plugin_active('wp-travel/wp-travel.php')) {
                    if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {

                        if (function_exists('wptravel_frontend_contents')) {
                            echo esc_html(wptravel_frontend_contents($trip_id));
                        }
                    }
                }
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
            
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            global $post;

            $settings = $this->get_settings_for_display();
            $this->add_inline_editing_attributes('title', 'none');

            $trip_id = get_the_ID();
            // $check_plugin = is_plugin_active('wp-travel/wp-travel.php');echo $check_plugin;
            if (is_plugin_active('wp-travel/wp-travel.php')) {
                if (!empty(get_the_ID()) && get_the_ID() > 0 && get_post_type(get_the_ID()) == 'itineraries') {

                    if (function_exists('wptravel_frontend_contents')) {
                        echo esc_html(wptravel_frontend_contents($trip_id));
                    }
                }
            }
            else {
                echo esc_html__('WP Travel is not activated','wt-widgets-elementor');
            }
            ?>
<?php
        }
    }
}
