<?php

/**
 * Icon Holder class.
 *
 * @category   Class
 * @package    WTWidgetsElementor
 * @author     WP Travel
 * @license    https://opensource.org/licenses/GPL-2.0 GPL-2.0-only
 * @since      1.0.0
 * php version 7.4
 */

namespace WTWE\Widgets;

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || exit;

/**
 * Icon Holder widget class.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'WTWE_Icon_Holder' ) ) {
    class WTWE_Icon_Holder extends Widget_Base {
        /**
		 * Class constructor.
		 *
		 * @param array $data Widget data.
		 * @param array $args Widget arguments.
		 */
		public function __construct( $data = array(), $args = null ) {
			parent::__construct( $data, $args );
            $prefixed = defined( WP_DEBUG ) ? '.min' : '';
            wp_register_style( 'icon-holder', plugins_url( 'assets/css/icon-holder' . $prefixed . '.css', WTWE_PLUGIN_FILE ), array() );
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
		public function get_name() {
			return 'wp-travel-icon-holder';
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
		public function get_title() {
			return esc_html__( 'Icon Holder', 'wt-widgets-elementor' );
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
		public function get_icon() {
			return 'eicon-favorite';
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
		public function get_categories() {
			return array( 'wp-travel' );
		}

        /**
		 * Enqueue styles.
		 */
		public function get_style_depends() {
			return array( 'icon-holder' );
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
		protected function _register_controls() {
			$this->start_controls_section(
				'icon_content',
				array(
					'label' => esc_html__( 'Icon', 'wt-widgets-elementor' ),
					'tab'	=> Controls_Manager::TAB_CONTENT,
                ),
			);

            $this->add_control(
                'icon',
                array(
                    'label' => esc_html__( 'Icon', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-circle',
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
                )
            );

            // Icon Alignment Control
            $this->add_responsive_control(
                'alignment',
                array(
                    'label' => esc_html__( 'Alignment', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__( 'Left', 'wt-widgets-elementor' ),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'wt-widgets-elementor' ),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'wt-widgets-elementor' ),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => esc_html__( 'center', 'wt-widgets-elementor' ),
					'selectors' => array(
						'{{WRAPPER}} .wtwe-icon-wrapper' => 'text-align: {{VALUE}};',
					),
                    'toggle' => true,
                )
            );

            // Icon Link Control
            $this->add_control(
                'link',
                [
                    'label' => esc_html__( 'Link', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::URL,
                    'placeholder' => 'https://example.com',
                    'options' => array( 'url', 'is_external', 'nofollow' ),
                    'default' => array(
                        'url' => '',
                        'is_external' => true,
                        'no_follow' => false,
                    ),
                ]
            );

            // Icon Tooltip Control
            $this->add_control(
                'tooltip',
                [
                    'label' => esc_html__( 'Tooltip', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__( 'Tooltip text', 'wt-widgets-elementor' ),
                    'placeholder' => esc_html__( 'Enter tooltip text', 'wt-widgets-elementor' ),
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'icon_style',
				array(
					'label' => esc_html__( 'Icon', 'wt-widgets-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
            );

            // Icon Color Control
            $this->add_control(
                'icon_color',
                array(
                    'label' => esc_html__( 'Icon Color', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#38A827',
                )
            );

            // Icon Background Control
            $this->add_control(
                'background_color',
                [
                    'label' => esc_html__( 'Background Color', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => 'transparent',
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-icon-holder' => 'display: inline-block; background-color: {{VALUE}};',
                    ],
                ]
            );

            // Icon Size Control
            $this->add_responsive_control(
                'size',
                [
                    'label' => esc_html__( 'Size', 'wt-widgets-elementor' ),
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
                        'size' => 50,
                    ],
                ]
            );

            $this->add_responsive_control(
                'icon_holder_padding',
                array(
                    'label' => esc_html__( 'Padding', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => array( 'px' ),
                    'range' => array(
                        'px' => array(
                            'min' => 1,
                            'max' => 100,
                            'step' => 1,
                        ),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .wtwe-icon-holder' => 'padding: {{SIZE}}{{UNIT}};',
                    ),
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                array(
                    'name' => 'border',
                    'selector' => '{{WRAPPER}} .wtwe-icon-holder',
                )
            );

            $this->add_responsive_control(
                'border_radius',
                [
                    'label' => esc_html__( 'Border radius', 'wt-widgets-elementor' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem' ],
                    'selectors' => [
                        '{{WRAPPER}} .wtwe-icon-holder' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
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
        protected function render() {
            $settings = $this->get_settings_for_display();
            ?>
            <div class="wtwe-icon-wrapper">
                <div class="wtwe-icon-holder">
                    <?php if ( isset($settings['link']['url']) && !empty($settings['link']['url']) ) { ?>
                        <a href="<?php echo esc_url($settings['link']['url']); ?>" title="<?php echo esc_attr( $settings['tooltip'] ); ?>"  <?php if( $settings['link']['is_external'] == 'on') { echo " target=_blank"; } ?> >
                    <?php } ?>
                        <i class="<?php echo esc_attr( $settings['icon']['value'] ) ?>" style="font-size:<?php echo esc_attr( $settings['size']['size'] ) . esc_attr( $settings['size']['unit'] ) ?>; color:<?php echo esc_attr( $settings['icon_color'] ) ?>;"    >
                        </i>
                    <?php if ( isset($settings['link']['url']) && !empty($settings['link']['url']) ) { ?>
                        </a>
                    <?php } ?>
                </div>
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
        protected function _content_template() {
            ?>
            <div class="wtwe-icon-wrapper">
                <#
                view.addInlineEditingAttributes( 'tooltip', 'none' );
                view.addRenderAttribute( 'tooltip', 'title', settings.tooltip );
                view.addRenderAttribute( 'icon', 'class', settings.icon.value + ' ' + settings.style );
                view.addRenderAttribute( 'icon', 'style', 'font-size:' + settings.size.size + settings.size.unit + '; color:' + settings.icon_color + ';' );
                #>
                <div class="wtwe-icon-holder">
                    <a href="{{ settings.link.url }}" {{{ view.getRenderAttributeString( 'tooltip' ) }}}>
                        <i {{{ view.getRenderAttributeString( 'icon' ) }}}></i>
                    </a>
                </div>
            </div>
            <?php
        }
    }
}