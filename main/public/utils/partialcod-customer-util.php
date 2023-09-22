<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Customer_Util' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Customer_Util {

        private static $users = array();

        public static function get_user_roles( $cart_data ) {

            $user = self::get_user_by( $cart_data );

            if ( !$user ) {
                return array();
            }

            return $user->roles;
        }

        private static function get_user_by( $cart_data ) {

            if ( !isset( $cart_data[ 'wc' ][ 'customer' ][ 'id' ] ) ) {
                return false;
            }

            $user_id = $cart_data[ 'wc' ][ 'customer' ][ 'id' ];

            if ( 0 >= $user_id ) {
                return false;
            }

            if ( isset( self::$users[ $user_id ] ) ) {

                return self::$users[ $user_id ];
            }

            $user = get_user_by( 'id', $user_id );

            if ( $user ) {
                self::$users[ $user_id ] = $user;
            }

            return $user;
        }

    }

}