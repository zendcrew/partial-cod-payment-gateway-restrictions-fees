<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_Purchase_History' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_Purchase_History {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 130, 2 );

            add_filter( 'woopcd_partialcod-admin/get-purchase_history-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'purchase_history' ] = esc_html__( 'Purchase History', 'partial-cod-payment-gateway-restrictions-fees' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
           
            $in_list[ 'prem_58' ] = esc_html__( 'Purchased Products (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'prem_59' ] = esc_html__( 'Purchased Variations (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'prem_60' ] = esc_html__( 'Purchased Categories (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'prem_61' ] = esc_html__( 'Purchased Tags (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'prem_62' ] = esc_html__( 'Purchased Attributes (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            
            return $in_list;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_Purchase_History::init();
}
