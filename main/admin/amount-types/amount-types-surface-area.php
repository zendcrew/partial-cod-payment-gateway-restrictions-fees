<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Amount_Type_Surface_Area' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Amount_Type_Surface_Area {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 70, 2 );
            add_filter( 'woopcd_partialcod-admin/get-amount-group-types-per_surface_areas', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_surface_areas' ] = $types_data[ 'per_surface_areas' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_16' ] = $types_data[ 'prem_16' ];
            $in_types[ 'prem_17' ] = $types_data[ 'prem_17' ];


            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            if ( 'cart-discounts' == $args[ 'module' ] ) {
                return array(
                    'per_surface_areas' => esc_html__( 'Discount Per Surface Area', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_16' => esc_html__( 'Discount per surface area (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_17' => esc_html__( 'Discount x surface area (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                );
            }

            return array(
                'per_surface_areas' => esc_html__( 'Fee Per Surface Area', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_16' => esc_html__( 'Fee per surface area (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_17' => esc_html__( 'Fee x surface area (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Amount_Type_Surface_Area::init();
}
