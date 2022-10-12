<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_DateTime' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_DateTime {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 120, 2 );

            add_filter( 'paygeo-admin/get-datetime-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'datetime' ] = esc_html__( 'Date &amp; Time', 'zcpg-woo-paygeo' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_52' ] = esc_html__( 'Date (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_53' ] = esc_html__( 'Time (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_54' ] = esc_html__( 'Date &amp; Time (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_55' ] = esc_html__( 'Days Of Week (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_56' ] = esc_html__( 'Days Of Month (Premium)', 'zcpg-woo-paygeo' );
            $in_list[ 'prem_57' ] = esc_html__( 'Months Of Year (Premium)', 'zcpg-woo-paygeo' );

            return $in_list;
        }

    }

    PGEO_PayGeo_Admin_Conditions_DateTime::init();
}