<?php

/**
 * Plugin Name: WT Widgets for Elementor 
 * Plugin URI: 
 * Description: The WT Widgets for Elementor Plugin seamlessly integrates with WP Travel, offering widgets for trip search, filters, sliders, and more. It empowers users to enhance their travel websites built with Elementor, creating visually appealing interfaces with comprehensive functionality. It's the perfect solution for building attractive and user-friendly travel websites.
 * Version: 1.3.0
 * Author: WP Travel
 * Author URI: https://wptravel.io/
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.5
 * Elementor tested up to: 3.16.5
 *
 * Text Domain: wt-widgets-elementor
 * Domain Path: /i18n/languages/
 *
 * @package WTWidgetsElementor
 * @category Addon
 * @author WP Travel
 */

namespace WTWE;

use WTWE\Helper\WTWE_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


define( 'WTWE_PLUGIN_FILE', __FILE__ );
define( 'WTWE_ABSPATH', dirname( __FILE__ ) . '/' );
define( 'WTWE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WTWE_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WTWE_VERSION', '1.0.0' );
define( 'WTWE_API_VERSION', 'v1' );
define( 'WTWE_PLUGIN_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WTWE_TEXT_DOMAIN', 'wt-widgets-elementor' );
define( 'WTWE_MINIMUM_WPTRAVEL_VERSION', '7.3.0' );
define( 'WTWE_MINIMUM_ELEMENTOR_VERSION', '3.15.0' );

if ( ! class_exists( 'WTWE\WTWidgets' ) ) {
	/**
	 * Main WT Widgets for Elementor Class (singleton).
	 *
	 * @since 1.0
	 */
	final class WTWidgets {

		/**
		 * WT Widgets for Elementor version.
		 *
		 * @var string
		 */
		public $version = '1.0.0';
		/**
		 * WT Widgets for Elementor API version.
		 *
		 * @var string
		 */
		public $api_version = 'v1';

		/**
		 * The single instance of the class.
		 *
		 * @var WTWidgets
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * The text domain name of WT Widgets for Elementor.
		 *
		 * @package WTWidgetsElementor
		 * @since 1.0.0
		 */
		protected $text_domain_name = 'wt-widgets-elementor';

		/**
		 * Main WTWidgets Instance.
		 * Ensures only one instance of WTWidgets is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @return WTWidgets - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		/**
		 * WTWidgets Constructor.
		 * 
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Create Init function
		 *
		 * All Hook List For all function
		 */
		public function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_widget_styles' ) );
			add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_widget_styles' ) );

			// add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'register_widget_scripts' ) );
			// add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_widget_scripts' ) );
			add_action( 'elementor/editor/localize_settings', array( $this, 'wtwe_editor_localize' ) );
		}

		public function prefix_enable_trip_content(){
			return true;
		}

		public function register_widget_styles() {
			$prefixed = defined( WP_DEBUG ) ? '.min' : '';
			wp_register_style( 'wtwe-main', plugins_url( 'assets/css/wtwe-main' . $prefixed . '.css', WTWE_PLUGIN_FILE ), array() );
		}

		public function enqueue_widget_styles() {
			wp_enqueue_style( 'wtwe-main' );
		}

		/**
		 * Define plugins constant
		 *
		 * @return plugin paths, url, version etc
		 */
		public function define_constants() {
		}
		/**
		 * WT Widgets for Elementor file includes
		 */
		public function includes() {

			require_once WTWE_ABSPATH . 'inc/breadcrumb-class.php';

			add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
			add_action( 'elementor/elements/categories_registered', array( $this, 'wtwe_add_elementor_widget_categories' ) );
		}
		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name  Name of constant.
		 * @param  string $value Value of constant.
		 * @return void ( define name and value )
		 */
		public static function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value ); // phpcs:ignore
			}
		}
		/**
		 * Load Localization files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/wt-widgets-elementor/wtwe-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/wtwe-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'wtwe_plugin_locale', $locale, 'wt-widgets-elementor' ); // phpcs:ignore
			unload_textdomain( WTWE_TEXT_DOMAIN );
			load_textdomain( WTWE_TEXT_DOMAIN, WP_LANG_DIR . '/wtwe/wtwe-' . $locale . '.mo' );
			load_plugin_textdomain( WTWE_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
		}

		/**
		 * Include custom elementor widgets here.
		 */
		public function get_elementor_widget_files() {

			/**
			 * Require all the widget files once before register
			 */
			// require_once WTWE_ABSPATH . 'inc/widgets/currency-exchange.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-video-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/breadcrumb-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/add-to-cart-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/book-button-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/category-trips-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/featured-trips-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-maps-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/hero-slider-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/icon-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-rating-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-meta-type-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/related-trips-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-booking-date-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-code-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-excerpt-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-exclude-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-facts-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-faq-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-include-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-offers-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-outline-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-overview-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-price-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-review-lists-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-review-form-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-tabs-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-wishlist-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trips-by-type-grid-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trips-by-type-list-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trips-search-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-gallery-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trips-filter-widget.php';
			require_once WTWE_ABSPATH . 'inc/widgets/trip-enquiry-widget.php';
			require_once WTWE_ABSPATH . 'inc/helper.php';
			
			
		}

		public function register_widgets( $widgets_manager ) {
			$this->get_elementor_widget_files();

			/**
			 * Register WT Widgets
			 */
			// $widgets_manager->register( new Widgets\WTWE_Currency_Exchange() );
			$widgets_manager->register( new Widgets\WTWE_Trip_video() );
			$widgets_manager->register( new Widgets\WTWE_Breadcrumb() );
			$widgets_manager->register( new Widgets\WTWE_Trips_By_Type_Grid() );
			$widgets_manager->register( new Widgets\WTWE_Trips_By_Type_List() );
			$widgets_manager->register( new Widgets\WTWE_Trips_Search() );
			$widgets_manager->register( new Widgets\WTWE_Featured_Trips() );
			$widgets_manager->register( new Widgets\WTWE_Hero_Slider() );
			$widgets_manager->register( new Widgets\WTWE_Icon_Holder() );
			$widgets_manager->register( new Widgets\WTWE_Category_Trips() );
			$widgets_manager->register( new Widgets\WTWE_Trip_Filter() );
			$widgets_manager->register( new Widgets\Single_Page_Add_To_Cart\WTWE_Add_To_Cart() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Tabs\WTWE_Trip_Tabs() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Meta_Type\Trip_Meta_Type() );
			$widgets_manager->register( new Widgets\Single_Page_Meta_Trip_Rating\WTWE_Meta_Trip_Rating() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Maps\WTWE_Trip_Maps() );
			$widgets_manager->register( new Widgets\Single_Page_Related_Trips\WTWE_Related_Trips() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_FAQ\WTWE_Trip_FAQ() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Book_Button\WTWE_Book_Button() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Overview\WTWE_Trip_Overviews() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Outline\WTWE_Trip_Outline() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Include\WTWE_Trip_Include() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Excerpt\WTWE_Trip_Excerpt() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Price\WTWE_Trip_Price() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Exclude\WTWE_Trip_Exclude() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Wishlist\WTWE_Trip_Wishlist() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Code\WTWE_Trip_Code() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Offers\WTWE_Trip_Offers() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Booking_Date\WTWE_Trip_Booking_Date() );
			$widgets_manager->register( new Widgets\Single_Page_Trip_Review_List\WTWE_Trip_Review_List() ) ;
			$widgets_manager->register( new Widgets\Single_Page_Trip_Review_Form\WTWE_Trip_Review_Form() ) ;
			$widgets_manager->register( new Widgets\Single_Page_Trip_Enquiry\WTWE_Trip_Enquiry() ) ;
			$widgets_manager->register( new Widgets\Single_page_Trip_Facts\WTWE_Trip_Facts() );
			$widgets_manager->register( new Widgets\Single_page_Trip_Gallery\WTWE_Trip_Gallery() );
			
		}

		/**
		 * Register new elementor Widget categories.
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function wtwe_add_elementor_widget_categories( $elements_manager ) {
			$elements_manager->add_category(
				'wp-travel',
				array(
					'title' => esc_html__( 'WP Travel', 'wt-widgets-elementor' ),
					'icon'  => 'fa fa-plug',
				)
			);
			$elements_manager->add_category(
				'wp-travel-single',
				array(
					'title' => esc_html__( 'Single - WP Travel', 'wt-widgets-elementor' ),
					'icon'  => 'fa fa-plug',
				)
			);
		}
		/**
		 * localize data for elementor editor
		 */
		public function wtwe_editor_localize( $args ) {
			$trip_data                        = WTWE_Helper::wtwe_get_trip_itinerary();
			$args['trip_itinerary']           = $trip_data['itinerary'];
			$args['trip_include']             = $trip_data['trip_include'];
			$args['trip_exclude']             = $trip_data['trip_exclude'];
			$args['wp_travel_featured_trips'] = WTWE_Helper::wtwe_get_wp_travel_featured_trips();
			$args['wp_travel_trips_by_type']  = WTWE_Helper::wtwe_get_wp_travel_trips_by_type();
			$args['wp_travel_category_trips'] = WTWE_Helper::wtwe_get_wp_travel_category_trips();
			// $args['wp_travel_trips_hero_slider'] = WTWE_Helper::wtwe_get_wp_travel_trips_hero_slider();
			$args['wp_travel_trips_meta'] 	  = WTWE_Helper::wtwe_get_wp_travel_trips_meta();
			// $args['wp_travel_meta_trip_rating'] = WTWE_Helper::wtwe_get_wp_travel_meta_trip_rating();
			$args['wp_travel_trip_faq'] 	  = WTWE_Helper::wtwe_get_wp_travel_trip_faq();
			$args['tripFacts']    			  = WTWE_Helper::wtwe_get_wp_travel_elementor_trips_facts_content();
			$args['tripMap']       			  = WTWE_Helper::wtwe_get_wp_travel_elementor_map();
			$args['relatedTrips']  			  = WTWE_Helper::wtwe_get_wp_travel_elementor_related_trips();
			$args['time_format']       	  = get_option( 'time_format' );
			return $args;
		}
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0.0
	 */
	add_action( 'plugins_loaded', function() {
		if ( ! defined( 'WP_TRAVEL_VERSION' ) || version_compare(WP_TRAVEL_VERSION, WTWE_MINIMUM_WPTRAVEL_VERSION, '<') ) {
			$dependencies[] = '<a class="thickbox open-plugin-details-modal" href="' . admin_url( 'plugin-install.php' ) . '?tab=plugin-information&plugin=wp-travel&TB_iframe=true&width=640&height=500" target="__blank">WP Travel</a>';
		}

		if ( ! class_exists( '\Elementor\Plugin' ) || version_compare(ELEMENTOR_VERSION, WTWE_MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			$dependencies[] = '<a class="thickbox open-plugin-details-modal" href="' . admin_url( 'plugin-install.php' ) . '?tab=plugin-information&plugin=elementor&TB_iframe=true&width=640&height=500" target="__blank">Elementor</a>';
		}

		if ( ! empty( $dependencies ) ) {
			add_action( 'admin_notices', function() use ( $dependencies ) {
				echo wp_kses_post(
					sprintf(
						'<div class="error"><p><strong>WT Widgets for Elementor</strong>'
						. __( ' requires the latest version of ',  'wt-widgets-elementor'  )
						.implode( ' and ', $dependencies )

						. __( ' plugin to work.',  'wt-widgets-elementor' )
						. '</p></div>'
					)
				);
			});
			
			return;
		}

		/**
		 * Main instance of WTWidgets
		 *
		 * Returns the main instance of WTWidgets to prevent the need to use globals.
		 *
		 * @since  1.0
		 * @return WTWidgets
		 */
		WTWidgets::instance();
	});
}
