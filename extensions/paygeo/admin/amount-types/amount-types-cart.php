<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Type_Cart' ) ) {

    class PGEO_PayGeo_Admin_Amount_Type_Cart {

        public static function init() {

            add_filter( 'paygeo-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 10, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-cart', array( new self(), 'get_types' ), 10, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-cart', array( new self(), 'get_types2' ), 20, 2 );

            add_filter( 'paygeo-admin/get-amount-type-cart_shipping_classes-fields', array( new self(), 'get_shippting_classes_amount_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-based-on-required-ids', array( new self(), 'get_based_on_required_ids' ), 10, 2 );

            add_filter( 'paygeo-admin/process-amount-type-cart_shipping_classes-options', array( new self(), 'process_shipping_classes_amount_options' ), 10, 3 );
            add_filter( 'paygeo-admin/process-amount-type-cart_per-options', array( new self(), 'process_percentage_amount_options' ), 10, 3 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'cart' ] = esc_html__( 'Cart', 'zcpg-woo-paygeo' );

            return $in_groups;
        }

        public static function get_types( $in_types = array(), $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'cart_fixed' ] = $types_data[ 'cart_fixed' ];
            $in_types[ 'cart_shipping_classes' ] = $types_data[ 'cart_shipping_classes' ];

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $in_types[ 'prem_1' ] = $types_data[ 'prem_1' ];
                $in_types[ 'prem_2' ] = $types_data[ 'prem_2' ];
            }


            return $in_types;
        }

        public static function get_types2( $in_types = array(), $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'cart_per' ] = $types_data[ 'cart_per' ];

            return $in_types;
        }

        public static function get_based_on_required_ids( $in_ids, $args ) {

            $in_ids[] = 'cart_per';

            return $in_ids;
        }

        public static function get_shippting_classes_amount_fields( $in_fields, $args ) {
            $in_fields[] = array(
                'id' => 'shipping_classes_args',
                'type' => 'columns-field',
                'columns' => 8,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'shipping_classes',
                        'type' => 'select2',
                        'column_size' => 6,
                        'column_title' => esc_html__( 'Shipping Classes', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls which shipping classes should be included', 'zcpg-woo-paygeo' ),
                        'default' => '',
                        'multiple' => true,
                        'minimum_input_length' => 1,
                        'minimum_results_forsearch' => 10,
                        'placeholder' => esc_html__( 'Search shipping classes...', 'zcpg-woo-paygeo' ),
                        'data' => array(
                            'source' => 'wc:shipping_classes',
                            'ajax' => true,
                            'value_col' => 'slug',
                            'show_value' => false,
                        ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'compare',
                        'type' => 'select2',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Equals To', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls how the shipping classes should be included', 'zcpg-woo-paygeo' ),
                        'default' => 'in_list',
                        'options' => array(
                            'in_list' => esc_html__( 'Any in the list', 'zcpg-woo-paygeo' ),
                            'none' => esc_html__( 'Any NOT in the list', 'zcpg-woo-paygeo' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );
            return $in_fields;
        }

        public static function process_shipping_classes_amount_options( $amount, $raw_amount, $args ) {

            if ( isset( $raw_amount[ 'shipping_classes_args' ] ) ) {
                $amount[ 'shipping_classes_args' ] = $raw_amount[ 'shipping_classes_args' ];
            }

            return $amount;
        }

        public static function process_percentage_amount_options( $amount, $raw_amount, $args ) {

            if ( isset( $raw_amount[ 'base_on' ] ) ) {
                $amount[ 'base_on' ] = $raw_amount[ 'base_on' ];
            }

            return $amount;
        }

        private static function get_amount_type_data( $args ) {

            if ( 'cart-discounts' == $args[ 'module' ] ) {
                return array(
                    'cart_fixed' => esc_html__( 'Fixed discount', 'zcpg-woo-paygeo' ),
                    'cart_shipping_classes' => esc_html__( 'Discount per shipping classes', 'zcpg-woo-paygeo' ),
                    'prem_1' => esc_html__( 'Discount per categories (Premium)', 'zcpg-woo-paygeo' ),
                    'prem_2' => esc_html__( 'Discount per tags (Premium)', 'zcpg-woo-paygeo' ),
                    'cart_per' => esc_html__( 'Percentage discount', 'zcpg-woo-paygeo' ),
                );
            }

            return array(
                'cart_fixed' => esc_html__( 'Fixed fee', 'zcpg-woo-paygeo' ),
                'cart_shipping_classes' => esc_html__( 'Fee per shipping classes', 'zcpg-woo-paygeo' ),
                'prem_1' => esc_html__( 'Fee per categories (Premium)', 'zcpg-woo-paygeo' ),
                'prem_2' => esc_html__( 'Fee per tags (Premium)', 'zcpg-woo-paygeo' ),
                'cart_per' => esc_html__( 'Percentage fee', 'zcpg-woo-paygeo' ),
            );
        }

    }

    PGEO_PayGeo_Admin_Amount_Type_Cart::init();
}