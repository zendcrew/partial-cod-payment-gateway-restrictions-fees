<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_WPML_Cart_Fees' ) ) {

    class PGEO_PayGeo_WPML_Cart_Fees {

        public function __construct() {

            add_filter( 'paygeo/fee-title', array( $this, 'tranlate_title' ), 10, 3 );
            add_filter( 'paygeo/fee-desc', array( $this, 'tranlate_desc' ), 10, 3 );
        }

        public function tranlate_title( $title, $fee_key, $fee ) {

            if ( !isset( $fee[ 'method_id' ] ) ) {

                return $title;
            }

            $method_id = $fee[ 'method_id' ];

            $prefix = $this->get_prefix( $method_id, $fee_key );

            $string_id = $prefix . 'title';

            return PGEO_PayGeo_WPML::get_translated_string( $title, $string_id );
        }

        public function tranlate_desc( $desc, $fee_key, $fee ) {

            if ( !isset( $fee[ 'method_id' ] ) ) {

                return $desc;
            }

            $method_id = $fee[ 'method_id' ];

            $prefix = $this->get_prefix( $method_id, $fee_key );

            $string_id = $prefix . 'desc';

            return PGEO_PayGeo_WPML::get_translated_string( $desc, $string_id );
        }

        public function get_strings( $options, $method_id ) {

            $option_strings = array();

            $method_key = $method_id . '_fee_rules';

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

            return 'pgeo_' . $method_id . '_cart_fees_' . $rule_id . '_';
        }

    }

}