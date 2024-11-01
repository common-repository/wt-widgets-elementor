<?php

/**
 * This file is used to fetch the trip review form.
 */

namespace WTWE\Widgets\Single_Page_Trip_Gallery;

use Elementor\Widget_Base;
use WTWE\Helper\WTWE_Helper;

defined('ABSPATH') || exit;

if (!class_exists('WTWE_Trip_Gallery')) {
    class WTWE_Trip_Gallery extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
            $prefixed = defined(WP_DEBUG) ? '.min' : '';
            wp_register_style('trip-gallery', plugins_url('assets/css/trip-gallery' . $prefixed . '.css', WTWE_PLUGIN_FILE), []);
        }

        public function get_name()
        {
            return 'wp-travel-trip-gallery';
        }

        public function get_title()
        {
            return esc_html__('Trip Gallery', 'wt-widgets-elementor');
        }

        public function get_icon()
        {
            return 'eicon-gallery-grid';
        }

        public function get_categories()
        {
            return ['wp-travel-single'];
        }

        /**
         * Enqueue styles.
         */
        public function get_style_depends()
        {
            return array('trip-gallery');
        }

        protected function render()
        {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }

            $trip_id = get_the_ID();

            $tab_data = wptravel_get_frontend_tabs();
            $content = is_array($tab_data) && isset($tab_data['gallery']) && isset($tab_data['gallery']['content']) ? $tab_data['gallery']['content'] : '';

            if (!empty($trip_id) && get_post_type($trip_id) == 'itineraries') {
?>
                <div id="wptravel-block-trip-gallery" class="wptravel-block-wrapper wptravel-block-trip-gallery">
                    <?php echo wpautop(do_shortcode($content)); ?>
                </div>
            <?php
            } else {
                WTWE_Helper::wtwe_get_widget_notice( __( 'This widget is only visible on the frontend for itineraries.', 'wt-widgets-elementor' ), 'info');
            }
        }

        protected function content_template()
        {
            $elementor_placeholder_image = plugins_url('assets/images/elementor-placeholder-image.png', WTWE_PLUGIN_FILE);
            if (isset($elementor_placeholder_image) && !empty($elementor_placeholder_image)) {
                $placeholder_image = $elementor_placeholder_image;
            } else {
                $placeholder_image = '#';
            }
            ?>
            <div class="wtwe-trip-gallery-title">
                <h5 class="wtwe-trip-gallery-title"><?php echo esc_html__('Trip Gallery', 'wt-widgets-elementor'); ?></h5>
            </div>
            <div class="wtwe-trip-gallery-editor-wrapper">
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
                <div class="wtwe-trip-gallery">
                    <img src="<?php echo esc_url($placeholder_image); ?>" width="600" height="400">
                </div>
            </div>

<?php
        }
    }
}
