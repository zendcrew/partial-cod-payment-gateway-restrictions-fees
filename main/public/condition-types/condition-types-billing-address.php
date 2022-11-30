<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Conditions_Billing_Address' ) ) {

    class WOOPCD_PartialCOD_Conditions_Billing_Address {

        public function __construct() {

            add_filter( 'woopcd_partialcod/validate-billing_countries-condition', array( $this, 'validate_countries' ), 10 );
            add_filter( 'woopcd_partialcod/validate-billing_states-condition', array( $this, 'validate_states' ), 10 );
            add_filter( 'woopcd_partialcod/validate-billing_cities-condition', array( $this, 'validate_cities' ), 10 );
            add_filter( 'woopcd_partialcod/validate-billing_postcodes-condition', array( $this, 'validate_postcodes' ), 10 );
        }

        public function validate_countries( $condition ) {

            $rule_countries = $condition[ 'countries' ];

            $rule_compare = $condition[ 'compare' ];

            if ( !is_array( $rule_countries ) ) {
                return false;
            }

            $country = WC()->customer->get_billing_country();

            return WOOPCD_PartialCOD_Validation_Util::validate_value_list( $country, $rule_countries, $rule_compare );
        }

        public function validate_states( $condition ) {
            $rule_states = $condition[ 'states' ];

            $rule_compare = $condition[ 'compare' ];

            if ( !is_array( $rule_states ) ) {
                return false;
            }
            $state = WC()->customer->get_billing_country() . ':' . WC()->customer->get_billing_state();

            return WOOPCD_PartialCOD_Validation_Util::validate_value_list( $state, $rule_states, $rule_compare );
        }

        public function validate_cities( $condition ) {

            $rule_city_str = $condition[ 'cities' ];

            if ( empty( $rule_city_str ) ) {
                return false;
            }

            $rule_cities = explode( ',', str_replace( ', ', ',', strtolower( $rule_city_str ) ) );

            $rule_compare = $condition[ 'compare' ];

            $city = strtolower( WC()->customer->get_billing_city() );

            return WOOPCD_PartialCOD_Validation_Util::validate_value_list( $city, $rule_cities, $rule_compare );
        }

        public function validate_postcodes( $condition ) {
            $rule_postcode = $condition[ 'postcode' ];

            if ( empty( $rule_postcode ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            $postcode = WC()->customer->get_billing_postcode();

            return WOOPCD_PartialCOD_Validation_Util::validate_match_value( $rule_compare, $postcode, $rule_postcode );
        }

    }

    new WOOPCD_PartialCOD_Conditions_Billing_Address();
}