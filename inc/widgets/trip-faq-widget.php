<?php
/**
 * This file use to fetch trip outline using id or global post
 * 
 */
namespace WTWE\Widgets\Single_Page_Trip_FAQ;
/**
 * @uses elementor widget namespce
 */
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Controls_Stack;
use WP_Travel_Itinerary;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Single Trip FAQ widget class.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'WTWE_Trip_FAQ' ) ) {
    class WTWE_Trip_FAQ extends Widget_Base {
        public function __construct( $data = [], $args = [] ) {
            parent::__construct($data, $args);

            // add_action(  );
            $prefixed = defined( WP_DEBUG ) ? '.min' : '';
            wp_register_style( 'trip-faq', plugins_url( 'assets/css/trip-faq' . $prefixed . '.css', WTWE_PLUGIN_FILE), [] );
            wp_register_script( 'faq-accordion', plugins_url( 'assets/js/accordion' . $prefixed . '.js', WTWE_PLUGIN_FILE), [ 'jquery' ] );
        }
        // create widget name
        public function get_name()
        {
            return 'wp-travel-trip-faq';
        }
        // Create title of trip-faq widget name
        public function get_title() {
            return esc_html__( 'Trip FAQ', 'wt-widgets-elementor' );
        }
        // set icon 
        public function get_icon() {
            return 'eicon-help-o';
        }
        // set widget under the wp-travel category widgets
        public function get_categories() {
            return ['wp-travel-single'];
        }
        
		/**
		 * Enqueue styles.
		 */
		public function get_style_depends() {
			return array( 'trip-faq' );
		}
        
		/**
		 * Enqueue scripts.
		 */
		public function get_script_depends() {
			return array( 'faq-accordion' );
		}
        
        // Register and setup control setting for trip faq
        public function _register_controls() {

            
            $this->start_controls_section(
                'accordion_title_style',
                array(
                    'label' => esc_html__('Accordion Title', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'accordion_title_background',
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .wtwe-faq-accordion .wtwe-faq-accordion-label',
                ]
            );
            
            $this->end_controls_section();

            $this->start_controls_section(
                'accordion_panel_style',
                array(
                    'label' => esc_html__( 'Accordion Panel', 'wt-widgets-elementor' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                )
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'accordion_panel_background',
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .wtwe-faq-accordion .wtwe-faq-accordion-answer',
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
            global $post;

            $elementor_setting = $this->get_settings_for_display();
            $trip_id = isset( $elementor_setting['trip_id'] ) ? $elementor_setting['trip_id'] : 0;

            if ( !empty( get_the_ID() ) && get_the_ID() > 0 && get_post_type( get_the_ID() ) == 'itineraries' ) {
                $wptravel_trip_id = get_the_ID();
                $faqs = wptravel_get_faqs( $wptravel_trip_id );
            ?>
                <section class="wtwe-faq-accordion">
                    <?php foreach($faqs as $faq) { ?>
                        <div class="wtwe-faq-accordion-panel">
                            <div class="wtwe-faq-accordion-label">
                                <span class="wtwe-faq-accordion-question"><?php echo esc_html( $faq['question'] ) ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="wtwe-faq-accordion-answer"><?php echo esc_html( $faq['answer'] ) ?></div>
                        </div>
                    <?php } ?>
                </section>
            <?php
            } elseif( $trip_id > 0 && get_post_type( $trip_id ) == 'itineraries' ) {
                $wptravel_trip_id = $trip_id;
                $faqs = wptravel_get_faqs( $wptravel_trip_id );
            ?>
                <section class="wtwe-faq-accordion">
                    <?php foreach($faqs as $faq) { ?>
                        <div class="wtwe-faq-accordion-panel">
                            <div class="wtwe-faq-accordion-label">
                                <span class="wtwe-faq-accordion-question"><?php echo esc_html( $faq['question'] ) ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="wtwe-faq-accordion-answer"><?php echo esc_html( $faq['answer'] ) ?></div>
                        </div>
                    <?php } ?>
                </section>
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
        protected function content_template() {
        ?>

            <section class="wtwe-faq-accordion">
                <div class="wtwe-faq-accordion-panel">
                    <div class="wtwe-faq-accordion-label">
                        <span class="wtwe-faq-accordion-question"><?php echo esc_html( 'Question One?', 'wt-widgets-elementor') ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="wtwe-faq-accordion-answer"><?php echo esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'wt-widgets-elementor' ); ?></div>
                    <div class="wtwe-faq-accordion-label">
                        <span class="wtwe-faq-accordion-question"><?php echo esc_html( 'Question Two?', 'wt-widgets-elementor') ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="wtwe-faq-accordion-answer"><?php echo esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'wt-widgets-elementor' ); ?></div>
                
                </div>        
            </section>
        <?php
        }
    }
}