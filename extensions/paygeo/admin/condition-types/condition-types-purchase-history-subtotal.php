<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Purchase_History_Subtotals' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Purchase_History_Subtotals {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 140, 2 );

            add_filter( 'paygeo-admin/get-purchase_history_subtotal-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            
            $in_groups[ 'purchase_history_subtotal' ] = esc_html__( 'Purchase History Subtotal', 'pgeo-paygeo' );
            
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
           
            $in_list[ 'prem_63' ] = esc_html__( 'Purchased Products Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_64' ] = esc_html__( 'Purchased Variations Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_65' ] = esc_html__( 'Purchased Categories Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_66' ] = esc_html__( 'Purchased Tags Subtotal (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_67' ] = esc_html__( 'Purchased Attributes Subtotal (Premium)', 'pgeo-paygeo' );
            
            return $in_list;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Purchase_History_Subtotals::init();
}