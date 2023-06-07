<?php

if ( !defined( 'ABSPATH' ) ) {

    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Cart_Session' ) ) {

    class WOOPCD_PartialCOD_Cart_Session {

        private $payment_method_id = '';
        private static $instance = null;

        public static function get_instance(): self {

            if ( null == self::$instance ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        private function __construct() {

            add_action( 'woocommerce_load_cart_from_session', array( $this, 'load_cart_from_session' ) );
            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'loaded_cart_from_session' ) );
            add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ), 1 );
        }

        public function init() {

            if ( isset( $_POST[ 'payment_method' ] ) ) {

                $this->payment_method_id = sanitize_key( $_POST[ 'payment_method' ] );
            }
        }

        public function load_cart_from_session() {

            if ( !empty( $this->payment_method_id ) ) {

                return;
            }

            $this->payment_method_id = WC()->session->get( 'chosen_payment_method', '' );
        }

        public function loaded_cart_from_session( $cart ) {

            if ( !empty( $this->payment_method_id ) ) {

                return;
            }

            $this->payment_method_id = WC()->session->get( 'chosen_payment_method', '' );
        }

        public function before_calculate_totals( $cart ) {

            if ( !empty( $this->payment_method_id ) ) {

                return;
            }

            $this->payment_method_id = WC()->session->get( 'chosen_payment_method', '' );
        }

        public function get_payment_method_id() {

            if ( !empty( $this->payment_method_id ) ) {

                return $this->payment_method_id;
            }

            return WC()->session->get( 'chosen_payment_method', '' );
        }

    }

    WOOPCD_PartialCOD_Cart_Session::get_instance()->init();
}
