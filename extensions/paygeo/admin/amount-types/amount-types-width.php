<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Type_Width' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Amount_Type_Width {

        public static function init() {
            add_filter( 'paygeo-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 50, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-per_widths', array( new self(), 'get_types' ), 10, 2 );
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

                $d_prem_12 = str_replace( '[0]', $width_unit, esc_html__( 'Discount per width ([0]) (Premium)', 'zcpg-woo-paygeo' ) );
                $d_prem_13 = str_replace( '[0]', $width_unit, esc_html__( 'Discount x width ([0]) (Premium)', 'zcpg-woo-paygeo' ) );

                return array(
                    'per_widths' => esc_html__( 'Discount Per Width', 'zcpg-woo-paygeo' ),
                    'prem_12' => $d_prem_12,
                    'prem_13' => $d_prem_13,
                );
            }

            $f_prem_12 = str_replace( '[0]', $width_unit, esc_html__( 'Fee per width ([0]) (Premium)', 'zcpg-woo-paygeo' ) );
            $f_prem_13 = str_replace( '[0]', $width_unit, esc_html__( 'Fee x width ([0]) (Premium)', 'zcpg-woo-paygeo' ) );

            return array(
                'per_widths' => esc_html__( 'Fee Per Width', 'zcpg-woo-paygeo' ),
                'prem_12' => $f_prem_12,
                'prem_13' => $f_prem_13,
            );
        }

    }

    PGEO_PayGeo_Admin_Amount_Type_Width::init();
}