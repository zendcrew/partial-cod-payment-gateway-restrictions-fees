<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Amount_Type_Height' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Amount_Type_Height {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 60, 2 );
            add_filter( 'woopcd_partialcod-admin/get-amount-group-types-per_heights', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_heights' ] = $types_data[ 'per_heights' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_14' ] = $types_data[ 'prem_14' ];
            $in_types[ 'prem_15' ] = $types_data[ 'prem_15' ];


            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            $height_unit = get_option( 'woocommerce_dimension_unit' );

            if ( 'cart-discounts' == $args[ 'module' ] ) {

                $d_prem_14 = str_replace( '[0]', $height_unit, esc_html__( 'Discount per height ([0]) (Premium)', 'woopcd-partialcod' ) );
                $d_prem_15 = str_replace( '[0]', $height_unit, esc_html__( 'Discount x height ([0]) (Premium)', 'woopcd-partialcod' ) );

                return array(
                    'per_heights' => esc_html__( 'Discount Per Height', 'woopcd-partialcod' ),
                    'prem_14' => $d_prem_14,
                    'prem_15' => $d_prem_15,
                );
            }

            $f_prem_14 = str_replace( '[0]', $height_unit, esc_html__( 'Fee per height ([0]) (Premium)', 'woopcd-partialcod' ) );
            $f_prem_15 = str_replace( '[0]', $height_unit, esc_html__( 'Fee x height ([0]) (Premium)', 'woopcd-partialcod' ) );

            return array(
                'per_heights' => esc_html__( 'Fee Per Height', 'woopcd-partialcod' ),
                'prem_14' => $f_prem_14,
                'prem_15' => $f_prem_15,
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Amount_Type_Height::init();
}