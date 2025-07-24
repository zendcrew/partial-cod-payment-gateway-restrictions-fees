<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Amount_Type_Width' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Amount_Type_Width {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 50, 2 );
            add_filter( 'woopcd_partialcod-admin/get-amount-group-types-per_widths', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_widths' ] = $types_data[ 'per_widths' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_12' ] = $types_data[ 'prem_12' ];
            $in_types[ 'prem_13' ] = $types_data[ 'prem_13' ];


            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            $width_unit = get_option( 'woocommerce_dimension_unit' );

            if ( 'cart-discounts' == $args[ 'module' ] ) {

                $d_prem_12 = str_replace( '[0]', $width_unit, esc_html__( 'Discount per width ([0]) (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ) );
                $d_prem_13 = str_replace( '[0]', $width_unit, esc_html__( 'Discount x width ([0]) (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ) );

                return array(
                    'per_widths' => esc_html__( 'Discount Per Width', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_12' => $d_prem_12,
                    'prem_13' => $d_prem_13,
                );
            }

            $f_prem_12 = str_replace( '[0]', $width_unit, esc_html__( 'Fee per width ([0]) (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ) );
            $f_prem_13 = str_replace( '[0]', $width_unit, esc_html__( 'Fee x width ([0]) (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ) );

            return array(
                'per_widths' => esc_html__( 'Fee Per Width', 'partial-cod-payment-gateway-restrictions-fees' ),
                'prem_12' => $f_prem_12,
                'prem_13' => $f_prem_13,
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Amount_Type_Width::init();
}