<?php

/*
 * Plugin Name: PCOD – Partial COD, Payment Gateway Restrictions & Fees | for WooCommerce
 * Plugin URI: https://codecanyon.net/item/woocommerce-partial-cod-payment-gateway-restrictions-fees/41741012?ref=zendcrew
 * Description: A powerful, flexible and easy-to-use WooCommerce extention that can be used to manage payment availability and other gateway options based on product rules and conditions.
 * Version: 1.4
 * Author: zendcrew
 * Author URI: https://codecanyon.net/user/zendcrew?ref=zendcrew
 * Text Domain: partial-cod-payment-gateway-restrictions-fees
 * Domain Path: /languages/
 * Requires at least: 5.8
 * Requires PHP: 5.6
 * WC requires at least: 5.6
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * Tested up to: 6.8
 * WC tested up to: 9.9
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( is_admin() ) {

    require_once (dirname( __FILE__ ) . '/framework/reon_loader.php');
}

if ( !defined( 'WOOPCD_PARTIALCOD_VERSION' ) ) {

    define( 'WOOPCD_PARTIALCOD_VERSION', '1.4' );
}

if ( !defined( 'WOOPCD_PARTIALCOD_FILE' ) ) {

    define( 'WOOPCD_PARTIALCOD_FILE', __FILE__ );
}

if ( !defined( 'WOOPCD_PARTIALCOD_OPTION_NAME' ) ) {

    define( 'WOOPCD_PARTIALCOD_OPTION_NAME', 'woopcd_partialcod' );
}

if ( !defined( 'WOOPCD_PARTIALCOD_HOOKS_INDEX' ) ) {

    define( 'WOOPCD_PARTIALCOD_HOOKS_INDEX', 99999 );
}

if ( !class_exists( 'WOOPCD_PartialCOD_Init' ) ) {

    class WOOPCD_PartialCOD_Init {

        public function __construct() {

            add_action( 'plugins_loaded', array( $this, 'plugin_loaded' ), 1 );

            add_action( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ) );

            add_action( 'init', array( $this, 'load_textdomain' ) );
        }

        public function load_textdomain() {

            load_plugin_textdomain( 'partial-cod-payment-gateway-restrictions-fees', false, dirname( plugin_basename( WOOPCD_PARTIALCOD_FILE ) ) . '/languages/' );
        }

        public function plugin_loaded() {

            if ( function_exists( 'WC' ) ) { // Check if WooCommerce is active
                $this->init();
            } else {

                add_action( 'admin_notices', array( $this, 'missing_notice' ) );
            }
        }

        public function before_woocommerce_init() {

            // Check for HPOS
            if ( !class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {

                return;
            }

            // Adds support for HPOS
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOOPCD_PARTIALCOD_FILE, true );
        }

        public function missing_notice() {

            echo '<div class="error"><p><strong>' . esc_html__( 'PCOD – Partial COD, Payment Gateway Restrictions & Fees | for WooCommerce requires WooCommerce to be installed and activated.', 'partial-cod-payment-gateway-restrictions-fees' ) . '</strong></p></div>';
        }

        private function init() {

            //Partial COD Main
            if ( !class_exists( 'WOOPCD_PartialCOD_Main' ) ) {

                include_once ('main/main.php');

                WOOPCD_PartialCOD_Main::init();
            }
        }

    }

    new WOOPCD_PartialCOD_Init();
}
