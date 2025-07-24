<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Amount_Type_Per_Items' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Amount_Type_Per_Items {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 20, 2 );
            add_filter( 'woopcd_partialcod-admin/get-amount-group-types-per_items', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_items' ] = $types_data[ 'per_items' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_3' ] = $types_data[ 'prem_3' ];
            $in_types[ 'prem_4' ] = $types_data[ 'prem_4' ];
            $in_types[ 'prem_5' ] = $types_data[ 'prem_5' ];
            $in_types[ 'prem_6' ] = $types_data[ 'prem_6' ];
            $in_types[ 'prem_7' ] = $types_data[ 'prem_7' ];

            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            if ( 'cart-discounts' == $args[ 'module' ] ) {
                return array(
                    'per_items' => esc_html__( 'Discounts Per Items', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_3' => esc_html__( 'Discount per cart line (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_4' => esc_html__( 'Discount per item quantity (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_5' => esc_html__( 'Discount per product (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_6' => esc_html__( 'Discount per variation (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_7' => esc_html__( 'Percentage discount per item (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                );
            }

            return array(
                'per_items' => esc_html__( 'Fee Per Items', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_3' => esc_html__( 'Fee per cart lines (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_4' => esc_html__( 'Fee per item quantity (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_5' => esc_html__( 'Fee per product (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_6' => esc_html__( 'Fee per variation (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_7' => esc_html__( 'Percentage fee per item (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Amount_Type_Per_Items::init();
}