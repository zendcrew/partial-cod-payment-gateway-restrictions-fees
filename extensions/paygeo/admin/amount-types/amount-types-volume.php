<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Type_Volume' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Amount_Type_Volume {

        public static function init() {
            add_filter( 'paygeo-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 80, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-per_volumes', array( new self(), 'get_types' ), 10, 2 );
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
                    'per_volumes' => esc_html__( 'Discount Per Volume', 'pgeo-paygeo' ),
                    'prem_18' => esc_html__( 'Discount per volume (Premium)', 'pgeo-paygeo' ),
                    'prem_19' => esc_html__( 'Discount x volume (Premium)', 'pgeo-paygeo' ),
                );
            }

            return array(
                'per_volumes' => esc_html__( 'Fee Per Volume', 'pgeo-paygeo' ),
                'prem_18' => esc_html__( 'Fee per volume (Premium)', 'pgeo-paygeo' ),
                'prem_19' => esc_html__( 'Fee x volume (Premium)', 'pgeo-paygeo' ),
            );
        }

    }

    PGEO_PayGeo_Admin_Amount_Type_Volume::init();
}

