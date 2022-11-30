<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Polylang_Cart_Discounts' ) ) {

    class WOOPCD_PartialCOD_Polylang_Cart_Discounts {

        public function __construct() {

            add_filter( 'woopcd_partialcod/discount-title', array( $this, 'tranlate_title' ), 10, 3 );
            add_filter( 'woopcd_partialcod/discount-desc', array( $this, 'tranlate_desc' ), 10, 3 );

            add_filter( 'woopcd_partialcod/discount-coupon-title', array( $this, 'tranlate_coupon_title' ), 10, 4 );
            add_filter( 'woopcd_partialcod/discount-coupon-desc', array( $this, 'tranlate_coupon_desc' ), 10, 4 );
        }

        public function tranlate_title( $title, $discount_key, $discount ) {

            if ( !isset( $discount[ 'method_id' ] ) ) {

                return $title;
            }

            $method_id = $discount[ 'method_id' ];

            $prefix = $this->get_prefix( $method_id, $discount_key );

            $string_id = $prefix . 'title';

            return WOOPCD_PartialCOD_Polylang::get_translated_string( $title, $string_id );
        }

        public function tranlate_desc( $desc, $discount_key, $discount ) {

            if ( !isset( $discount[ 'method_id' ] ) ) {

                return $desc;
            }

            $method_id = $discount[ 'method_id' ];

            $prefix = $this->get_prefix( $method_id, $discount_key );

            $string_id = $prefix . 'desc';

            return WOOPCD_PartialCOD_Polylang::get_translated_string( $desc, $string_id );
        }

        public function tranlate_coupon_title( $title, $coupon_code, $discount_key, $discount ) {

            if ( !isset( $discount[ 'method_id' ] ) ) {

                return $title;
            }

            $method_id = $discount[ 'method_id' ];

            $prefix = $this->get_prefix( $method_id, $discount_key );

            $string_id = $prefix . 'title';

            return WOOPCD_PartialCOD_Polylang::get_translated_string( $title, $string_id );
        }

        public function tranlate_coupon_desc( $desc, $coupon_code, $discount_key, $discount ) {

            if ( !isset( $discount[ 'method_id' ] ) ) {

                return $desc;
            }

            $method_id = $discount[ 'method_id' ];

            $prefix = $this->get_prefix( $method_id, $discount_key );

            $string_id = $prefix . 'desc';

            return WOOPCD_PartialCOD_Polylang::get_translated_string( $desc, $string_id );
        }

        public function get_strings( $options, $method_id ) {

            $option_strings = array();

            $method_key = $method_id . '_discount_rules';

            if ( !isset( $options[ $method_key ] ) ) {

                return $option_strings;
            }

            foreach ( $options[ $method_key ] as $partial_cod_options ) {

                $rule_id = $partial_cod_options[ 'rule_id' ];

                $prefix = $this->get_prefix( $method_id, $rule_id );

                $option_strings[ $prefix . 'title' ] = array(
                    'value' => $partial_cod_options[ 'title' ],
                    'is_multiline' => true
                );

                $option_strings[ $prefix . 'desc' ] = array(
                    'value' => $partial_cod_options[ 'desc' ],
                    'is_multiline' => true
                );
            }

            return $option_strings;
        }

        private function get_prefix( $method_id, $rule_id ) {

            return 'woopcd_' . $method_id . '_cart_discounts_' . $rule_id . '_';
        }

    }

}