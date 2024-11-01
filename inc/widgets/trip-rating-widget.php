<?php

/**
 * Meta Trip Rating class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets\Single_Page_Meta_Trip_Rating;

use WP_Travel_Helpers_Trip_Dates;
use WTWE_Helper;

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Meta Trip Rating widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Meta_Trip_Rating')) {
    class WTWE_Meta_Trip_Rating extends Widget_Base
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
            wp_register_style('meta-trip-rating', plugins_url('assets/css/meta-trip-rating' . $prefixed . '.css', WTWE_PLUGIN_FILE), array());
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
            return 'wp-travel-meta-trip-rating';
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
            return esc_html__('Trip Rating', 'wt-widgets-elementor');
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
            return 'eicon-rating';
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
            return array('meta-trip-rating');
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
            // $this->start_controls_section(
            //     'section_content',
            //     [
            //         'label' => esc_html__('Content', 'wt-widgets-elementor'),
            //     ]
            // );

            // $this->end_controls_section();

            $this->start_controls_section(
                'rating_content',
                [
                    'label' => esc_html__('Layout', 'wt-widgets-elementor'),
                ]
            );

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'rating_layout',
                    [
                        'label' => esc_html__('Layout', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'basic',
                        'options' => [
                            'basic' => esc_html__('Basic', 'wt-widgets-elementor'),
                            'minimal' => esc_html__('Minimal', 'wt-widgets-elementor'),
                        ]
                    ]
                );
            } else {
                $this->add_control(
                    'rating_layout',
                    [
                        'label' => esc_html__('Layout', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'basic',
                        'options' => [
                            'basic' => esc_html__('Basic', 'wt-widgets-elementor'),
                        ]
                    ]
                );
                // WP Travel Pro is not active, display information to activate it
                $this->add_control(
                    'inactive_message',
                    [
                        'label' => esc_html__('WP Travel Pro is not active', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => esc_html__('Please activate the WP Travel Pro plugin to access the style options.', 'wt-widgets-elementor'),
                        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                    ]
                );
            }

            $this->end_controls_section();



            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {

                $this->start_controls_section(
                    'meta_trip_rating_content',
                    [
                        'label' => esc_html__('Trip Rating', 'wt-widgets-elementor'),
                        'tab'   => Controls_Manager::TAB_CONTENT,
                    ]
                );

                $this->add_responsive_control(
                    'show_trip_max_rating',
                    array(
                        'label' => esc_html__('Show Trip Max Rating (out of 5)', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                        'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                        'default' => 'yes',
                        'label_block' => true,
                        'condition' => [
                            'rating_layout' => 'minimal'
                        ]
                    ),
                );

                $this->add_control(
                    'trip_rating_icon',
                    array(
                        'label' => esc_html__('Rating Icon', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::ICONS,
                        'default' => [
                            'value' => 'fa fa-star',
                            'library' => 'fa-solid',
                        ],
                        'recommended' => [
                            'fa-solid' => [
                                'circle',
                                'dot-circle',
                                'square-full',
                            ],
                            'fa-regular' => [
                                'circle',
                                'dot-circle',
                                'square-full',
                            ],
                        ],
                        'condition' => [
                            'rating_layout' => 'minimal'
                        ]
                    )
                );

                $this->add_responsive_control(
                    'show_trip_rating_icon',
                    array(
                        'label' => esc_html__('Show Rating Icon', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Show', 'wt-widgets-elementor'),
                        'label_off' => esc_html__('Hide', 'wt-widgets-elementor'),
                        'default' => 'yes',
                        'condition' => [
                            'rating_layout' => 'minimal'
                        ]
                    ),
                );

                $this->add_control(
                    'trip_rating_outer_layer_color',
                    array(
                        'label' => esc_html__('Outer Layer Color', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-basic .wp-travel-average-review::before' => 'color:{{VALUE}}'
                        ],
                        'condition' => [
                            'rating_layout' => 'basic'
                        ]
                    )
                );

                $this->add_control(
                    'trip_rating_fill_color',
                    array(
                        'label' => esc_html__('Fill Color', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-basic .wp-travel-average-review span::before' => 'color:{{VALUE}}'
                        ],
                        'condition' => [
                            'rating_layout' => 'basic'
                        ]
                    )
                );

                $this->add_control(
                    'trip_rating_icon_color',
                    array(
                        'label' => esc_html__('Icon Color', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-minimal i' => 'color:{{VALUE}}'
                        ],
                        'condition' => [
                            'rating_layout' => 'minimal'
                        ]
                    )
                );

                $this->add_responsive_control(
                    'trip_rating_icon_size',
                    [
                        'label' => esc_html__('Size', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => ['px', '%', 'rem', 'vw'],
                        'range' => [
                            'px' => [
                                'min' => 1,
                                'max' => 300,
                                'step' => 1,
                            ],
                            '%' => [
                                'min' => 1,
                                'max' => 100,
                                'step' => 1,
                            ],
                            'rem' => [
                                'min' => 10,
                                'max' => 20,
                                'step' => 1,
                            ],
                            'vw' => [
                                'min' => 1,
                                'max' => 100,
                                'step' => 1,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => 13,
                        ],
                        'condition' => [
                            'rating_layout' => 'minimal'
                        ]
                    ]
                );

                $this->add_control(
                    'trip_minimal_bg_color',
                    array(
                        'label' => esc_html__('Background Color', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-minimal' => 'background:{{VALUE}}'
                        ],
                        'condition' => [
                            'rating_layout' => 'minimal'
                        ]
                    )
                );

                $this->end_controls_section();

                $this->start_controls_section(
                    'meta_trip_rating_styles',
                    [
                        'label' => esc_html__('Trip Rating', 'wt-widgets-elementor'),
                        'tab'   => Controls_Manager::TAB_STYLE,
                        'condition' => [
                            'rating_layout' => 'minimal',
                        ],
                    ]
                );

                $this->add_control(
                    'meta_trip_rating_padding',
                    [
                        'label' => esc_html__('Padding', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%', 'em', 'rem'],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-minimal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_control(
                    'meta_trip_rating_color',
                    array(
                        'label' => esc_html__('Text Color', 'wt-widgets-elementor'),
                        'type'  => Controls_Manager::COLOR,
                        'default'   => '#fff',
                        'selectors' => array(
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-minimal' => 'color: {{VALUE}}',
                        ),
                    ),
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    array(
                        'name' => 'meta_trip_rating_typography',
                        'selector'  => '{{ WRAPPER }} .wtwe-meta-trip-rating .wtwe-rating-minimal',
                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Stroke::get_type(),
                    array(
                        'name' => 'meta_trip_rating_stroke',
                        'selector' => '{{ WRAPPER }} .wtwe-meta-trip-rating .wtwe-rating-minimal',
                    )
                );

                $this->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    array(
                        'name' => 'meta_trip_rating_shadow',
                        'selector' => '{{ WRAPPER }} .wtwe-meta-trip-rating .wtwe-rating-minimal',
                    )
                );

                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'meta_trip_rating_background',
                        'types' => ['classic', 'gradient', 'video'],
                        'selector' => '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-minimal',
                    ]
                );

                $this->add_control(
                    'meta_trip_rating_border_radius',
                    [
                        'label' => esc_html__('Border Radius', 'wt-widgets-elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%', 'em', 'rem'],
                        'selectors' => [
                            '{{WRAPPER}} .wtwe-meta-trip-rating .wtwe-rating-minimal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->end_controls_section();
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
            $wptravel_trip_id = get_the_ID();

            if (get_post_type(get_the_ID()) == 'itineraries') {
?>
                <div class="wtwe-meta-trip-rating">
                    <?php if ($settings['rating_layout'] == 'basic') {
                    ?>
                        <div class="wtwe-rating-basic">
                            <?php echo esc_html(wptravel_single_trip_rating($wptravel_trip_id)); ?>
                        </div>
                    <?php
                    } else { ?>
                        <span class="wtwe-rating-minimal">
                            <i class="<?php echo esc_attr($settings['trip_rating_icon']['value']); ?> <?php if ($settings['show_trip_rating_icon'] != 'yes') echo 'wtwe-hidden'; ?>" style="font-size:<?php echo esc_attr($settings['trip_rating_icon_size']['size']) . esc_attr($settings['trip_rating_icon_size']['unit']) ?>; color:<?php echo esc_attr($settings['trip_rating_icon_color']) ?>;"></i>
                            <?php echo esc_html(wptravel_get_average_rating($wptravel_trip_id));
                            if ($settings['show_trip_max_rating']) echo esc_html__('/5', 'wt-widgets-elementor'); ?>
                        </span>
                    <?php } ?>
                </div>
            <?php
            } elseif ($wptravel_trip_id > 0 && get_post_type($wptravel_trip_id) == 'itineraries') {
            ?>
                <div class="wtwe-meta-trip-rating">
                    <?php if ($settings['rating_layout'] == 'basic') {
                    ?>
                        <div class="wtwe-rating-basic ">
                            <?php echo esc_html(wptravel_single_trip_rating($wptravel_trip_id)); ?>
                        </div>
                    <?php
                    } else { ?>
                        <span class="wtwe-rating-minimal ">
                            <i class="<?php echo esc_attr($settings['trip_rating_icon']['value']) ?> <?php if ($settings['show_trip_rating_icon'] != 'yes') echo 'wtwe-hidden' ?>" style="font-size:<?php echo esc_attr($settings['trip_rating_icon_size']['size']) . esc_attr($settings['trip_rating_icon_size']['unit']) ?>; color:<?php echo esc_attr($settings['trip_rating_icon_color']) ?>;"></i>
                            <?php echo esc_html(wptravel_get_average_rating($wptravel_trip_id));
                            if ($settings['show_trip_max_rating']) echo esc_html__('/5', 'wt-widgets-elementor'); ?>
                        </span>
                    <?php } ?>
                </div>
            <?php
            }
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
        protected function content_template()
        {
            ?>
            <div class="wtwe-meta-trip-rating">
                <# if ( settings.rating_layout=='basic' ) { #>
                    <div class="wtwe-meta-trip-rating">
                        <div class="wtwe-rating-basic">
                            <div class="wp-travel-average-review" title="Rated 4 out of 5">
                            <a href="#">
                                <span style="width:80%">
                                    <strong itemprop="ratingValue" class="rating"><?php echo esc_html__( '4', 'wt-widgets-elementor' ); ?></strong> 
                                    <?php echo esc_html__( 'out of ', 'wt-widgets-elementor' ); ?>
                                    <span itemprop="bestRating"><?php echo esc_html__( '5', 'wt-widgets-elementor' ); ?></span> 
                                </span>
                            </a>

                        </div>
                        </div>
                        
                        
                    </div>
                    <# } else { 
                        var get_icon = settings.trip_rating_icon.value;
                        var get_font_size = settings.trip_rating_icon_size;
                        #>
                        <div class="wtwe-meta-trip-rating">
                            <span class="wtwe-rating-minimal">
                                <# if(settings.show_trip_rating_icon) { #>
                            <i class="fas {{ get_icon }}" style="font-size: {{get_font_size.size}}{{get_font_size.unit}};"></i>
                            <# } #> 4
                                <# if(settings.show_trip_max_rating == 'yes'){ #> /5 <# } #></span>
                        </div>

                        <# } #>
            </div>
<?php
        }
    }
}
