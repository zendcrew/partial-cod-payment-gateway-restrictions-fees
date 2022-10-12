<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Amount_Types' ) ) {
    PGEO_PayGeo_Extension::required_paths( dirname( __FILE__ ), array( 'amount-types.php' ) );

    class PGEO_PayGeo_Amount_Types {

        private static $instance = null;

        private static function get_instance() {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function calculate_amounts( $amounts, $amounts_args, $cart_data ) {

            // go through each of the amount args and apply them
            foreach ( $amounts_args as $amount_args ) {

                if ( !self::get_instance()->can_calculate_amount( $amount_args, $cart_data ) ) {
                    continue; //do nothing
                }

                $amount = apply_filters( 'paygeo/calculate-' . $amount_args[ 'amount_type' ] . '-amount', array( 'amount' => 0 ), $amount_args, $cart_data );

                //calculates tax if not calculated
                if ( !isset( $amount[ 'amount_tax' ] ) ) {

                    $tax_args = array(
                        'tax_class' => $amount_args[ 'taxable' ],
                        'inclusive_tax' => ('yes' == $amount_args[ 'inclusive_tax' ])
                    );



                    $amount_taxes = self::get_instance()->calculate_amount_tax( $amount[ 'amount' ], $tax_args, $cart_data );
                    $amount[ 'amount_tax' ] = array_sum( $amount_taxes );

                    $amount[ 'taxes' ] = $amount_taxes;


                    if ( true == $tax_args[ 'inclusive_tax' ] ) {
                        $amount[ 'amount' ] = $amount[ 'amount' ] - $amount[ 'amount_tax' ];
                    }
                }


                $amount[ 'add_type' ] = $amount_args[ 'add_type' ];

                // allows other plugins to modify the processed amount
                if ( has_filter( 'paygeo/calculated-amount' ) ) {
                    $amount = apply_filters( 'paygeo/calculated-amount', $amount, $amount_args, $cart_data );
                }

                $amounts[] = $amount;
            }

            return $amounts;
        }

        public static function prepare_amount( $amount, $calc_amount, $cart_items = array(), $amount_args = array() ) {

            if ( 0 >= $calc_amount ) {
                return $amount;
            }

            $amount[ 'amount' ] = $calc_amount;

            if ( !count( $cart_items ) ) {
                return $amount;
            }

            if ( !count( $amount_args ) ) {
                return $amount;
            }

            $tax_args = array(
                'tax_class' => $amount_args[ 'taxable' ],
                'inclusive_tax' => ('yes' == $amount_args[ 'inclusive_tax' ])
            );


            $amount_taxes = PGEO_PayGeo_Amount_Tax::calculate( $calc_amount, $tax_args, $cart_items );
            $amount[ 'amount_tax' ] = array_sum( $amount_taxes );
            $amount[ 'taxes' ] = $amount_taxes;

            return $amount;
        }

        public static function merge_amounts( $amounts, $merge_args ) {

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $calc_amount = PGEO_PayGeo_Premium_Amount_Types::merge_amounts( $amounts );
            } else {

                $calc_amount = array(
                    'amount' => 0,
                    'amount_tax' => 0,
                    'taxes' => array(),
                );

                foreach ( $amounts as $amount ) {
                    $calc_amount[ 'amount' ] += $amount[ 'amount' ];
                    $calc_amount[ 'amount_tax' ] += $amount[ 'amount_tax' ];

                    $calc_amount[ 'taxes' ] = self::add_taxes( $calc_amount[ 'taxes' ], $amount[ 'taxes' ] );
                }
            }

            if ( $calc_amount[ 'amount' ] < 0 ) {
                $calc_amount[ 'amount' ] = 0;
            }

            if ( $calc_amount[ 'amount_tax' ] < 0 ) {
                $calc_amount[ 'amount_tax' ] = 0;
                $calc_amount[ 'taxes' ] = array();
            }

            // allows other plugins to modify the merged amounts
            if ( has_filter( 'paygeo/merged-amount' ) ) {
                $calc_amount = apply_filters( 'paygeo/merged-amount', $calc_amount, $amounts, $merge_args );
            }

            return $calc_amount;
        }

        private function can_calculate_amount( $amount_args, $cart_data ) {

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                return PGEO_PayGeo_Premium_Amount_Types::can_calculate_amount( $amount_args, $cart_data );
            }

            return true;
        }

        private function calculate_amount_tax( $amount_value, $tax_args, $cart_data ) {

            if ( $tax_args[ 'tax_class' ] == '--0' ) {
                return PGEO_PayGeo_Amount_Tax::calculate( $amount_value, $tax_args, $cart_data[ 'wc' ][ 'cart_items' ] );
            } else {
                return PGEO_PayGeo_Amount_Tax::calculate( $amount_value, $tax_args );
            }
        }

        private static function add_taxes( $prev_amount_taxes, $new_amount_taxes ) {

            foreach ( $new_amount_taxes as $key => $tax ) {
                if ( isset( $prev_amount_taxes[ $key ] ) ) {
                    $prev_amount_taxes[ $key ] += $tax;
                } else {
                    $prev_amount_taxes[ $key ] = $tax;
                }
            }
            return $prev_amount_taxes;
        }

    }

    new PGEO_PayGeo_Amount_Types();
}
