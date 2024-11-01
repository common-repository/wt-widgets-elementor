<?php

/**
 * This file use to fetch trip price using global post
 * 
 */

namespace WTWE\Widgets\Single_Page_Trip_Price;

/**
 * @uses elementor widget namespce
 */

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

use WP_Travel_Itinerary;


// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * create class for register widget
 * 
 */
if (!class_exists('WTWE_Trip_Price')) {
    class WTWE_Trip_Price extends Widget_Base
    {
        public function __construct($data = [], $args = [])
        {
            parent::__construct($data, $args);
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-price';
        }
        // Create title of trip-price widget name
        public function get_title()
        {
            return esc_html__('Trip Price', 'wt-widgets-elementor');
        }
        // set icon 
        public function get_icon()
        {
            return 'eicon-product-price';
        }
        // set widget under the wp-travel category widgets
        public function get_categories()
        {
            return ['wp-travel-single'];
        }

        // Register and setup control setting for trip price
        public function _register_controls()
        {

            // Style Tab
            $this->start_controls_section(
                'section_style',
                [
                    'label' => esc_html__('Style', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
            // Add remaining style controls here


            $this->add_control(
                'trip_title_style',
                [
                    'label' => esc_html__('Trip Price', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            
            $this->add_control(
                'trip_price_color',
                [
                    'label' => esc_html__('Text Color', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-trip-price-wrapper .wtwe-trip-price' => 'color: {{VALUE}};',
                    ],
                ]
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'name' => 'trip_tytle_heading_typography',
                        'selector'    => '{{ WRAPPER }} .wtwe-trip-price-wrapper .wtwe-trip-price',
                    )
                );
    
                $this->add_control(
                    'trip_price_alignment',
                    [
                        'label' => esc_html__('Alignment', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'price' => esc_html__('Left', 'wt-widgets-elementor'),
                                'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                'price' => esc_html__('Center', 'wt-widgets-elementor'),
                                'icon' => 'eicon-text-align-center',
                            ],
                            'right' => [
                                'price' => esc_html__('Right', 'wt-widgets-elementor'),
                                'icon' => 'eicon-text-align-right',
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-trip-price-wrapper .wtwe-trip-price' => 'text-align: {{VALUE}};',
                        ],
                    ]
                );
    
    
                $this->add_group_control(
                    Group_Control_Box_Shadow::get_type(),
                    [
                        'name' => 'box_shadow',
                        'selector' => '{{WRAPPER}} .wtwe-trip-price-wrapper .wtwe-trip-price',
                    ]
                );
            } else {
                 // WP Travel Pro is not active, display information to activate it
                 $this->add_control(
                    'inactive_message',
                    [
                        'label' => esc_html__('WP Travel Pro is not active', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' =>esc_html__('Please activate the WP Travel Pro plugin to access the style options.', 'wt-widgets-elementor'),
                        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                    ]
                );
                
            }

           

            // Add remaining style controls here


            $this->end_controls_section();
        }

        protected function content_template()
        {


?>
            <div class="wtwe-trip-price-wrapper">

                <h2 class="wtwe-trip-price"><span class="price-from">From</span> $000</h2>

            </div>
            <?php
        }
        //Show content on frontend trip single page
        protected function render()
        {
            
                global $post;
                $settings = $this->get_settings_for_display();
                $post_id = get_the_ID();

                $args                             = array( 'trip_id' => $post_id );
                $args_regular                     = $args;
                $args_regular['is_regular_price'] = true;
                $trip_price                       = \WP_Travel_Helpers_Pricings::get_price( $args );
                $regular_price                    = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
                $enable_sale                      = \WP_Travel_Helpers_Trips::is_sale_enabled(
                    array(
                        'trip_id'                => $post_id,
                        'from_price_sale_enable' => true,
                    )
                );

                $strings = \WpTravel_Helpers_Strings::get();
                if ($post && 'itineraries' === $post->post_type) {
                    $wptravel_trip_price = get_post_meta($post_id, 'wp_travel_trip_price', true);
                    if (!empty($wptravel_trip_price)) {
            ?>
                        <div class="wtwe-trip-price-wrapper wp-travel-trip-detail">
                            <div class="wtwe-trip-price trip-price">
                                <span class="price-from">
                                    <?php echo esc_html( $strings['from'] ); ?>
                                </span>
                                <?php if ( $enable_sale && $regular_price !== $trip_price ) : ?>
									<del><span><?php echo wp_kses_post( wptravel_get_formated_price_currency( $regular_price, true ) ); ?></span></del>
								<?php endif; ?>
								<span class="person-count">
									<ins>
										<span><?php echo wp_kses_post( wptravel_get_formated_price_currency( $trip_price ) ); ?></span>
									</ins>
								</span>
                            </div>
                        </div>
<?php

                    } else {
                        echo ' Trip Price is empty';
                    }
                }
            } 
    }
}