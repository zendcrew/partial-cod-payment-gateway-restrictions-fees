<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_GeoLocation' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_GeoLocation {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 10, 2 );

            add_filter( 'woopcd_partialcod-admin/get-geo_locations-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'geo_locations' ] = esc_html__( 'GeoIP Locations', 'partial-cod-payment-gateway-restrictions-fees' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
            $in_list[ 'prem_1' ] = esc_html__( 'GeoIP Continents (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'prem_2' ] = esc_html__( 'GeoIP Countries (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            
            return $in_list;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_GeoLocation::init();
}