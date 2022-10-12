<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_WPML_Method_Options' ) ) {

    require_once 'wpml-options-all.php';

    class PGEO_PayGeo_WPML_Method_Options {

        public function __construct() {

            add_filter( 'paygeo/method-option', array( $this, 'translate_option' ), 10, 2 );
        }

        public function translate_option( $option, $option_id ) {

            if ( !isset( $option[ 'option_type' ] ) ) {

                return $option;
            }

            if ( !isset( $option[ 'method_id' ] ) ) {

                return $option;
            }

            if ( !isset( $option[ 'rule_id' ] ) ) {

                return $option;
            }

            $option_type = $option[ 'option_type' ];


            $option_type_instance = $this->get_option_type_instance( $option_type );

            if ( !$option_type_instance ) {

                return $option;
            }

            $method_id = $option[ 'method_id' ];
            $rule_id = $option[ 'rule_id' ];

            $prefix = $this->get_prefix( $method_id, $rule_id, $option_id );

            return $option_type_instance->translate_option( $option, $prefix );
        }

        public function get_strings( $options, $method_id ) {

            $option_strings = array();

            $method_key = $method_id . '_method_rules';

            if ( !isset( $options[ $method_key ] ) ) {

                return $option_strings;
            }

            foreach ( $options[ $method_key ] as $method_options ) {

                $rule_id = $method_options[ 'rule_id' ];

                $option_strings = $this->get_method_option_strings( $option_strings, $method_options, $rule_id, $method_id );
            }

            return $option_strings;
        }

        private function get_method_option_strings( $option_strings, $method_options, $rule_id, $method_id ) {

            if ( !isset( $method_options[ 'method_options' ] ) ) {

                return $option_strings;
            }

            foreach ( $method_options[ 'method_options' ] as $method_option ) {

                $option_type = $method_option[ 'option_type' ];

                $option_type_instance = $this->get_option_type_instance( $option_type );

                if ( !$option_type_instance ) {

                    continue;
                }

                $option_id = $method_option[ 'option_id' ];

                $prefix = $this->get_prefix( $method_id, $rule_id, $option_id );

                $option_strings = $option_type_instance->get_strings( $option_strings, $method_option, $prefix );
            }

            return $option_strings;
        }

        private function get_prefix( $method_id, $rule_id, $option_id ) {

            return 'pgeo_' . $method_id . '_option_' . $rule_id . '_' . $option_id . '_';
        }

        private function get_option_type_instance( $option_type ) {

            if ( 'all' == $option_type ) {

                return new PGEO_PayGeo_WPML_Method_Options_All();
            }

            return false;
        }

    }

}
