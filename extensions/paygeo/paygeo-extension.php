<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Extension' ) ) {

    class PGEO_PayGeo_Extension {

        private static $risky_method_ids = array();

        public static function init() {
            
            if ( !defined( 'PGEO_PAYGEO_ASSETS_URL' ) ) {
                
                define( 'PGEO_PAYGEO_ASSETS_URL', plugins_url( 'assets/', __FILE__ ) );
            }

            if ( is_admin() ) {
                add_action( 'reon/init', array( new self(), 'load_admin_page' ) );
                add_action( 'admin_enqueue_scripts', array( new self(), 'enqueue_admin_scripts' ), 20 );
            } else {
                add_action( 'wp_enqueue_scripts', array( new self(), 'enqueue_public_scripts' ), 20 );
            }

            add_action( 'init', array( new self(), 'init_public' ) );
            
            require_once dirname( __FILE__ ) . '/compatibility/compatibility.php';
        }

        public static function load_admin_page() {
            
            require_once dirname( __FILE__ ) . '/admin/admin.php';
        }

        public static function init_public() {
            
            require_once dirname( __FILE__ ) . '/public/paygeo.php';
        }

        public static function required_paths( $dir, $ingore_list = array(), $subdirs = array() ) {

            if ( $dir_handle = opendir( $dir ) ) {
                while ( false !== ($file_path = readdir( $dir_handle )) ) {

                    if ( in_array( $file_path, $subdirs ) ) {
                        self::required_paths( $dir . '/' . $file_path, $ingore_list, $subdirs );
                    } else {
                        $explode_entry = explode( '.', $file_path );
                        if ( isset( $explode_entry[ 1 ] ) && $explode_entry[ 1 ] == 'php' && !in_array( $file_path, $ingore_list ) ) {
                            require_once $dir . '/' . $file_path;
                        }
                    }
                }
                closedir( $dir_handle );
            }
        }

        public static function enqueue_admin_scripts() {

            wp_enqueue_style( 'pgeo_paygeo_admin_css', PGEO_PAYGEO_ASSETS_URL . 'admin-styles.css', array(), '1.0', 'all' );
            
            PGEO_PayGeo_Admin_Notices::get_instance()->enqueue_scripts();
        }

        public static function enqueue_public_scripts() {

            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'tipTip', PGEO_PAYGEO_ASSETS_URL . 'tipTip.min.css', array(), '1.0', 'all' );
            wp_enqueue_style( 'paygeo_public_css', PGEO_PAYGEO_ASSETS_URL . 'public-styles.min.css', array(), '1.0', 'all' );

            wp_enqueue_script( 'jquery-tipTip', PGEO_PAYGEO_ASSETS_URL . 'jquery.tipTip.min.js', array( 'jquery' ), '1.0', true );
            wp_enqueue_script( 'paygeo_public_scripts_js', PGEO_PAYGEO_ASSETS_URL . 'public-scripts.min.js', array( 'jquery' ), '1.0', true );

            $script_params = PGEO_PayGeo::get_scrip_params();

            wp_localize_script( 'paygeo_public_scripts_js', 'pgeo_paygeo', $script_params );

            //Custom CSS
            $custom_css = PGEO_PayGeo::get_option( 'custom_css', '' );
            if ( $custom_css != '' ) {
                wp_add_inline_style( 'paygeo_public_css', $custom_css );
            }
        }

        public static function get_shipping_rates( $args ) {

            $data_list_args = array(
                'source' => 'wc:zones_shipping',
                'ajax' => false
            );

            $shipping_rates = ReonApi::get_data_list( $data_list_args );

            return apply_filters( 'paygeo-admin/get-shipping-rates', $shipping_rates, $args );
        }

        public static function is_risky_method( $method_id ) {
            foreach ( self::get_risky_method_ids() as $id ) {
                if ( $method_id == $id ) {
                    return true;
                }
            }
            return false;
        }

        private static function get_risky_method_ids() {
            if ( count( self::$risky_method_ids ) > 0 ) {
                return self::$risky_method_ids;
            }
            self::$risky_method_ids = array(
                'cod',
            );
            return apply_filters( 'paygeo/risk-free/get-method-ids', self::$risky_method_ids );
        }

    }

}