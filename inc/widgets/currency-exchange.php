<?php

/**
 * Currency Exchange class.
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
use Elementor\Controls_Manager;
use WTWE\Helper\WTWE_Helper;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || exit;

/**
 * Trip Tabs widget class.
 *
 * @since 1.0.0
 */
if (!class_exists('WTWE_Currency_Exchange')) {
    class WTWE_Currency_Exchange extends Widget_Base
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
            wp_register_style('currency-converter-style', plugins_url('assets/css/currency-converter' . $prefixed . '.css', WTWE_PLUGIN_FILE), []);
            wp_register_script('currency-converter-script', plugins_url('assets/js/currency-converter' . $prefixed . '.js', WTWE_PLUGIN_FILE), ['jquery']);

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
            return 'wp-travel-currency-exchange';
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
            return esc_html__('Currency Exchange', 'wt-widgets-elementor');
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
            return 'eicon-money';
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
         * Enqueue styles.
         */
        public function get_style_depends()
        {
            return array('currency-converter-style');
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
            return array('currency-converter-script','jquery');
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
        protected function register_controls()
        {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Content', 'wt-widgets-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'base_currency',
                [
                    'label' => __('Base Currency', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'USD',
                ]
            );

            $this->add_control(
                'target_currency',
                [
                    'label' => __('Target Currency', 'wt-widgets-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'EUR',
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
            $settings = wptravel_get_settings();
            $settings = $this->get_settings_for_display();
            $base_currency = esc_attr($settings['base_currency']);
            $target_currency = esc_attr($settings['target_currency']);
            ?>
            <div class="currency-converter-widget">
                <input type="number" id="amount" placeholder="<?php echo 'Convert ' . $base_currency . ' to ' . $target_currency; ?>" />
                <button id="convert">Convert</button>
                <p id="conversion-result"></p>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    var baseCurrency = '<?php echo $base_currency; ?>';
                    var targetCurrency = '<?php echo $target_currency; ?>';
                    // var apiKey = 'c8c0a07b425ce85b03527c88991cad98';
                    var apiKey = '201f7f42c62e0acef2f6ae198c67dc45';
        
                    $('#convert').click(function() {
                        var amount = $('#amount').val();
        
                        if (amount && baseCurrency && targetCurrency) {
                            $.ajax({
                                url: `http://data.fixer.io/api/latest?access_key=${apiKey}&symbols=${targetCurrency}`,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if (data.success) {
                                        var rate = data.rates[targetCurrency];
                                        var result = (amount * rate).toFixed(2);
                                        $('#conversion-result').text(`${amount} ${baseCurrency} = ${result} ${targetCurrency}`);
                                    } else {
                                        $('#conversion-result').text('Error: ' + data.error.info);
                                    }
                                },
                                error: function() {
                                    $('#conversion-result').text('Error fetching conversion rates.');
                                }
                            });
                        } else {
                            $('#conversion-result').text('Please enter an amount and select currencies.');
                        }
                    });
                });
            </script>
            <?php
        }
        
       
    }
}
