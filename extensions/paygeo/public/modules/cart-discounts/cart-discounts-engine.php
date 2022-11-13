<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'PGEO_PayGeo_Cart_Discounts_Engine' ) ) {

    class PGEO_PayGeo_Cart_Discounts_Engine {

        public function process_cart_data( $cart_data ) {
            if ( 'fees_discounts' != $cart_data[ 'wc' ][ 'source' ] ) {
                return $cart_data;
            }

            if ( isset( $cart_data[ 'discounts' ] ) ) {
                return $cart_data;
            }

            $cart_data = $this->calculate_discounts( $this->reset_riskfree_method( $cart_data ) );

            return $cart_data;
        }

        private function calculate_discounts( $cart_data ) {

            $method_id = $this->get_method_id( $cart_data );

            //check if payment method is selected
            if ( $method_id == '' ) {
                return $cart_data;
            }
            $rules_settings = $this->get_rules_settings( $method_id );

            //check if discount rules should apply

            if ( 'no' == $rules_settings[ 'mode' ] ) {
                return $cart_data;
            }

            // go through the rules and apply them
            foreach ( $this->get_rule_options( $method_id ) as $rule ) {

                $rule_head = array(
                    'apply_mode' => $rule[ 'apply_mode' ],
                    'enable' => $rule[ 'enable' ],
                    'rule_id' => $rule[ 'rule_id' ],
                    'rule_limit' => $rule[ 'discounts_limit' ],
                    'settings' => $rules_settings,
                    'method_id' => $method_id,
                    'module' => 'cart-discounts',
                );

                $rule_conditions = $rule[ 'rule_conditions' ];
                $rule_conditions[ 'method_id' ] = $method_id;
                $rule_conditions[ 'module' ] = 'cart-discounts';


                if ( !$this->can_apply_rule( $rule_head, $rule_conditions, $cart_data ) ) {
                    continue;
                }

                $amounts_args = $this->get_amounts_args_from_rule( $rule, $method_id );

                $amounts = PGEO_PayGeo_Amount_Types::calculate_amounts( array(), $amounts_args, $cart_data );

                if ( has_filter( 'paygeo/calculated-amounts' ) ) {
                    $amounts = apply_filters( 'paygeo/calculated-amounts', $amounts, $rule_head[ 'rule_id' ], $cart_data );
                }

                $merge_args = array(
                    'rule_id' => $rule[ 'rule_id' ],
                    'method_id' => $method_id,
                    'module' => 'cart-discounts',
                );

                $m_amount = PGEO_PayGeo_Amount_Types::merge_amounts( $amounts, $merge_args );


                $rule_discount = array(
                    'title' => $rule[ 'title' ],
                    'desc' => $rule[ 'desc' ],
                    'apply_as_coupon' => $rule[ 'apply_as_coupon' ],
                    'coupon_code' => strtolower( $rule[ 'coupon_code' ] ),
                    'inclusive_tax' => $rule[ 'inclusive_tax' ],
                    'method_id' => $method_id,
                    'rule_id' => $rule[ 'rule_id' ],
                    'amount' => $m_amount[ 'amount' ],
                    'amount_tax' => $m_amount[ 'amount_tax' ],
                    'taxes' => $m_amount[ 'taxes' ],
                );


                if ( isset( $rule[ 'notify' ] ) ) {
                    $rule_discount[ 'notify' ] = $rule[ 'notify' ];
                }

                if ( '' == $rule[ 'coupon_code' ] && 'no' != $rule[ 'apply_as_coupon' ] ) {
                    $rule_discount[ 'coupon_code' ] = strtolower( $rule[ 'rule_id' ] );
                }

                if ( '' == $rule[ 'title' ] ) {
                    $rule_discount[ 'title' ] = esc_html__( 'Discount', 'pgeo-paygeo' );
                }

                $cart_data = $this->add_rule_discount( $cart_data, $rule_discount, $rule_head );
            }

            return $cart_data;
        }

        private function get_amounts_args_from_rule( $rule, $method_id ) {
            $amounts_args = array();

            if ( isset( $rule[ 'discount_amounts' ] ) ) {
                $amounts_args = $rule[ 'discount_amounts' ];
            }

            $arg_keys = array_keys( $amounts_args );
            foreach ( $arg_keys as $key ) {
                $amounts_args[ $key ][ 'method_id' ] = $method_id;
                $amounts_args[ $key ][ 'module' ] = 'cart-discounts';
                $amounts_args[ $key ][ 'rule_id' ] = $rule[ 'rule_id' ];

                if ( 'no' == $rule[ 'apply_as_coupon' ] ) {
                    $amounts_args[ $key ][ 'inclusive_tax' ] = $rule[ 'inclusive_tax' ];
                } else {
                    $amounts_args[ $key ][ 'inclusive_tax' ] = 'no';
                    $amounts_args[ $key ][ 'taxable' ] = '--1';
                }
            }

            return $amounts_args;
        }

        private function can_apply_rule( $rule_head, $rule_conditions, $cart_data ) {

            // allows active rules only

            if ( 'no' == $rule_head[ 'enable' ] ) {
                return false;
            }

            $bool_val = true;

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $bool_val = PGEO_PayGeo_Premium_Cart_Discounts_Engine::can_apply_rule( $rule_head, $cart_data );
            }


            // allows other plugins to check if the rule should apply

            if ( has_filter( 'paygeo/cart-discounts/can-apply-rule' ) ) {
                $bool_val = apply_filters( 'paygeo/cart-discounts/can-apply-rule', $bool_val, $rule_head, $cart_data );
            }

            // there no need for validations
            if ( false == $bool_val ) {
                return $bool_val;
            }

            // validate rule

            $bool_val = PGEO_PayGeo_Condition_Types::validate_rule_conditions( $rule_conditions, $cart_data );


            return $bool_val;
        }

        private function add_rule_discount( $cart_data, $rule_discount, $rule_head ) {

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $cart_data = PGEO_PayGeo_Premium_Cart_Discounts_Engine::add_rule_discount( $cart_data, $rule_discount, $rule_head );
            } else {
                $cart_data[ 'discounts' ][ $rule_head[ 'rule_id' ] ] = $rule_discount;
            }

            // allows other plugins to modify discounts data once added
            if ( has_filter( 'paygeo/cart-discounts/added-rule-discount' ) ) {
                if ( isset( $cart_data[ 'discounts' ][ $rule_head[ 'rule_id' ] ] ) ) {
                    $cart_data[ 'discounts' ][ $rule_head[ 'rule_id' ] ] = apply_filters( 'paygeo/cart-discounts/added-rule-discount', $cart_data[ 'discounts' ][ $rule_head[ 'rule_id' ] ], $rule_head );
                }
            }

            return $cart_data;
        }

        private function get_rules_settings( $method_id ) {
            $default = array(
                'mode' => 'no',
            );
            return PGEO_PayGeo::get_option( $method_id . '_discount_rules_settings', $default );
        }

        private function get_rule_options( $method_id ) {

            $rules = array();
            foreach ( PGEO_PayGeo::get_option( $method_id . '_discount_rules', array() ) as $rule ) {
                $rules[] = $rule;
            }
            return $rules;
        }

        private function get_method_id( $cart_data ) {

            if ( !isset( $cart_data[ 'wc' ][ 'method_ids' ] ) ) {
                return '';
            }

            $method_ids = $cart_data[ 'wc' ][ 'method_ids' ];

            if ( isset( $method_ids[ 'riskfree_id' ] ) && '' != $method_ids[ 'riskfree_id' ] ) {
                return $method_ids[ 'riskfree_id' ];
            } else if ( isset( $method_ids[ 'method_id' ] ) ) {
                return $method_ids[ 'method_id' ];
            }

            return '';
        }

        private function reset_riskfree_method( $cart_data ) {

            if ( !isset( $cart_data[ 'wc' ][ 'method_ids' ] ) ) {
                return $cart_data;
            }

            $method_ids = $cart_data[ 'wc' ][ 'method_ids' ];

            if ( !isset( $method_ids[ 'riskfree_id' ] ) || $method_ids[ 'riskfree_id' ] == '' ) {
                return $cart_data;
            }

            if ( !isset( $cart_data[ 'riskfee' ][ $method_ids[ 'riskfree_id' ] ] ) ) {
                $cart_data[ 'wc' ][ 'method_ids' ][ 'riskfree_id' ] = '';
            }

            return $cart_data;
        }

    }

}