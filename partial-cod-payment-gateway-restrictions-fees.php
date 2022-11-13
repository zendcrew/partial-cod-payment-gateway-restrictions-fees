<?php

/*
 * Plugin Name: Paygeo - WooCommerce Partial COD - Payment Gateway Restrictions & Fees
 * Plugin URI: https://codecanyon.net/user/zendcrew/portfolio
 * Description: A powerful, flexible and easy-to-use WooCommerce extention that can be used to manage payment availability and other gateway options based on product rules and conditions.
 * Version: 1.0
 * Author: zendcrew
 * Author URI: https://codecanyon.net/user/zendcrew
 * Text Domain: pgeo-paygeo
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

if ( !defined( 'PGEO_PAYGEO_VERSION' ) ) {
    
    define( 'PGEO_PAYGEO_VERSION', '1.0' );
}

if ( !defined( 'PGEO_PAYGEO_FILE' ) ) {

    define( 'PGEO_PAYGEO_FILE', __FILE__ );
}

if ( !defined( 'PGEO_PAYGEO_OPTION_NAME' ) ) {
    
    define( 'PGEO_PAYGEO_OPTION_NAME', 'pgeo_paygeo' );
}

if ( !defined( 'PGEO_PAYGEO_HOOKS_INDEX' ) ) {
    
    define( 'PGEO_PAYGEO_HOOKS_INDEX', 99999 );
}

if ( !class_exists( 'PGEO_PayGeo_Main' ) ) {

    class PGEO_PayGeo_Main {

        public function __construct() {

            add_action( 'plugins_loaded', array( $this, 'init' ), 1 );

            load_plugin_textdomain( 'pgeo-paygeo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        public function init() {

            if ( function_exists( 'WC' ) ) { // Check if WooCommerce is active
                
                require_once 'extensions/extensions.php';
            } else {

                add_action( 'admin_notices', array( $this, 'missing_notice' ) );
            }
        }

        public function missing_notice() {

            echo '<div class="error"><p><strong>' . esc_html__( 'WooCommerce Partial COD - Payment Gateway Restrictions & Fees to be installed and activated.', 'pgeo-paygeo' ) . '</strong></p></div>';
        }

    }

    new PGEO_PayGeo_Main();
}
