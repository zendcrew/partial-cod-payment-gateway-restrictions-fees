<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Customer_Util' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Customer_Util {

        public static function get_user_roles() {
            return wp_get_current_user()->roles;
        }

    }

}