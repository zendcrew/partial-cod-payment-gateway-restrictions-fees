<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_DateTime' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_DateTime {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 120, 2 );

            add_filter( 'woopcd_partialcod-admin/get-datetime-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'datetime' ] = esc_html__( 'Date &amp; Time', 'woopcd-partialcod' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_52' ] = esc_html__( 'Date (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_53' ] = esc_html__( 'Time (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_54' ] = esc_html__( 'Date &amp; Time (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_55' ] = esc_html__( 'Days Of Week (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_56' ] = esc_html__( 'Days Of Month (Premium)', 'woopcd-partialcod' );
            $in_list[ 'prem_57' ] = esc_html__( 'Months Of Year (Premium)', 'woopcd-partialcod' );

            return $in_list;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_DateTime::init();
}