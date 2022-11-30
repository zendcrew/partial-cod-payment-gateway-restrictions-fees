<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_Customer_Value' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_Customer_Value {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 110, 2 );

            add_filter( 'woopcd_partialcod-admin/get-customer_value-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'customer_value' ] = esc_html__( 'Customer Value', 'woopcd-partialcod' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
          
            $currency = get_woocommerce_currency_symbol( get_woocommerce_currency() );

            $in_list[ 'prem_43' ] = esc_html__( 'Coupons Used (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_44' ] = str_replace( '[0]', $currency, esc_html__( 'Totals Spent ([0]) (Premium)', 'woopcd-partialcod' ) );
            $in_list[ 'prem_45' ] = esc_html__( 'Last Order Date (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_46' ] = str_replace( '[0]', $currency, esc_html__( 'Last Order Amount ([0]) (Premium)', 'woopcd-partialcod' ) );

            $in_list[ 'prem_47' ] = str_replace( '[0]', $currency, esc_html__( 'Average ([0]) Per Order (Premium)', 'woopcd-partialcod' ) );
            $in_list[ 'prem_48' ] = str_replace( '[0]', $currency, esc_html__( 'Maximum ([0]) Per Order (Premium)', 'woopcd-partialcod' ) );
            $in_list[ 'prem_49' ] = str_replace( '[0]', $currency, esc_html__( 'Minimum ([0]) Per Order (Premium)', 'woopcd-partialcod' ) );

            $in_list[ 'prem_50' ] = esc_html__( 'Number Of Orders (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_51' ] = esc_html__( 'Number Of Reviews (Premium)', 'woopcd-partialcod' );

            return $in_list;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_Customer_Value::init();
}