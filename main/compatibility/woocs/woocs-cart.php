<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_WOOCS_Cart' ) ) {

    class WOOPCD_PartialCOD_WOOCS_Cart {

        public function __construct() {

            add_filter( 'woopcd_partialcod/get-cart-totals', array( $this, 'get_totals' ), 10, 3 );
        }

        public function get_totals( $cart_totals, $source, $cart ) {

            $converter = $this->get_converter();

            $cart_totals[ 'subtotal' ] = $converter->revert_amount( $cart_totals[ 'subtotal' ] );
            $cart_totals[ 'subtotal_tax' ] = $converter->revert_amount( $cart_totals[ 'subtotal_tax' ] );

            return $cart_totals;
        }

        private function get_converter() {

            return WOOPCD_PartialCOD_WOOCS::get_instance();
        }

    }

    new WOOPCD_PartialCOD_WOOCS_Cart();
}
