<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_WPML' ) && function_exists( 'SitePress' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    require_once 'wpml-options/wpml-options.php';
    require_once 'wpml-cart-fees.php';
    require_once 'wpml-cart-discounts.php';

    class WOOPCD_PartialCOD_WPML {

        private $method_options;
        private $cart_fees;
        private $cart_discounts;

        public function __construct() {

            $option_name = WOOPCD_PARTIALCOD_OPTION_NAME;

            $this->method_options = new WOOPCD_PartialCOD_WPML_Method_Options();
            $this->cart_fees = new WOOPCD_PartialCOD_WPML_Cart_Fees();
            $this->cart_discounts = new WOOPCD_PartialCOD_WPML_Cart_Discounts();

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

        public function get_translated_string( $string_value, $string_id ) {

            if ( function_exists( 'icl_t' ) ) {

                return icl_t( 'partial-cod-payment-gateway-restrictions-fees', $string_id, $string_value );
            }

            return $string_value;
        }

        private function register_string( $string_id, $string_value ) {

            if ( function_exists( 'icl_register_string' ) ) {

                icl_register_string( 'partial-cod-payment-gateway-restrictions-fees', $string_id, $string_value );
            }
        }

    }

}
