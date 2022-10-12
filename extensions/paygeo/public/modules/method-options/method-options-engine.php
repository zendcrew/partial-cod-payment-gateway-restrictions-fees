<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'PGEO_PayGeo_Method_Options_Engine' ) ) {

    class PGEO_PayGeo_Method_Options_Engine {

        public function process_cart_data( $cart_data ) {
            
            if ( 'gateways' != $cart_data['wc'][ 'source' ] ) {
                return $cart_data;
            }

            if ( isset( $cart_data[ 'options' ] ) ) {
                return $cart_data;
            }

            return $this->get_options( $cart_data );
        }

        private function get_options( $cart_data ) {
            $method_ids = PGEO_PayGeo_Cart::get_method_ids();

            foreach ( $method_ids as $method_id ) {
                $cart_data = $this->get_method_options( $cart_data, $method_id );
            }

            return $cart_data;
        }

        private function get_method_options( $cart_data, $method_id ) {

            $rules_settings = $this->get_rules_settings( $method_id );

            //check if fee rules should apply

            if ( 'no' == $rules_settings[ 'mode' ] ) {
                return $cart_data;
            }



            // go through the rules and apply them
            foreach ( $this->get_rule_options( $method_id ) as $rule ) {

                $rule_head = array(
                    'apply_mode' => $rule[ 'apply_mode' ],
                    'enable' => $rule[ 'enable' ],
                    'method_id' => $method_id,
                    'rule_id' => $rule[ 'rule_id' ],
                    'settings' => $rules_settings,
                    'module' => 'method-options',
                );

                $rule_conditions = $rule[ 'rule_conditions' ];
                $rule_conditions[ 'module' ] = 'method-options';


                if ( !$this->can_apply_rule( $rule_head, $rule_conditions, $cart_data ) ) {
                    continue;
                }


                $options_args = $this->get_options_args_from_rule( $method_id, $rule );

                $options = PGEO_PayGeo_Option_Types::get_options( array(), $options_args, $cart_data );

                if ( has_filter( 'paygeo/method-options/gotten-options' ) ) {
                    $options = apply_filters( 'paygeo/method-options/gotten-options', $options, $rule_head[ 'rule_id' ], $cart_data );
                }


                $cart_data = $this->add_rule_options( $cart_data, $options, $rule_head );
            }




            return $cart_data;
        }

        private function get_options_args_from_rule( $method_id, $rule ) {
            $options_args = array();

            if ( isset( $rule[ 'method_options' ] ) ) {
                $options_args = $rule[ 'method_options' ];
            }

            $arg_keys = array_keys( $options_args );
            foreach ( $arg_keys as $key ) {
                $options_args[ $key ][ 'module' ] = 'method-options';
                $options_args[ $key ][ 'rule_id' ] = $rule[ 'rule_id' ];
                $options_args[ $key ][ 'method_id' ] = $method_id;
            }

            return $options_args;
        }

        private function can_apply_rule( $rule_head, $rule_conditions, $cart_data ) {

            // allows active rules only

            if ( 'no' == $rule_head[ 'enable' ] ) {
                return false;
            }

            $bool_val = true;

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $bool_val = PGEO_PayGeo_Premium_Method_Options_Engine::can_apply_rule( $rule_head, $cart_data );
            }


            // allows other plugins to check if the rule should apply

            if ( has_filter( 'paygeo/cart-fees/can-apply-rule' ) ) {
                $bool_val = apply_filters( 'paygeo/method-options/can-apply-rule', $bool_val, $rule_head, $cart_data );
            }

            // there no need for validations
            if ( false == $bool_val ) {
                return $bool_val;
            }

            // validate rule

            $bool_val = PGEO_PayGeo_Condition_Types::validate_rule_conditions( $rule_conditions, $cart_data );


            return $bool_val;
        }

        private function add_rule_options( $cart_data, $rule_options, $rule_head ) {

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $cart_data = PGEO_PayGeo_Premium_Method_Options_Engine::add_rule_options( $cart_data, $rule_options, $rule_head );
            } else {
                $cart_data[ 'options' ][ $rule_head[ 'rule_id' ] ] = $rule_options;
            }

            // allows other plugins to modify options data once added
            if ( has_filter( 'paygeo/method-options/added-rule-options' ) ) {
                if ( isset( $cart_data[ 'options' ][ $rule_head[ 'rule_id' ] ] ) ) {
                    $cart_data[ 'options' ][ $rule_head[ 'rule_id' ] ] = apply_filters( 'paygeo/method-options/added-rule-options', $cart_data[ 'options' ][ $rule_head[ 'rule_id' ] ], $rule_head );
                }
            }


            return $cart_data;
        }

        private function get_rules_settings( $method_id ) {
            $default = array(
                'mode' => 'no',
            );
            return PGEO_PayGeo::get_option( $method_id . '_rules_settings', $default );
        }

        private function get_rule_options( $method_id ) {

            $rules = array();
            foreach ( PGEO_PayGeo::get_option( $method_id . '_method_rules', array() ) as $rule ) {
                $rules[] = $rule;
            }
            return $rules;
        }

    }

}
