<?php

/**
 * This file use to fetch trip code using global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Code;

/**
 * @uses elementor widget namespace
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

//   Security Note : Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;
/**
 * Create class for register widget
 * 
 */
if (!class_exists('WTWE_Trip_Code')) {
    class WTWE_Trip_code extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }

        // create widge name
        public function get_name()
        {
            return 'wp-travel-trip-code';
        }
        // create title of trip-code widget name
        public function get_title()
        {
            return esc_html__('Trip Code', 'wt-widgets-elementor');
        }
        // set icon
        public function get_icon()
        {
            return 'eicon-navigation-horizontal';
        }
        // set widget under the wp-travel category widgets
        public function get_categories()
        {
            return ['wp-travel-single'];
        }
        // Register and setup control setting for trip code
        public function _register_controls()
        {

            $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Style', 'wt-widgets-elementor'),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'content_typography',
                        'selector' => '{{WRAPPER}} .wtwe-trip-code',
                    ]
                );
                $this->add_control(
                    'trip-code_color',
                    [
                        'label' => esc_html__('Color', 'wt-widgets-elementor'),
                        'type'  => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-trip-code' => 'color:{{VALUE}}',
                        ],
                    ]
                );
                // Add remaining style controls here
                $this->end_controls_section();
            } else {
                $this->add_control(
                    'trip-code_color',
                    [
                        'label' => esc_html__('Color', 'wt-widgets-elementor'),
                        'type'  => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-trip-code' => 'color:{{VALUE}}',
                        ],
                    ]
                );
                // Add remaining style controls here
                $this->end_controls_section();
                
            }
           
        }
        protected function content_template()
        {
            global $post;
            $trip_id = get_the_ID();
?>
            <div class="wtwe-trip-code-wrapper">
                <span class="wtwe-trip-code"><?php echo esc_html__( 'WT-CODE 42', 'wt-widgets-elementor' ) ?></span>
            </div>
            <?php
        }
        // Show content on frontend trip single page
        protected function render()
        {
                global $post;
                $trip_id = get_the_ID();
                $wptravel_get_tripcode = wptravel_get_trip_code($trip_id);
                if ($post && 'itineraries' === $post->post_type) {
            ?>
                    <div class="wtwe-trip-code-wrapper">
                        <div class="wtwe-trip-code">
                            <code>
                                <?php echo esc_html( $wptravel_get_tripcode ); ?>
                            </code>
                        </div>
                    </div>
<?php
                }
            
        }
    }
}
