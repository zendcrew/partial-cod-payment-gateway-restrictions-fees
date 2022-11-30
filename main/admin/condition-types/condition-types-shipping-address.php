<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Conditions_Shipping_Address' ) ) {

    class WOOPCD_PartialCOD_Admin_Conditions_Shipping_Address {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/get-condition-groups', array( new self(), 'get_groups' ), 30, 2 );

            add_filter( 'woopcd_partialcod-admin/get-shipping_address-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );

            add_filter( 'woopcd_partialcod-admin/get-shipping_countries-condition-fields', array( new self(), 'get_countries_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-shipping_states-condition-fields', array( new self(), 'get_states_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-shipping_cities-condition-fields', array( new self(), 'get_cities_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-shipping_postcodes-condition-fields', array( new self(), 'get_postcodes_fields' ), 10, 2 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'shipping_address' ] = esc_html__( 'Shipping Address', 'woopcd-partialcod' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            if ( !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                $in_list[ 'prem_4' ] = esc_html__( 'Shipping Continents (Premium)', 'woopcd-partialcod' );
            } else {
                $in_list[ 'shipping_continents' ] = esc_html__( 'Shipping Continents', 'woopcd-partialcod' );
            }
            
            $in_list[ 'shipping_countries' ] = esc_html__( 'Shipping Countries', 'woopcd-partialcod' );
            $in_list[ 'shipping_states' ] = esc_html__( 'Shipping States', 'woopcd-partialcod' );
            $in_list[ 'shipping_cities' ] = esc_html__( 'Shipping Cities', 'woopcd-partialcod' );
            $in_list[ 'shipping_postcodes' ] = esc_html__( 'Shipping Postcode / ZIP', 'woopcd-partialcod' );

            return $in_list;
        }

        public static function get_countries_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'woopcd-partialcod' ),
                    'none' => esc_html__( 'None in the list', 'woopcd-partialcod' ),
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
                'placeholder' => esc_html__( 'Shipping Countries...', 'woopcd-partialcod' ),
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
                    'in_list' => esc_html__( 'Any in the list', 'woopcd-partialcod' ),
                    'none' => esc_html__( 'None in the list', 'woopcd-partialcod' ),
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
                'placeholder' => esc_html__( 'Shipping States...', 'woopcd-partialcod' ),
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
                    'in_list' => esc_html__( 'Any in the list', 'woopcd-partialcod' ),
                    'none' => esc_html__( 'None in the list', 'woopcd-partialcod' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'cities',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'Seperate with comma, (e.g Jakarta, Delhi, Manila)', 'woopcd-partialcod' ),
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
                    'match' => esc_html__( 'Match', 'woopcd-partialcod' ),
                    'not_match' => esc_html__( 'Not match', 'woopcd-partialcod' ),
                ),
                'width' => '98%',
                'box_width' => '22%',
            );

            $in_fields[] = array(
                'id' => 'postcode',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'e.g 1815, 870*, [1870 - 9999], DSE, LDS', 'woopcd-partialcod' ),
                'width' => '100%',
                'box_width' => '78%',
            );

            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Conditions_Shipping_Address::init();
}