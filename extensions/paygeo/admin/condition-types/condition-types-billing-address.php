<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Billing_Address' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Billing_Address {

        public static function init() {

            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 20, 2 );

            add_filter( 'paygeo-admin/get-billing_address-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );

            add_filter( 'paygeo-admin/get-billing_countries-condition-fields', array( new self(), 'get_countries_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-billing_states-condition-fields', array( new self(), 'get_states_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-billing_cities-condition-fields', array( new self(), 'get_cities_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/get-billing_postcodes-condition-fields', array( new self(), 'get_postcodes_fields' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'billing_address' ] = esc_html__( 'Billing Address', 'pgeo-paygeo' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
            
            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $in_list[ 'prem_3' ] = esc_html__( 'Billing Continents (Premium)', 'pgeo-paygeo' );
            } else {
                $in_list[ 'billing_continents' ] = esc_html__( 'Billing Continents', 'pgeo-paygeo' );
            }
            
            $in_list[ 'billing_countries' ] = esc_html__( 'Billing Countries', 'pgeo-paygeo' );
            $in_list[ 'billing_states' ] = esc_html__( 'Billing States', 'pgeo-paygeo' );
            $in_list[ 'billing_cities' ] = esc_html__( 'Billing Cities', 'pgeo-paygeo' );
            $in_list[ 'billing_postcodes' ] = esc_html__( 'Billing Postcode / ZIP', 'pgeo-paygeo' );

            return $in_list;
        }

        public static function get_countries_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'countries',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'minimum_input_length' => 2,
                'minimum_results_forsearch' => 10,
                'placeholder' => esc_html__( 'Billing Countries...', 'pgeo-paygeo' ),
                'ajax_data' => 'wc:countries',
                'width' => '100%',
                'box_width' => '74%',
            );

            return $in_fields;
        }

        public static function get_states_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'states',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'minimum_input_length' => 2,
                'minimum_results_forsearch' => 10,
                'placeholder' => esc_html__( 'Billing States...', 'pgeo-paygeo' ),
                'ajax_data' => 'wc:states',
                'width' => '100%',
                'box_width' => '74%',
            );

            return $in_fields;
        }

        public static function get_cities_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'cities',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'Seperate with comma, (e.g Jakarta, Delhi, Manila)', 'pgeo-paygeo' ),
                'width' => '100%',
                'box_width' => '74%',
            );

            return $in_fields;
        }

        public static function get_postcodes_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'match' => esc_html__( 'Match', 'pgeo-paygeo' ),
                    'not_match' => esc_html__( 'Not match', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '22%',
            );

            $in_fields[] = array(
                'id' => 'postcode',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'e.g 1815, 870*, [1870 - 9999], DSE, LDS', 'pgeo-paygeo' ),
                'width' => '100%',
                'box_width' => '78%',
            );

            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Billing_Address::init();
}
