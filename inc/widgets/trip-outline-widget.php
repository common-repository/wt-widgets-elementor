<?php
/**
 * This file use to fetch trip outline using id or global post
 * 
 */
namespace WTWE\Widgets\Single_Page_Trip_Outline;
/**
 * @uses elementor widget namespce
 */
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use WP_Travel_Itinerary;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * create class for register widget
 * 
 */
if ( ! class_exists( 'WTWE_Trip_Outline' ) ) {
    class WTWE_Trip_Outline extends Widget_Base {
        public function __construct( $data = [], $args = [] ) {
            parent::__construct($data, $args);
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-outline';
        }
        // Create title of trip-outline widget name
        public function get_title() {
            return esc_html__( 'Trip Outline', 'wt-widgets-elementor' );
        }
        // set icon 
        public function get_icon() {
            return 'eicon-post-excerpt';
        }
        // set widget under the wp-travel category widgets
        public function get_categories() {
            return ['wp-travel-single'];
        }
        
        // Register and setup control setting for trip outline
        public function _register_controls() {
            // register controller for trip_id

            /**
             * start itinerary heading
             */

            // register controller for itinerary heading section
            $this->start_controls_section(
                'itinerary_heading',
                array(
                    'label' => esc_html__('Itinerary Heading', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );
            // itinerary heading enable disable
            $this->add_control(
                'enable_heading',
                array(
                    'label'   => esc_html__('Enable Heading', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor'  ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor'  ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // itinerary heading text
            $this->add_control(
                'heading_itinerary',
                array(
                    'label'   => esc_html__('Heading', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => esc_html__( 'Itineraries', 'wt-widgets-elementor'  ),
                )
            );
            // itineary heading tag
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'itinerary_tag',
                    array(
                        'label'   => esc_html__('Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h1'  => esc_html__( 'H1', 'wt-widgets-elementor'  ),
                            'h2'  => esc_html__( 'H2', 'wt-widgets-elementor'  ),
                            'h3'  => esc_html__( 'H3', 'wt-widgets-elementor'  ),
                            'h4'  => esc_html__( 'H4', 'wt-widgets-elementor'  ),
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                            'h6'  => esc_html__( 'H6', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h5',
                    )
                );

            } else {
                $this->add_control(
                    'itinerary_tag',
                    array(
                        'label'   => esc_html__('Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h5',
                        'condition'   => [
                            'editor_view' => '0', // Specify the condition to hide the control in the editor
                        ],
                    )
                );
                
            }
            
            $this->end_controls_section();
            //end itinerary heading section
            $this->start_controls_section(
                'outline_style',
                array(
                    'label' => esc_html__('Itinerary Heading', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //css color
            $this->add_control(
                'itinerary_heading_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-itinerary-heading' => 'color: {{VALUE}}',
                    ],
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                // css typography
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'content_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-heading',
                ] 
            );
            // css custom css filter
            $this->add_group_control(
                \Elementor\Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'custom_css_filters',
                    'label' => 'CSS',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-heading',
                ]
            );
            // css text stroke
            $this->add_group_control(
                \Elementor\Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'text_stroke',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-heading',
                ]
            );
            } 
            
            $this->end_controls_section();
            /**
             * end itinrary heading
             * 
             * start itinerary label section
             */
            // register controller for itinerary label section
            $this->start_controls_section(
                'itinerary_label',
                array(
                    'label' => esc_html__('Itinerary Label', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );
            // itinerary label enable disable
            $this->add_control(
                'enable_label',
                array(
                    'label'   => esc_html__('Enable Label', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor'  ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor'  ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // itineary label tag

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'itinerary_label_tag',
                    array(
                        'label'   => esc_html__('Label Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h1'  => esc_html__( 'H1', 'wt-widgets-elementor'  ),
                            'h2'  => esc_html__( 'H2', 'wt-widgets-elementor'  ),
                            'h3'  => esc_html__( 'H3', 'wt-widgets-elementor'  ),
                            'h4'  => esc_html__( 'H4', 'wt-widgets-elementor'  ),
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                            'h6'  => esc_html__( 'H6', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h4',
                    )
                );
            } else {
                $this->add_control(
                    'itinerary_label_tag',
                    array(
                        'label'   => esc_html__('Label Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h6'  => esc_html__( 'H6', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h5',
                        'condition'   => [
                            'editor_view' => '0', // Specify the condition to hide the control in the editor
                        ],
                    )
                );
                
            }
           
            $this->end_controls_section();
            //end itinerary label section for content
            $this->start_controls_section(
                'itinerary_label_style',
                array(
                    'label' => esc_html__('Itinerary Label', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //css color
            $this->add_control(
                'itinerary_label_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-itinerary-label' => 'color: {{VALUE}}',
                    ],
                )
            );

            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                 // css typography
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'label_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-label',
                ] 
            );
            // css custom css filter
            $this->add_group_control(
                \Elementor\Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'itinerary_label_css_filters',
                    'label' => 'CSS',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-label',
                ]
            );
            // css text stroke
            $this->add_group_control(
                \Elementor\Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'itinerary_label_text_stroke',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-label',
                ]
            );
            } 
           
            $this->end_controls_section();
            /**
             * end itinerary label section
             * 
             * start itinerary date section
             */
            // register controller for itinerary date section
            $this->start_controls_section(
                'itinerary_date',
                array(
                    'label' => esc_html__('Itinerary Date', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );
            // itinerary date enable disable
            $this->add_control(
                'enable_date',
                array(
                    'label'   => esc_html__('Enable Date', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor'  ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor'  ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // itineary date tag
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'itinerary_date_tag',
                    array(
                        'label'   => esc_html__('Date Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h1'  => esc_html__( 'H1', 'wt-widgets-elementor'  ),
                            'h2'  => esc_html__( 'H2', 'wt-widgets-elementor'  ),
                            'h3'  => esc_html__( 'H3', 'wt-widgets-elementor'  ),
                            'h4'  => esc_html__( 'H4', 'wt-widgets-elementor'  ),
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                            'h6'  => esc_html__( 'H6', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h3',
                    )
                );
            } else {
                $this->add_control(
                    'itinerary_date_tag',
                    array(
                        'label'   => esc_html__('Date Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                           
                        ],
                        'default' => 'h5',
                        'condition'   => [
                            'editor_view' => '0', // Specify the condition to hide the control in the editor
                        ],
                    )
                );
                
            }
            
            $this->end_controls_section();
            //end itinerary date section for content
            $this->start_controls_section(
                'itinerary_date_style',
                array(
                    'label' => esc_html__('Itinerary Date', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //css color
            $this->add_control(
                'itinerary_date_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-itinerary-date' => 'color: {{VALUE}}',
                    ],
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                 // css typography
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'date_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-date',
                ] 
            );
            // css custom css filter
            $this->add_group_control(
                \Elementor\Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'itinerary_date_css_filters',
                    'label' => 'CSS',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-date',
                ]
            );
            // css text stroke
            $this->add_group_control(
                \Elementor\Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'itinerary_date_text_stroke',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-date',
                ]
            );
            }
           
            $this->end_controls_section();
            /**
             * end itinerary date section
             * 
             * start itinerary time section
             */
            // register controller for itinerary date section
            $this->start_controls_section(
                'itinerary_time',
                array(
                    'label' => esc_html__('Itinerary Time', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );
            // itinerary time enable disable
            $this->add_control(
                'enable_time',
                array(
                    'label'   => esc_html__('Enable Time', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor'  ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor'  ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // itineary time tag
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'itinerary_time_tag',
                    array(
                        'label'   => esc_html__('Time Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h1'  => esc_html__( 'H1', 'wt-widgets-elementor'  ),
                            'h2'  => esc_html__( 'H2', 'wt-widgets-elementor'  ),
                            'h3'  => esc_html__( 'H3', 'wt-widgets-elementor'  ),
                            'h4'  => esc_html__( 'H4', 'wt-widgets-elementor'  ),
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                            'h6'  => esc_html__( 'H6', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h3',
                    )
                );
            } else {
                $this->add_control(
                    'itinerary_time_tag',
                    array(
                        'label'   => esc_html__('Time Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                          
                        ],
                        'default' => 'h5',
                        'condition'   => [
                            'editor_view' => '0', // Specify the condition to hide the control in the editor
                        ],
                    )
                );
                
            }
           
            $this->end_controls_section();
            //end itinerary time section for content
            $this->start_controls_section(
                'itinerary_time_style',
                array(
                    'label' => esc_html__('Itinerary Time', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //css color
            $this->add_control(
                'itinerary_time_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-itinerary-time' => 'color: {{VALUE}}',
                    ],
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                // css typography
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'time_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-time',
                ] 
            );
            // css custom css filter
            $this->add_group_control(
                \Elementor\Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'itinerary_time_css_filters',
                    'label' => 'CSS',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-time',
                ]
            );
            // css text stroke
            $this->add_group_control(
                \Elementor\Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'itinerary_time_text_stroke',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-time',
                ]
            );
            }
            
            $this->end_controls_section();
            /**
             * end itinerary time section
             * 
             * start itierary title section
             */
            // register controller for itinerary title section
            $this->start_controls_section(
                'itinerary_title',
                array(
                    'label' => esc_html__('Itinerary Title', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );
            // itinerary title enable disable
            $this->add_control(
                'enable_title',
                array(
                    'label'   => esc_html__('Enable Title', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor'  ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor'  ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );
            // itineary title tag
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                $this->add_control(
                    'itinerary_title_tag',
                    array(
                        'label'   => esc_html__('Title Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h1'  => esc_html__( 'H1', 'wt-widgets-elementor'  ),
                            'h2'  => esc_html__( 'H2', 'wt-widgets-elementor'  ),
                            'h3'  => esc_html__( 'H3', 'wt-widgets-elementor'  ),
                            'h4'  => esc_html__( 'H4', 'wt-widgets-elementor'  ),
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                            'h6'  => esc_html__( 'H6', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h3',
                    )
                );
            } else {
                
                $this->add_control(
                    'itinerary_title_tag',
                    array(
                        'label'   => esc_html__('Title Tag', 'wt-widgets-elementor' ),
                        'type'    => Controls_Manager::SELECT2,
                        'options' => [
                            'h5'  => esc_html__( 'H5', 'wt-widgets-elementor'  ),
                        ],
                        'default' => 'h5',
                        'condition'   => [
                            'editor_view' => '0', // Specify the condition to hide the control in the editor
                        ],
                    )
                );
            }
           
            $this->end_controls_section();
            //end itinerary title section for content
            $this->start_controls_section(
                'itinerary_title_style',
                array(
                    'label' => esc_html__('Itinerary Title', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //css color
            $this->add_control(
                'itinerary_title_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-itinerary-title' => 'color: {{VALUE}}',
                    ],
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                // css typography
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-title',
                ] 
            );
            // css custom css filter
            $this->add_group_control(
                \Elementor\Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'itinerary_title_css_filters',
                    'label' => 'CSS',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-title',
                ]
            );
            // css text stroke
            $this->add_group_control(
                \Elementor\Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'itinerary_title_text_stroke',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-title',
                ]
            );
            }
            
            $this->end_controls_section();
            /**
             * end itinerary title section 
             * 
             * start itinerary description section
             */
            $this->start_controls_section(
                'itinerary_desc',
                array(
                    'label' => esc_html__('Itinerary Description', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                )
            );
            // itinerary desc enable disable
            $this->add_control(
                'enable_desc',
                array(
                    'label'   => esc_html__('Enable Description', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'wt-widgets-elementor'  ),
                    'label_off' => esc_html__( 'Hide', 'wt-widgets-elementor'  ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                )
            );

            $this->end_controls_section();
            //end itinerary desc section for content
            $this->start_controls_section(
                'itinerary_desc_style',
                array(
                    'label' => esc_html__('Itinerary Description', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );
            //css color
            $this->add_control(
                'itinerary_desc_text_color',
                array(
                    'label'   => esc_html__('Text Color', 'wt-widgets-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#000000',
                    'selectors' => [
                        '{{WRAPPER}} .wp-travel-itinerary-desc' => 'color: {{VALUE}}',
                    ],
                )
            );
            if (is_plugin_active('wp-travel-pro/wp-travel-pro.php')) {
                // css typography
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'desc_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-desc',
                ] 
            );
            // css custom css filter
            $this->add_group_control(
                \Elementor\Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'itinerary_desc_css_filters',
                    'label' => 'CSS',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-desc',
                ]
            );
            // css text stroke
            $this->add_group_control(
                \Elementor\Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'itinerary_desc_text_stroke',
                    'selector' => '{{WRAPPER}} .wp-travel-itinerary-desc',
                ]
            );
            } 
            
            
            $this->end_controls_section();
        }
        protected function content_template() {
            $trip_id = get_the_ID();
            
        ?> 
        <div class="wtwe-trip-outline">
			<h5 class="wtwe-trip-outline"><?php echo esc_html__( 'Trip Outline', 'wt-widgets-elementor' ); ?></h5>
            <?php \WTWE\Helper\WTWE_Helper::wtwe_get_widget_notice( __( 'Only visible on Frontend.', 'wt-widgets-elementor' ), 'info'); ?>
		</div>

        <?php
        }
        //Show content on frontend trip single page
        protected function render() {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            global $post;
            // echo "<pre>"; print_r($post);
            $elementor_setting = $this->get_settings_for_display();
            $trip_id = get_the_id();

            $enable_heading = isset( $elementor_setting['enable_heading'] ) ? $elementor_setting['enable_heading'] : 'yes';
            $heading_tag = isset( $elementor_setting['itinerary_tag'] ) ? $elementor_setting['itinerary_tag'] : 'h2';
            $itinerary_heading = isset( $elementor_setting['heading_itinerary'] ) ? $elementor_setting['heading_itinerary'] : 'Itineraries';

            $enable_label = isset( $elementor_setting['enable_label'] ) ? $elementor_setting['enable_label'] : 'yes';
            $label_tag = isset( $elementor_setting['itinerary_label_tag'] ) ? $elementor_setting['itinerary_label_tag'] : 'h4';

            $enable_date = isset( $elementor_setting['enable_date'] ) ? $elementor_setting['enable_date'] : 'yes';
            $date_tag = isset( $elementor_setting['itinerary_date_tag'] ) ? $elementor_setting['itinerary_date_tag'] : 'h3';

            $enable_time = isset( $elementor_setting['enable_time'] ) ? $elementor_setting['enable_time'] : 'yes';
            $time_tag = isset( $elementor_setting['itinerary_time_tag'] ) ? $elementor_setting['itinerary_time_tag'] : 'h3';
            
            $enable_title = isset( $elementor_setting['enable_title'] ) ? $elementor_setting['enable_title'] : 'yes';
            $title_tag = isset( $elementor_setting['itinerary_title_tag'] ) ? $elementor_setting['itinerary_title_tag'] : 'h3';

            $enable_desc = isset( $elementor_setting['enable_desc'] ) ? $elementor_setting['enable_desc'] : 'yes';
            $desc_tag = isset( $elementor_setting['itinerary_desc_tag'] ) ? $elementor_setting['itinerary_desc_tag'] : 'p';

            $tab_data = wptravel_get_frontend_tabs();
	        $content = is_array( $tab_data ) && isset( $tab_data['trip_outline'] ) && isset( $tab_data['trip_outline']['content'] ) ? $tab_data['trip_outline']['content'] : '';

            if ( !empty( get_the_ID() ) && get_the_ID() > 0 && get_post_type( get_the_ID() ) == 'itineraries' ) {
                $wptravel_trip_id     = get_the_ID();
                $wptravel_itineraries = get_post_meta( $wptravel_trip_id, 'wp_travel_trip_itinerary_data', true );
                
                if ( isset( $wptravel_itineraries ) && ! empty( $wptravel_itineraries ) ) : 
                    echo '<div id="wptravel-block-trip-outline" class="wptravel-block-wrapper wptravel-block-trip-outline">'; //@phpcs:ignore
                    echo wpautop( do_shortcode( $content ) );
                ?>
                    <div class="itenary clearfix">
                        <div class="timeline-contents clearfix">
                            <?php
                                if ( $enable_heading == 'yes' ) {
                                    echo '<' . esc_html( $heading_tag ) . ' style="text-align:center" class="wp-travel-itinerary-heading" >';
                                    printf(
                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                        esc_html($itinerary_heading),
                                        esc_html($wptravel_trip_id)
                                    );
                                    echo '</' . esc_html( $heading_tag ) . '>';
                                } else {
                                    echo '';
                                }
                                $wptravel_index = 1;
                                foreach ( $wptravel_itineraries as $wptravel_itinerary ) :
                                    if ( 0 == $wptravel_index % 2 ) :
                                        $wptravel_first_class  = 'right';
                                        $wptravel_second_class = 'left';
                                        $wptravel_row_reverse  = 'row-reverse';
                                    else :
                                        $wptravel_first_class  = 'left';
                                        $wptravel_second_class = 'right';
                                        $wptravel_row_reverse  = '';
                                    endif;
                                    $wptravel_time_format = get_option( 'time_format' );
                
                                    $wptravel_itinerary_label = '';
                                    $wptravel_itinerary_title = '';
                                    $wptravel_itinerary_desc  = '';
                                    $wptravel_itinerary_date  = '';
                                    $wptravel_itinerary_time  = '';
                                    if ( isset( $wptravel_itinerary['label'] ) && '' !== $wptravel_itinerary['label'] ) {
                                        $wptravel_itinerary_label = stripslashes( $wptravel_itinerary['label'] );
                                    }
                                    if ( isset( $wptravel_itinerary['title'] ) && '' !== $wptravel_itinerary['title'] ) {
                                        $wptravel_itinerary_title = stripslashes( $wptravel_itinerary['title'] );
                                    }
                                    if ( isset( $wptravel_itinerary['desc'] ) && '' !== $wptravel_itinerary['desc'] ) {
                                        $wptravel_itinerary_desc = stripslashes( $wptravel_itinerary['desc'] );
                                    }
                                    if ( isset( $wptravel_itinerary['date'] ) && '' !== $wptravel_itinerary['date'] && 'invalid date' !== strtolower( $wptravel_itinerary['date'] ) ) {
                                        $wptravel_itinerary_date = wptravel_format_date( $wptravel_itinerary['date'] );
                                    }
                                    if ( isset( $wptravel_itinerary['time'] ) && '' !== $wptravel_itinerary['time'] ) {
                                        $wptravel_itinerary_time = stripslashes( $wptravel_itinerary['time'] );
                                        $wptravel_itinerary_time = date( $wptravel_time_format, strtotime( $wptravel_itinerary_time ) ); // @phpcs:ignore
                                    }
                                    ?>
                                    <div class="col clearfix <?php echo esc_attr( $wptravel_row_reverse ); ?>">
                                        <div class="tc-heading <?php echo esc_attr( $wptravel_first_class ); ?> clearfix">
                                            <?php if ( '' !== $wptravel_itinerary_label ) :
                                                if( $enable_label == 'yes' ) {
                                                    echo '<' . esc_html( $label_tag ) . ' style="text-align:' . esc_attr( $wptravel_second_class ) . '" class="wp-travel-itinerary-label" >';
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_label_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_label),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $label_tag ) . '>';
                                                } else {
                                                    echo '';
                                                }
                                            endif;
                                            if ( $wptravel_itinerary_date ) : ?>
                                                <?php if( $enable_date == 'yes' ) {
                                                    echo '<' . esc_html( $date_tag ) . ' style="text-align:' . esc_attr( $wptravel_second_class ) . '" class="arrival wp-travel-itinerary-date" >' . esc_html__( 'Date : ', 'wt-widgets-elementor' );
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_date_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_date),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $date_tag ) . '>';
                                                } else {
                                                    echo '';
                                                }
                                            endif; ?>
                                            <?php if ( $wptravel_itinerary_time ) : ?>
                                                <?php if( $enable_time == 'yes' ) {
                                                    echo '<' . esc_html( $time_tag ) . ' style="text-align:' . esc_attr( $wptravel_second_class ) . '" class="wp-travel-itinerary-time" >' .  esc_html__( 'Time : ', 'wt-widgets-elementor' );
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_time_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_time),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $time_tag ) . '>';
                                                } else {
                                                    echo '';
                                                }
                                            endif; ?>
                                        </div><!-- tc-content -->
                                        <div class="tc-content <?php echo esc_attr( $wptravel_second_class ); ?> clearfix" >
                                            <?php if ( '' !== $wptravel_itinerary_title ) : ?>
                                            <?php if ( $enable_title == 'yes' ) {
                                                echo '<' . esc_html( $title_tag ) . ' style="text-align:' . esc_attr( $wptravel_first_class ) . '" class="wp-travel-itinerary-title" >';
                                                printf(
                                                    esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_title_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                    esc_html($wptravel_itinerary_title),
                                                    esc_html($wptravel_trip_id)
                                                );
                                                echo '</' . esc_html( $title_tag ) . '>';
                                            } else {
                                                echo '';
                                            }
                                            endif; ?>
                                            <?php do_action( 'wp_travel_itineraries_after_title', $wptravel_itinerary ); ?>
                                            <div style="text-align:<?php echo esc_attr( $wptravel_first_class ); ?>" class="wp-travel-itinerary-desc">
                                                <?php if ( $enable_desc == 'yes' ) {
                                                    printf(
                                                        esc_html__(apply_filters('wp_travel_ititneraries_trip_outline_title_tab',wp_strip_all_tags(wpautop($wptravel_itinerary_desc)),'%d'),'wt-widgets-elementor'),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                } else {
                                                    echo '';
                                                } ?>
                                            </div>
                                            <div class="image"></div>
                                        </div><!-- tc-content -->
                                    </div><!-- first-content -->
                                    <?php $wptravel_index++; ?>
                                <?php endforeach; ?>
                        </div><!-- timeline-contents -->
                    </div><!-- itinerary -->
                </div>
                <?php endif; 
            }  elseif ( $trip_id > 0 && get_post_type( $trip_id ) == 'itineraries' ) {
                $wptravel_trip_id     = $trip_id;
                $wptravel_itineraries = get_post_meta( $wptravel_trip_id, 'wp_travel_trip_itinerary_data', true );
                
                if ( isset( $wptravel_itineraries ) && ! empty( $wptravel_itineraries ) ) : ?>
                    <div class="itenary clearfix">
                        <div class="timeline-contents clearfix">
                            <?php if( $enable_heading == 'yes' ) {
                                echo '<' . esc_html( $heading_tag ) . ' style="text-align:center" class="wp-travel-itinerary-heading" >';
                                printf(
                                    esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                    esc_html($itinerary_heading),
                                    esc_html($wptravel_trip_id)
                                );
                                echo '</' . esc_html( $heading_tag ) . '>';
                                } else {
                                    echo '';
                                }

                                $wptravel_index = 1;
                                foreach ( $wptravel_itineraries as $wptravel_itinerary ) :
                                    if ( 0 == $wptravel_index % 2 ) :
                                        $wptravel_first_class  = 'right';
                                        $wptravel_second_class = 'left';
                                        $wptravel_row_reverse  = 'row-reverse';
                                    else :
                                        $wptravel_first_class  = 'left';
                                        $wptravel_second_class = 'right';
                                        $wptravel_row_reverse  = '';
                                    endif;
                                    // print_r($wptravel_index);
                                    $wptravel_time_format = get_option( 'time_format' );
                
                                    $wptravel_itinerary_label = '';
                                    $wptravel_itinerary_title = '';
                                    $wptravel_itinerary_desc  = '';
                                    $wptravel_itinerary_date  = '';
                                    $wptravel_itinerary_time  = '';
                                    if ( isset( $wptravel_itinerary['label'] ) && '' !== $wptravel_itinerary['label'] ) {
                                        $wptravel_itinerary_label = stripslashes( $wptravel_itinerary['label'] );
                                    }
                                    if ( isset( $wptravel_itinerary['title'] ) && '' !== $wptravel_itinerary['title'] ) {
                                        $wptravel_itinerary_title = stripslashes( $wptravel_itinerary['title'] );
                                    }
                                    if ( isset( $wptravel_itinerary['desc'] ) && '' !== $wptravel_itinerary['desc'] ) {
                                        $wptravel_itinerary_desc = stripslashes( $wptravel_itinerary['desc'] );
                                    }
                                    if ( isset( $wptravel_itinerary['date'] ) && '' !== $wptravel_itinerary['date'] && 'invalid date' !== strtolower( $wptravel_itinerary['date'] ) ) {
                                        $wptravel_itinerary_date = wptravel_format_date( $wptravel_itinerary['date'] );
                                    }
                                    if ( isset( $wptravel_itinerary['time'] ) && '' !== $wptravel_itinerary['time'] ) {
                                        $wptravel_itinerary_time = stripslashes( $wptravel_itinerary['time'] );
                                        $wptravel_itinerary_time = date( $wptravel_time_format, strtotime( $wptravel_itinerary_time ) ); // @phpcs:ignore
                                    }
                                    ?>
                                    <div class="col clearfix <?php echo esc_attr( $wptravel_row_reverse ); ?>">
                                        <div class="tc-heading <?php echo esc_attr( $wptravel_first_class ); ?> clearfix">
                                            <?php if ( '' !== $wptravel_itinerary_label ) : ?>
                                                <?php if( $enable_label == 'yes' ) {
                                                    echo '<' . esc_html( $label_tag ) . ' style="text-align:' . esc_attr( $wptravel_second_class ) . '" class="wp-travel-itinerary-label" >';
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_label_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_label),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $label_tag ) . '>';
                                                } else {
                                                    echo '';
                                                } ?>
                                            <?php endif; ?>
                                            <?php if ( $wptravel_itinerary_date ) : ?>
                                                <?php if( $enable_date == 'yes' ) {
                                                    echo '<' . esc_html( $date_tag ) . ' style="text-align:' . esc_attr( $wptravel_second_class ) . '" class="arrival wp-travel-itinerary-date" >' . esc_html__( 'Date : ', 'wt-widgets-elementor' );
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_date_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_date),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $date_tag ) . '>';
                                                } else {
                                                    echo '';
                                                }
                                            endif; ?>
                                            <?php if ( $wptravel_itinerary_time ) : ?>
                                                <?php if( $enable_time == 'yes' ) {
                                                    echo '<' . esc_html( $time_tag ) . ' style="text-align:' . esc_attr( $wptravel_second_class ) . '" class="wp-travel-itinerary-time" >' .  esc_html__( 'Time : ', 'wt-widgets-elementor' );
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_time_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_time),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $time_tag ) . '>';
                                                } else {
                                                    echo '';
                                                }
                                            endif; ?>
                                        </div><!-- tc-content -->
                                        <div class="tc-content <?php echo esc_attr( $wptravel_second_class ); ?> clearfix" >
                                            <?php if ( '' !== $wptravel_itinerary_title ) : ?>
                                                <?php if ( $enable_title == 'yes' ) {
                                                    echo '<' . esc_html( $title_tag ) . ' style="text-align:' . esc_attr( $wptravel_first_class ) . '" class="wp-travel-itinerary-title" >';
                                                    printf(
                                                        esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_title_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                        esc_html($wptravel_itinerary_title),
                                                        esc_html($wptravel_trip_id)
                                                    );
                                                    echo '</' . esc_html( $title_tag ) . '>';
                                                } else {
                                                    echo '';
                                                }
                                            endif; ?>
                                            <?php do_action( 'wp_travel_itineraries_after_title', $wptravel_itinerary ); ?>
                                            <div style="text-align:<?php echo esc_attr( $wptravel_first_class ); ?>" class="wp-travel-itinerary-desc">
                                                <?php if ( $enable_desc == 'yes' ) {
                                                        printf(
                                                            esc_html__( apply_filters( 'wp_travel_ititneraries_trip_outline_desc_tab', '%s', '%d' ), 'wt-widgets-elementor' ),
                                                            wp_kses_post( wpautop( $wptravel_itinerary_desc ) ),
                                                            esc_html($wptravel_trip_id)
                                                        );
                                                    } else {
                                                        echo '';
                                                    }
                                                ?>
                                            </div>
                                            <div class="image"></div>
                                        </div><!-- tc-content -->
                                    </div><!-- first-content -->
                                    <?php $wptravel_index++; ?>
                                <?php endforeach; ?>
                        </div><!-- timeline-contents -->
                    </div><!-- itinerary -->
                <?php endif; 
            }
        }
    }
}