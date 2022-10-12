<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Type_Weight' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Amount_Type_Weight {

        public static function init() {
            add_filter( 'paygeo-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 30, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-per_weights', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_weights' ] = $types_data[ 'per_weights' ];

            return $in_groups;
        }

        public static function get_types( $in_types, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_types[ 'prem_8' ] = $types_data[ 'prem_8' ];
            $in_types[ 'prem_9' ] = $types_data[ 'prem_9' ];


            return $in_types;
        }

        private static function get_amount_type_data( $args ) {

            $weight_unit = get_option( 'woocommerce_weight_unit' );

            if ( 'cart-discounts' == $args[ 'module' ] ) {

                $d_prem_8 = str_replace( '[0]', $weight_unit, esc_html__( 'Discount per weight ([0]) (Premium)', 'zcpg-woo-paygeo' ) );
                $d_prem_9 = str_replace( '[0]', $weight_unit, esc_html__( 'Discount x weight ([0]) (Premium)', 'zcpg-woo-paygeo' ) );

                return array(
                    'per_weights' => esc_html__( 'Discount Per Weight', 'zcpg-woo-paygeo' ),
                    'prem_8' => $d_prem_8,
                    'prem_9' => $d_prem_9,
                );
            }

            $f_prem_8 = str_replace( '[0]', $weight_unit, esc_html__( 'Fee per weight ([0]) (Premium)', 'zcpg-woo-paygeo' ) );
            $f_prem_9 = str_replace( '[0]', $weight_unit, esc_html__( 'Fee x weight ([0]) (Premium)', 'zcpg-woo-paygeo' ) );

            return array(
                'per_weights' => esc_html__( 'Fee Per Weight', 'zcpg-woo-paygeo' ),
                'prem_8' => $f_prem_8,
                'prem_9' => $f_prem_9,
            );
        }

    }

    PGEO_PayGeo_Admin_Amount_Type_Weight::init();
}