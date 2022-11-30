<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_Cart_Item_Quantity' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_Cart_Item_Quantity {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 90, 2 );

            add_filter( 'woopcd_partialcod-admin/get-cart_item_quantity-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'cart_item_quantity' ] = esc_html__( 'Cart Item Quantity', 'woopcd-partialcod' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_29' ] = esc_html__( 'Products Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_30' ] = esc_html__( 'Variations Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_31' ] = esc_html__( 'Categories Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_32' ] = esc_html__( 'Tags Quantity (Premium)', 'woopcd-partialcod' );            
            $in_list[ 'prem_33' ] = esc_html__( 'Attributes Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_34' ] = esc_html__( 'Virtual Products Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_35' ] = esc_html__( 'Tax Classes Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_36' ] = esc_html__( 'Shipping Classes Quantity (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_37' ] = esc_html__( 'Product Meta Fields Quantity (Premium)', 'woopcd-partialcod' );

            return $in_list;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_Cart_Item_Quantity::init();
}