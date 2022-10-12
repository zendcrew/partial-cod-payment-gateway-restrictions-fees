<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Cart' )&& !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Cart {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 50, 2 );
            
            add_filter('paygeo-admin/get-cart-group-conditions', array(new self(), 'get_conditions'), 10, 2);
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'cart' ] = esc_html__( 'Cart', 'zcpg-woo-paygeo' );
            return $in_groups;
        }

        public static function get_conditions($in_list, $args) {
            $in_list[ 'prem_6' ] = esc_html__( 'Cart Total Quantity (Premium)', 'zcpg-woo-paygeo' );

            $weight_text = str_replace( '[0]', get_option( 'woocommerce_weight_unit' ), esc_html__( 'Cart Total Weight ([0])  (Premium)', 'zcpg-woo-paygeo' ) );
            $in_list[ 'prem_7' ] = $weight_text;

            $in_list[ 'prem_8' ] = esc_html__( 'Number Of Cart Items (Premium)', 'zcpg-woo-paygeo' );


            $in_list[ 'prem_9' ] = esc_html__( 'Applied Coupons (Premium)', 'zcpg-woo-paygeo' );

            if ( PGEO_PayGeo_Extension::is_risky_method( $args[ 'method_id' ] ) && 'method-options' != $args[ 'module' ] ) {
                $in_list[ 'prem_10' ] = esc_html__( 'Partial Payment Method (Premium)', 'zcpg-woo-paygeo' );
            }
            
            return $in_list;
        }
        
        
        
    }

    PGEO_PayGeo_Admin_Conditions_Cart::init();
}