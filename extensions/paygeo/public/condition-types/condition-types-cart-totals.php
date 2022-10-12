<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Conditions_Cart_Totals' ) ) {

    class PGEO_PayGeo_Conditions_Cart_Totals {

        public function __construct() {
            $all_cart_totals_ids = $this->get_all_cart_totals_ids();
            foreach ( $all_cart_totals_ids as $all_cart_totals_id ) {
                add_filter( 'paygeo/validate-' . 'cart_totals_' . $all_cart_totals_id . '-condition', array( $this, 'validate_cart_totals' ), 10, 2 );
            }
        }

        public function validate_cart_totals( $condition, $cart_data ) {

            $option_id = $this->get_cart_totals_id_by_condition( $condition[ 'condition_type' ] );

            if ( '' == $option_id ) {
                return false;
            }

            $args = array(
                'module' => $condition[ 'module' ],
                'option_id' => $option_id
            );

            $cart_total = PGEO_PayGeo_Cart_Total_Types::get_totals( $args, $cart_data );
        
            $rule_total = $condition[ 'totals' ];
                        
            $rule_compare = $condition[ 'compare' ];

            return PGEO_PayGeo_Validation_Util::validate_value( $rule_compare, $cart_total, $rule_total, 'no' );

        }

        private function get_cart_totals_id_by_condition( $condition_type ) {
            foreach ( $this->get_all_cart_totals_ids() as $cart_totals_id ) {
                if ( $condition_type == 'cart_totals_' . $cart_totals_id ) {
                    return $cart_totals_id;
                }
            }
            return '';
        }

        private function get_all_cart_totals_ids() {
            $cart_totals_ids = array();

            $this->get_default_options();

            foreach ( $this->get_default_options() as $option ) {
                $cart_totals_ids[] = $option[ 'option_id' ];
            }

            foreach ( $this->get_method_options( array() ) as $option ) {

                if ( !in_array( $option[ 'option_id' ], $cart_totals_ids ) ) {
                    $cart_totals_ids[] = $option[ 'option_id' ];
                }
            }

            foreach ( $this->get_riskfree_options( array() ) as $option ) {

                if ( !in_array( $option[ 'option_id' ], $cart_totals_ids ) ) {
                    $cart_totals_ids[] = $option[ 'option_id' ];
                }
            }

            foreach ( $this->get_cart_fee_options( array() ) as $option ) {

                if ( !in_array( $option[ 'option_id' ], $cart_totals_ids ) ) {
                    $cart_totals_ids[] = $option[ 'option_id' ];
                }
            }

            foreach ( $this->get_cart_discount_options( array() ) as $option ) {

                if ( !in_array( $option[ 'option_id' ], $cart_totals_ids ) ) {
                    $cart_totals_ids[] = $option[ 'option_id' ];
                }
            }



            return $cart_totals_ids;
        }

        private function get_method_options( $options ) {
            return PGEO_PayGeo::get_option( 'method_cart_totals', $options );
        }

        private function get_riskfree_options( $options ) {
            return PGEO_PayGeo::get_option( 'riskfree_cart_totals', $options );
        }

        private function get_cart_fee_options( $options ) {
            return PGEO_PayGeo::get_option( 'cart_fee_cart_totals', $options );
        }

        private function get_cart_discount_options( $options ) {
            return PGEO_PayGeo::get_option( 'cart_discount_cart_totals', $options );
        }

        private function get_default_options() {
            return array(
                array(
                    'option_id' => '2234343',
                    'include' => array( 'subtotal', 'subtotal_tax' ),
                ),
                array(
                    'option_id' => '2234344',
                    'include' => array( 'subtotal' ),
                ),
            );
        }

    }

}