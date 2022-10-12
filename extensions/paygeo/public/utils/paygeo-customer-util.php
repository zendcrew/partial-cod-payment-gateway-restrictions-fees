<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'PGEO_PayGeo_Customer_Util' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Customer_Util {

        public static function get_user_roles() {
            return wp_get_current_user()->roles;
        }

    }

}