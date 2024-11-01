<?php

/**
 * Add To Cart Button class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_Page_Add_To_Cart;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WTWE\Helper\WTWE_Helper;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Trip Tabs widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Add_To_Cart')) {
    class WTWE_Add_To_Cart extends Widget_Base
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
            wp_register_style('add-to-cart', plugins_url('assets/css/add-to-cart' . $prefixed . '.css', WTWE_PLUGIN_FILE), []);
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
            return 'wp-travel-add-to-cart';
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
            return esc_html__('Add To Cart', 'wt-widgets-elementor');
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
            return 'eicon-cart';
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
            return array('add-to-cart');
        }

        /**
         * Register the widget controls.
         *
         * Adds different input fields to allow the user to change and bookize the widget settings.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function _register_controls()
        {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Content', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'hide_counter',
                [
                    'label' => __('Hide Cart Counter', 'wt-widgets-elementor'),
                    'type'  => Controls_Manager::SWITCHER,
                    'label_on' => __('Hide', 'wt-widgets-elementor'),
                    'label_off' => __('Show', 'wt-widgets-elementor'),
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
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
             // is editor mode?
             if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            $settings = $this->get_settings_for_display();
            $isHideEnabled = $settings['hide_counter'];

            if (!empty(get_the_ID()) && get_the_ID() > 0 ) {
                if (is_plugin_active('wp-travel-pro/wp-travel-pro.php') && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    if (function_exists('wptravel_get_cart_icon')) {
?>
                        <div class="wptravel-cart-widget <?php echo $isHideEnabled ? "wptravel-hide-counter" : "wptravel-show-counter" ?>">
                            <?php
                        
                            if ( function_exists('wptravel_get_cart_icon')){
                                $wptravel_get_cart = wptravel_get_cart_icon();
                            }
                            echo esc_html($wptravel_get_cart);
                            ?>
                        </div>
                <?php

                    }
                } else {
                    WTWE_Helper::wtwe_get_widget_notice( esc_html__( 'Add-to-cart option is not enabled.', 'wt-widgets-elementor' ), 'info');
                }
            }
        }

        /**
         * Render the widget output on the editor.
         *
         * Written in JS and used to generate the final HTML.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function _content_template()
        {
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>

                <a class="wp-travel-add-to-cart-item-anchor" href="#" target="_blank" rel="noopener noreferrer">
                    <button class="wp-travel-single-trip-cart-button">
                        <span id="wp-travel-add-to-cart-cart_item_show">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span class="wp-travel-cart-items-number"><?php echo esc_html__('0', 'wt-widgets-elementor'); ?></span>
                        </span>
                    </button>
                </a>
<?php
            } else {
                echo esc_html__('Add-to-cart option is not enabled','wt-widgets-elementor');
            }
        }
    }
}
