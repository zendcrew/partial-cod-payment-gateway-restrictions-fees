<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Shipping' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Shipping {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 40, 2 );

            add_filter( 'paygeo-admin/get-shipping-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );

            add_filter( 'paygeo-admin/get-shipping_needed-condition-fields', array( new self(), 'get_needed_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-shipping_zones-condition-fields', array( new self(), 'get_zone_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-shipping_methods-condition-fields', array( new self(), 'get_methods_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-shipping_rates-condition-fields', array( new self(), 'get_rates_fields' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'shipping' ] = esc_html__( 'Shipping', 'pgeo-paygeo' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'shipping_needed' ] = esc_html__( 'Needs Shipping', 'pgeo-paygeo' );
            $in_list[ 'shipping_zones' ] = esc_html__( 'Shipping Zones', 'pgeo-paygeo' );
            $in_list[ 'shipping_methods' ] = esc_html__( 'Shipping Methods', 'pgeo-paygeo' );
            $in_list[ 'shipping_rates' ] = esc_html__( 'Shipping Rates', 'pgeo-paygeo' );

            return $in_list;
        }

        public static function get_needed_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'needs_shipping',
                'type' => 'select2',
                'default' => 'yes',
                'options' => array(
                    'yes' => esc_html__( 'Yes', 'pgeo-paygeo' ),
                    'no' => esc_html__( 'No', 'pgeo-paygeo' ),
                ),
                'width' => '100%',
                'box_width' => '100%',
            );



            return $in_fields;
        }

        public static function get_zone_fields( $in_fields, $args ) {
            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'in_all_list' => esc_html__( 'All in the list', 'pgeo-paygeo' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'pgeo-paygeo' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'shipping_zones',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'placeholder' => esc_html__( 'Shipping zones...', 'pgeo-paygeo' ),
                'data' => 'wc:shipping_zones',
                'width' => '100%',
                'box_width' => '74%',
            );
            return $in_fields;
        }

        public static function get_methods_fields( $in_fields, $args ) {
            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'in_all_list' => esc_html__( 'All in the list', 'pgeo-paygeo' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'pgeo-paygeo' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'shipping_methods',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'placeholder' => esc_html__( 'Shipping methods...', 'pgeo-paygeo' ),
                'data' => 'wc:shipping_methods',
                'width' => '100%',
                'box_width' => '74%',
            );
            return $in_fields;
        }

        public static function get_rates_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'in_all_list' => esc_html__( 'All in the list', 'pgeo-paygeo' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'pgeo-paygeo' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'shipping_rates',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'placeholder' => esc_html__( 'Shipping rates...', 'pgeo-paygeo' ),
                'options' => PGEO_PayGeo_Extension::get_shipping_rates( $args ),
                'width' => '100%',
                'box_width' => '74%',
            );


            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Shipping::init();
}
