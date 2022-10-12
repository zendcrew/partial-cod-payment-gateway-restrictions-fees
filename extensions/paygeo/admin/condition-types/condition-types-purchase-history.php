<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Purchase_History' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Purchase_History {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 130, 2 );

            add_filter( 'paygeo-admin/get-purchase_history-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'purchase_history' ] = esc_html__( 'Purchase History', 'zcpg-woo-paygeo' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
           
            $in_list[ 'prem_58' ] = esc_html__( 'Purchased Products (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_59' ] = esc_html__( 'Purchased Variations (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_60' ] = esc_html__( 'Purchased Categories (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_61' ] = esc_html__( 'Purchased Tags (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_62' ] = esc_html__( 'Purchased Attributes (Premium)', 'zcpg-woo-paygeo' );
            
            return $in_list;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Purchase_History::init();
}
