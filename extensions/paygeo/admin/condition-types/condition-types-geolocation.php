<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_GeoLocation' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_GeoLocation {

        public static function init() {

            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 10, 2 );

            add_filter( 'paygeo-admin/get-geo_locations-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'geo_locations' ] = esc_html__( 'GeoIP Locations', 'zcpg-woo-paygeo' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
            $in_list[ 'prem_1' ] = esc_html__( 'GeoIP Continents (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_2' ] = esc_html__( 'GeoIP Countries (Premium)', 'zcpg-woo-paygeo' );
            
            return $in_list;
        }

    }

    PGEO_PayGeo_Admin_Conditions_GeoLocation::init();
}