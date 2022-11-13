<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Type_Per_Items' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Amount_Type_Per_Items {

        public static function init() {
            add_filter( 'paygeo-admin/get-amount-type-groups', array( new self(), 'get_groups' ), 20, 2 );
            add_filter( 'paygeo-admin/get-amount-group-types-per_items', array( new self(), 'get_types' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $types_data = self::get_amount_type_data( $args );

            $in_groups[ 'per_items' ] = $types_data[ 'per_items' ];

            return $in_groups;
        }

        public static function get_types( $in_types = array(), $args ) {

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
                    'per_items' => esc_html__( 'Discounts Per Items', 'pgeo-paygeo' ),
                    'prem_3' => esc_html__( 'Discount per cart line (Premium)', 'pgeo-paygeo' ),
                    'prem_4' => esc_html__( 'Discount per item quantity (Premium)', 'pgeo-paygeo' ),
                    'prem_5' => esc_html__( 'Discount per product (Premium)', 'pgeo-paygeo' ),
                    'prem_6' => esc_html__( 'Discount per variation (Premium)', 'pgeo-paygeo' ),
                    'prem_7' => esc_html__( 'Percentage discount per item (Premium)', 'pgeo-paygeo' ),
                );
            }

            return array(
                'per_items' => esc_html__( 'Fee Per Items', 'pgeo-paygeo' ),
                'prem_3' => esc_html__( 'Fee per cart lines (Premium)', 'pgeo-paygeo' ),
                'prem_4' => esc_html__( 'Fee per item quantity (Premium)', 'pgeo-paygeo' ),
                'prem_5' => esc_html__( 'Fee per product (Premium)', 'pgeo-paygeo' ),
                'prem_6' => esc_html__( 'Fee per variation (Premium)', 'pgeo-paygeo' ),
                'prem_7' => esc_html__( 'Percentage fee per item (Premium)', 'pgeo-paygeo' ),
            );
        }

    }

    PGEO_PayGeo_Admin_Amount_Type_Per_Items::init();
}