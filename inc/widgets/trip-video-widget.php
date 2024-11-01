<?php

/**
 * Trip Video widget class.
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

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Trip Search widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Trip_Video')) {
    class WTWE_Trip_Video extends Widget_Base
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
            $prefixed = defined('WP_DEBUG') && WP_DEBUG ? '' : '.min';
            wp_register_style('trips-video', plugins_url('assets/css/trips-video' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
            wp_register_script('trips-video-script', plugins_url('assets/js/trips-video' . $prefixed . '.js', WTWE_PLUGIN_FILE), ['jquery']);
            wp_register_style('magnific-popup', plugins_url('assets/css/magnific-popup' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
            wp_register_script('magnific-popup-script', plugins_url('assets/js/magnific-popup' . $prefixed . '.js', WTWE_PLUGIN_FILE), array('jquery'), '1.0.0', true);
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
            return 'wp-travel-trip-video';
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
            return esc_html__('Trip Video', 'wt-widgets-elementor');
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
            return 'eicon-video-camera';
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

        public function get_style_depends()
        {
            return array('trips-video', 'magnific-popup');
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
            return array('jquery', 'trips-video-script', 'magnific-popup-script');
        }

        protected function register_controls()
        {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Trip Video', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
            );


            $this->add_control(
                'button_icon',
                [
                    'label' => __('Button Icon', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-play',
                        'library' => 'fa-solid',
                    ],
                ]
            );

            $this->add_control(
                'icon_color',
                [
                    'label' => __('Icon Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                ]
            );

            $this->add_control(
                'font_size',
                [
                    'label' => __('Font Size', 'wt-wdigets-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 2,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                ]
            );


            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            $wptravel_trip_video = get_post_meta(get_the_ID(), 'wp_travel_video_code', true);
            $icon_color = esc_attr($settings['icon_color']);
            $font_size = esc_attr($settings['font_size']['size']);
            $button_icon = !empty($settings['button_icon']['value']) ? esc_attr($settings['button_icon']['value']) : 'eicon-video-camera';
            
            // Determine the video URL to use
            $video_url =  esc_url($wptravel_trip_video);
            ?>
            <div class="wtwe-trip-video" style="font-size: <?php echo $font_size; ?>px;">
                <a class="video-button" href="<?php echo $video_url; ?>" data-magnific-popup="iframe" style="color: <?php echo $icon_color; ?>;">
                    <i class="<?php echo $button_icon; ?>"></i>
                </a>
            </div>
        
            <script>
                jQuery(document).ready(function($) {
                    $('.wtwe-trip-video').magnificPopup({
                        delegate: 'a',
                        type: 'iframe',
                        iframe: {
                            patterns: {
                                youtube: {
                                    index: 'youtube.com/',
                                    id: 'v=',
                                    src: 'https://www.youtube.com/embed/%id%?autoplay=1'
                                }
                            }
                        }
                    });
                });
            </script>
        
            <?php
        }
        
        protected function _content_template() {
            ?>
            <#
            var videoCode = settings.video_code || '';
            var iconColor = settings.icon_color;
            var fontSize = settings.font_size.size;
            var buttonIcon = settings.button_icon.value|| 'eicon-video-camera';
            #>
            <div class="wtwe-trip-video" style="font-size: {{ fontSize }}px;">
                    <a class="video-button" href="https://www.youtube.com/watch?v={{ videoCode }}" data-magnific-popup="iframe" style="color: {{ iconColor }};">
                        <i class="{{ buttonIcon }}"></i>
                    </a>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $('.wtwe-trip-video').magnificPopup({
                        delegate: 'a',
                        type: 'iframe',
                        iframe: {
                            patterns: {
                                youtube: {
                                    index: 'youtube.com/',
                                    id: 'v=',
                                    src: 'https://www.youtube.com/embed/%id%?autoplay=1'
                                }
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        
    }
}
