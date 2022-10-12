<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Purchase_History_Quantity' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Purchase_History_Quantity {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 150, 2 );

            add_filter( 'paygeo-admin/get-purchase_history_quantities-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            
            $in_groups[ 'purchase_history_quantities' ] = esc_html__( 'Purchase History Quantities', 'zcpg-woo-paygeo' );
            
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
           
            $in_list[ 'prem_68' ] = esc_html__( 'Purchased Products Quantity (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_69' ] = esc_html__( 'Purchased Variations Quantity (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_70' ] = esc_html__( 'Purchased Categories Quantity (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_71' ] = esc_html__( 'Purchased Tags Quantity (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_72' ] = esc_html__( 'Purchased Attributes Quantity (Premium)', 'zcpg-woo-paygeo' );
            
            return $in_list;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Purchase_History_Quantity::init();
}