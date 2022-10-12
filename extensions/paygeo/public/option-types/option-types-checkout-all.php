<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Option_Types_Checkout_All' ) ) {

    class PGEO_PayGeo_Option_Types_Checkout_All {

        public function __construct() {
            add_filter( 'paygeo/get-all-options', array( $this, 'get_option' ), 1, 3 );
            add_filter( 'paygeo/apply-all-method-option', array( $this, 'apply_option' ), 1, 2 );
            add_filter( 'paygeo/get-all-messages', array( $this, 'get_messages' ), 1, 3 );
        }

        public function get_option( $option, $option_args, $cart_data ) {


            $option[ 'enabled' ] = $option_args[ 'enabled' ];

            if ( isset( $option_args[ 'title' ] ) ) {
                $option[ 'title' ] = $option_args[ 'title' ];
            }

            if ( isset( $option_args[ 'desc' ] ) ) {
                $option[ 'desc' ] = $option_args[ 'desc' ];
            }

            if ( isset( $option_args[ 'message' ] ) ) {
                $option[ 'message' ] = $option_args[ 'message' ];
            }

            return $option;
        }

        public function apply_option( $method, $option ) {

            $method->enabled = $option[ 'enabled' ];

            if ( isset( $option[ 'title' ] ) ) {
                $method->title = $option[ 'title' ];
            }

            if ( isset( $option[ 'desc' ] ) ) {
                $method->description = $option[ 'desc' ];
            }

            return $method;
        }

        public function get_messages( $messages, $option, $option_id ) {

            if ( isset( $option[ 'message' ] ) ) {

                $messages[ $option[ 'method_id' ] ] [ 'message' ] = $option[ 'message' ];
                $messages[ $option[ 'method_id' ] ] [ 'option_id' ] = $option_id;

                return $messages;
            }

            if ( isset( $messages[ $option[ 'method_id' ] ] ) ) {

                unset( $m_messages[ $option[ 'method_id' ] ] );
            }

            return $messages;
        }

    }

    new PGEO_PayGeo_Option_Types_Checkout_All();
}