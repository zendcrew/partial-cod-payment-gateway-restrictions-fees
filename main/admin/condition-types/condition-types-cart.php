<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_Cart' )&& !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_Cart {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 50, 2 );
            
            add_filter('woopcd_partialcod-admin/get-cart-group-conditions', array(new self(), 'get_conditions'), 10, 2);
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'cart' ] = esc_html__( 'Cart', 'woopcd-partialcod' );
            return $in_groups;
        }

        public static function get_conditions($in_list, $args) {
            $in_list[ 'prem_6' ] = esc_html__( 'Cart Total Quantity (Premium)', 'woopcd-partialcod' );

            $weight_text = str_replace( '[0]', get_option( 'woocommerce_weight_unit' ), esc_html__( 'Cart Total Weight ([0])  (Premium)', 'woopcd-partialcod' ) );
            $in_list[ 'prem_7' ] = $weight_text;

            $in_list[ 'prem_8' ] = esc_html__( 'Number Of Cart Items (Premium)', 'woopcd-partialcod' );


            $in_list[ 'prem_9' ] = esc_html__( 'Applied Coupons (Premium)', 'woopcd-partialcod' );

            if ( WOOPCD_PartialCOD_Main::is_risky_method( $args[ 'method_id' ] ) && 'method-options' != $args[ 'module' ] ) {
                $in_list[ 'prem_10' ] = esc_html__( 'Partial Payment Method (Premium)', 'woopcd-partialcod' );
            }
            
            return $in_list;
        }
        
        
        
    }

    WOOPCD_PartialCOD_Admin_Conditions_Cart::init();
}