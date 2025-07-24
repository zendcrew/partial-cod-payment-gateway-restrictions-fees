<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Polylang' ) && class_exists( 'Polylang' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    require_once 'polylang-options/polylang-options.php';
    require_once 'polylang-cart-fees.php';
    require_once 'polylang-cart-discounts.php';

    class WOOPCD_PartialCOD_Polylang {

        private $method_options;
        private $cart_fees;
        private $cart_discounts;

        public function __construct() {

            $option_name = WOOPCD_PARTIALCOD_OPTION_NAME;

            $this->method_options = new WOOPCD_PartialCOD_Polylang_Method_Options();
            $this->cart_fees = new WOOPCD_PartialCOD_Polylang_Cart_Fees();
            $this->cart_discounts = new WOOPCD_PartialCOD_Polylang_Cart_Discounts();

            add_filter( 'reon/process-save-options-' . $option_name, array( $this, 'register_translations' ), 10 );
        }

        public function register_translations( $options ) {

            foreach ( WOOPCD_PartialCOD_Admin_Page::get_all_payment_method_ids() as $method_id ) {

                // Register method options strings
                foreach ( $this->method_options->get_strings( $options, $method_id ) as $string_id => $string_data ) {

                    $this->register_string( $string_id, $string_data[ 'value' ], $string_data[ 'is_multiline' ] );
                }

                // Register cart fees strings
                foreach ( $this->cart_fees->get_strings( $options, $method_id ) as $string_id => $string_data ) {

                    $this->register_string( $string_id, $string_data[ 'value' ], $string_data[ 'is_multiline' ] );
                }

                // Register cart discounts strings
                foreach ( $this->cart_discounts->get_strings( $options, $method_id ) as $string_id => $string_data ) {

                    $this->register_string( $string_id, $string_data[ 'value' ], $string_data[ 'is_multiline' ] );
                }
            }

            return $options;
        }

        public static function get_translated_string( $string_value, $string_id ) {

            if ( function_exists( 'pll__' ) ) {

                return pll__( $string_value );
            } else if ( function_exists( 'icl_t' ) ) {

                return icl_t( 'partial-cod-payment-gateway-restrictions-fees', $string_id, $string_value );
            }

            return $string_value;
        }

        private function register_string( $string_id, $string_value, $is_multiline = true ) {

            if ( function_exists( 'icl_register_string' ) ) {

                icl_register_string( 'partial-cod-payment-gateway-restrictions-fees', $string_id, $string_value );
            } else if ( function_exists( 'pll_register_string' ) ) {

                pll_register_string( $string_id, $string_value, 'partial-cod-payment-gateway-restrictions-fees', $is_multiline );
            }
        }

    }

    new WOOPCD_PartialCOD_Polylang();
}