<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_Billing_Address' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_Billing_Address {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 20, 2 );

            add_filter( 'woopcd_partialcod-admin/get-billing_address-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );

            add_filter( 'woopcd_partialcod-admin/get-billing_countries-condition-fields', array( new self(), 'get_countries_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-billing_states-condition-fields', array( new self(), 'get_states_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-billing_cities-condition-fields', array( new self(), 'get_cities_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-billing_postcodes-condition-fields', array( new self(), 'get_postcodes_fields' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'billing_address' ] = esc_html__( 'Billing Address', 'partial-cod-payment-gateway-restrictions-fees' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
            
            if ( !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                $in_list[ 'prem_3' ] = esc_html__( 'Billing Continents (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
            } else {
                $in_list[ 'billing_continents' ] = esc_html__( 'Billing Continents', 'partial-cod-payment-gateway-restrictions-fees' );
            }
            
            $in_list[ 'billing_countries' ] = esc_html__( 'Billing Countries', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'billing_states' ] = esc_html__( 'Billing States', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'billing_cities' ] = esc_html__( 'Billing Cities', 'partial-cod-payment-gateway-restrictions-fees' );
            $in_list[ 'billing_postcodes' ] = esc_html__( 'Billing Postcode / ZIP', 'partial-cod-payment-gateway-restrictions-fees' );

            return $in_list;
        }

        public static function get_countries_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'none' => esc_html__( 'None in the list', 'partial-cod-payment-gateway-restrictions-fees' ),
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
                'placeholder' => esc_html__( 'Billing Countries...', 'partial-cod-payment-gateway-restrictions-fees' ),
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
                    'in_list' => esc_html__( 'Any in the list', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'none' => esc_html__( 'None in the list', 'partial-cod-payment-gateway-restrictions-fees' ),
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
                'placeholder' => esc_html__( 'Billing States...', 'partial-cod-payment-gateway-restrictions-fees' ),
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
                    'in_list' => esc_html__( 'Any in the list', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'none' => esc_html__( 'None in the list', 'partial-cod-payment-gateway-restrictions-fees' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'cities',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'Seperate with comma, (e.g Jakarta, Delhi, Manila)', 'partial-cod-payment-gateway-restrictions-fees' ),
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
                    'match' => esc_html__( 'Match', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'not_match' => esc_html__( 'Not match', 'partial-cod-payment-gateway-restrictions-fees' ),
                ),
                'width' => '98%',
                'box_width' => '22%',
            );

            $in_fields[] = array(
                'id' => 'postcode',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'e.g 1815, 870*, [1870 - 9999], DSE, LDS', 'partial-cod-payment-gateway-restrictions-fees' ),
                'width' => '100%',
                'box_width' => '78%',
            );

            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_Billing_Address::init();
}
