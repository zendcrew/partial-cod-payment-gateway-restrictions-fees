<?php

/*
 * Plugin Name: WooCommerce Partial COD - Payment Gateway Restrictions & Fees
 * Plugin URI: https://codecanyon.net/item/woocommerce-partial-cod-payment-gateway-restrictions-fees/41741012?ref=zendcrew
 * Description: A powerful, flexible and easy-to-use WooCommerce extention that can be used to manage payment availability and other gateway options based on product rules and conditions.
 * Version: 1.1
 * Author: zendcrew
 * Author URI: https://codecanyon.net/user/zendcrew?ref=zendcrew
 * Text Domain: woopcd-partialcod
 * Domain Path: /languages/
 * Requires at least: 5.8
 * Tested up to: 6.1
 * Requires PHP: 5.6
 * 
 * WC requires at least: 5.6
 * WC tested up to: 7.1
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( is_admin() ) {

    require_once (dirname( __FILE__ ) . '/framework/reon_loader.php');
}

if ( !defined( 'WOOPCD_PARTIALCOD_VERSION' ) ) {

    define( 'WOOPCD_PARTIALCOD_VERSION', '1.1' );
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

            load_plugin_textdomain( 'woopcd-partialcod', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        public function plugin_loaded() {

            if ( function_exists( 'WC' ) ) { // Check if WooCommerce is active
                $this->init();
            } else {

                add_action( 'admin_notices', array( $this, 'missing_notice' ) );
            }
        }

        public function missing_notice() {

            echo '<div class="error"><p><strong>' . esc_html__( 'WooCommerce Partial COD - Payment Gateway Restrictions & Fees to be installed and activated.', 'woopcd-partialcod' ) . '</strong></p></div>';
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
