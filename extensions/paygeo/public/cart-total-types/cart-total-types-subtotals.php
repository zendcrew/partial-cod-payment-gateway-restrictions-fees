<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Cart_Total_Types_Subtotals' ) ) {

    class PGEO_PayGeo_Cart_Total_Types_Subtotals {

        public function calc_totals( $totals, $option, $cart_data ) {

            $subtotals = $this->get_subtotals( $cart_data );

            if ( 'subtotal' == $option ) {
                $totals += $subtotals[ 'subtotal' ];
            }
            if ( 'subtotal_tax' == $option ) {
                $totals += $subtotals[ 'subtotal_tax' ];
            }

            return $totals;
        }

        private function get_subtotals( $cart_data ) {

            $subtotals = array(
                'subtotal' => 0,
                'subtotal_tax' => 0
            );

            if ( !isset( $cart_data[ 'wc' ][ 'totals' ][ 'subtotal' ] ) ) {
                return $subtotals;
            }

            $subtotals[ 'subtotal' ] = $cart_data[ 'wc' ][ 'totals' ][ 'subtotal' ];
            $subtotals[ 'subtotal_tax' ] = $cart_data[ 'wc' ][ 'totals' ][ 'subtotal_tax' ];

            return $subtotals;
        }

    }

}