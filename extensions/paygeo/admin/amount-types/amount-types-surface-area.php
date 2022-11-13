<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Type_Surface_Area' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Amount_Type_Surface_Area {

        public static function init() {
            add_filter( 'paygeo-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 70, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-per_surface_areas', array( new self(), 'get_types' ), 10, 2 );
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
                    'per_surface_areas' => esc_html__( 'Discount Per Surface Area', 'pgeo-paygeo' ),
                    'prem_16' => esc_html__( 'Discount per surface area (Premium)', 'pgeo-paygeo' ),
                    'prem_17' => esc_html__( 'Discount x surface area (Premium)', 'pgeo-paygeo' ),
                );
            }

            return array(
                'per_surface_areas' => esc_html__( 'Fee Per Surface Area', 'pgeo-paygeo' ),
                'prem_16' => esc_html__( 'Fee per surface area (Premium)', 'pgeo-paygeo' ),
                'prem_17' => esc_html__( 'Fee x surface area (Premium)', 'pgeo-paygeo' ),
            );
        }

    }

    PGEO_PayGeo_Admin_Amount_Type_Surface_Area::init();
}
