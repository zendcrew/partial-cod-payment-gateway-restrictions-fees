<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Cart_Item_Subtotals' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Cart_Item_Subtotals {

        public static function init() {

            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 80, 2 );

            add_filter( 'paygeo-admin/get-cart_item_subtotals-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'cart_item_subtotals' ] = esc_html__( 'Cart Item Subtotals', 'pgeo-paygeo' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_20' ] = esc_html__( 'Products Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_21' ] = esc_html__( 'Variations Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_22' ] = esc_html__( 'Categories Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_23' ] = esc_html__( 'Tags Subtotal (Premium)', 'pgeo-paygeo' );            
            $in_list[ 'prem_24' ] = esc_html__( 'Attributes Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_25' ] = esc_html__( 'Virtual Products Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_26' ] = esc_html__( 'Tax Classes Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_27' ] = esc_html__( 'Shipping Classes Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_28' ] = esc_html__( 'Product Meta Fields Subtotal (Premium)', 'pgeo-paygeo' );

            return $in_list;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Cart_Item_Subtotals::init();
}