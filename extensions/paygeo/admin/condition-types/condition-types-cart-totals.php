<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Cart_Totals' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Cart_Totals {

        public static function init() {


            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 60, 2 );

            add_filter( 'paygeo-admin/get-cart_totals-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );

            foreach ( self::get_all_option_keys() as $key ) {
                add_filter( 'paygeo-admin/get-cart_totals_' . $key . '-condition-fields', array( new self(), 'get_fields' ), 10, 2 );
            }
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'cart_totals' ] = esc_html__( 'Cart Totals', 'pgeo-paygeo' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $options = array();

            if ( !isset( $args[ 'module' ] ) || $args[ 'module' ] == '' ) {
                $options = self::get_all_option( false );
            } else {
                $options = self::get_options_by_module( $args[ 'module' ], false );
            }
           

            if ( !count( $options ) ) {
               $options = self::get_default_options();
            }
            


            foreach ( $options as $key => $option ) {
                $in_list[ 'cart_totals_' . $key ] = $option;
            }

            return $in_list;
        }

        public static function get_fields( $in_fields, $args ) {
            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => '>=',
                'options' => array(
                    '>=' => esc_html__( 'More than or equal to', 'pgeo-paygeo' ),
                    '>' => esc_html__( 'More than', 'pgeo-paygeo' ),
                    '<=' => esc_html__( 'Less than or equal to', 'pgeo-paygeo' ),
                    '<' => esc_html__( 'Less than', 'pgeo-paygeo' ),
                    '==' => esc_html__( 'Equal to', 'pgeo-paygeo' ),
                    '!=' => esc_html__( 'Not equal to', 'pgeo-paygeo' ),
                ),
                'width' => '99%',
                'box_width' => '50%',
            );

            $in_fields[] = array(
                'id' => 'totals',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0.00',
                'placeholder' => esc_html__( '0.00', 'pgeo-paygeo' ),
                'width' => '100%',
                'box_width' => '50%',
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
            );
            return $in_fields;
        }

        private static function get_all_option( $include_default ) {
            $options = array();

            foreach ( self::get_options_by_module( 'method-options' ) as $key => $option ) {
                $options[ $key ] = $option;
            }

            foreach ( self::get_options_by_module( 'partial-payment' ) as $key => $option ) {
                $options[ $key ] = $option;
            }
            foreach ( self::get_options_by_module( 'cart-fees' ) as $key => $option ) {
                $options[ $key ] = $option;
            }
            foreach ( self::get_options_by_module( 'cart-discounts' ) as $key => $option ) {
                $options[ $key ] = $option;
            }

            if ( !$include_default ) {
                return $options;
            }

            foreach ( self::get_default_options() as $key => $option ) {
                if ( !isset( $options[ $key ] ) ) {
                    $options[ $key ] = $option;
                }
            }

            return $options;
        }

        private static function get_all_option_keys() {
            return array_keys( self::get_all_option( true ) );
        }

        private static function get_options_by_module( $module, $include_default = false ) {
            $options = array();

            $option_key = self::get_option_key_by_module( $module );

            $max_option = 9999;

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $max_option = 2;
            }
            $cnt = 1;
            foreach ( self::get_option( $option_key, array() ) as $key => $option ) {
                $options[ $key ] = $option;
                if ( $cnt >= $max_option ) {
                    break;
                }
                $cnt++;
            }

            if ( !$include_default ) {
                return $options;
            }

            foreach ( self::get_default_options() as $key => $option ) {
                if ( !isset( $options[ $key ] ) ) {
                    $options[ $key ] = $option;
                }
            }

            return $options;
        }

        private static function get_option( $option_key, $default ) {
            global $pgeo_paygeo;

            $options = array();

            if ( isset( $pgeo_paygeo[ $option_key ] ) ) {

                foreach ( $pgeo_paygeo[ $option_key ] as $option ) {
                    $options[ $option[ 'option_id' ] ] = $option[ 'title' ];
                }

                return $options;
            }



            return $default;
        }

        private static function get_default_options() {

            return array(
                '2234343' => esc_html__( 'Subtotal including tax', 'pgeo-paygeo' ),
                '2234344' => esc_html__( 'Subtotal excluding tax', 'pgeo-paygeo' )
            );
        }

        private static function get_option_key_by_module( $module ) {
            if ( 'cart-discounts' == $module ) {
                return 'cart_discount_cart_totals';
            }
            if ( 'cart-fees' == $module ) {
                return 'cart_fee_cart_totals';
            }
            if ( 'partial-payment' == $module ) {
                return 'riskfree_cart_totals';
            }
            if ( 'method-options' == $module ) {
                return 'method_cart_totals';
            }
            return '';
        }

    }

    PGEO_PayGeo_Admin_Conditions_Cart_Totals::init();
}
