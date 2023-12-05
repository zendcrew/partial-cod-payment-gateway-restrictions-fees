<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Amount_Type_Length' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Amount_Type_Length {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 40, 2 );
            add_filter( 'woopcd_partialcod-admin/get-amount-group-types-per_lengths', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_lengths' ] = $types_data[ 'per_lengths' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_10' ] = $types_data[ 'prem_10' ];
            $in_types[ 'prem_11' ] = $types_data[ 'prem_11' ];


            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            $length_unit = get_option( 'woocommerce_dimension_unit' );

            if ( 'cart-discounts' == $args[ 'module' ] ) {

                $d_prem_10 = str_replace( '[0]', $length_unit, esc_html__( 'Discount per length ([0]) (Premium)', 'woopcd-partialcod' ) );
                $d_prem_11 = str_replace( '[0]', $length_unit, esc_html__( 'Discount x length ([0]) (Premium)', 'woopcd-partialcod' ) );

                return array(
                    'per_lengths' => esc_html__( 'Discount Per Length', 'woopcd-partialcod' ),
                    'prem_10' => $d_prem_10,
                    'prem_11' => $d_prem_11,
                );
            }

            $f_prem_10 = str_replace( '[0]', $length_unit, esc_html__( 'Fee per length ([0]) (Premium)', 'woopcd-partialcod' ) );
            $f_prem_11 = str_replace( '[0]', $length_unit, esc_html__( 'Fee x length ([0]) (Premium)', 'woopcd-partialcod' ) );

            return array(
                'per_lengths' => esc_html__( 'Fee Per Length', 'woopcd-partialcod' ),
                'prem_10' => $f_prem_10,
                'prem_11' => $f_prem_11,
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Amount_Type_Length::init();
}