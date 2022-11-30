<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Amount_Type_Volume' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Amount_Type_Volume {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 80, 2 );
            add_filter( 'woopcd_partialcod-admin/get-amount-group-types-per_volumes', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_volumes' ] = $types_data[ 'per_volumes' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_18' ] = $types_data[ 'prem_18' ];
            $in_types[ 'prem_19' ] = $types_data[ 'prem_19' ];


            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            if ( 'cart-discounts' == $args[ 'module' ] ) {
                return array(
                    'per_volumes' => esc_html__( 'Discount Per Volume', 'woopcd-partialcod' ),
                    'prem_18' => esc_html__( 'Discount per volume (Premium)', 'woopcd-partialcod' ),
                    'prem_19' => esc_html__( 'Discount x volume (Premium)', 'woopcd-partialcod' ),
                );
            }

            return array(
                'per_volumes' => esc_html__( 'Fee Per Volume', 'woopcd-partialcod' ),
                'prem_18' => esc_html__( 'Fee per volume (Premium)', 'woopcd-partialcod' ),
                'prem_19' => esc_html__( 'Fee x volume (Premium)', 'woopcd-partialcod' ),
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Amount_Type_Volume::init();
}

