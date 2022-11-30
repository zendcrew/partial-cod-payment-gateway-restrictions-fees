<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Conditions_Shipping' ) ) {

    class WOOPCD_PartialCOD_Conditions_Shipping {

        public function __construct() {


            add_filter( 'woopcd_partialcod/validate-shipping_needed-condition', array( $this, 'validate_needed' ), 10, 2 );
            add_filter( 'woopcd_partialcod/validate-shipping_zones-condition', array( $this, 'validate_zones' ), 10, 2 );
            add_filter( 'woopcd_partialcod/validate-shipping_methods-condition', array( $this, 'validate_methods' ), 10, 2 );
            add_filter( 'woopcd_partialcod/validate-shipping_rates-condition', array( $this, 'validate_rates' ), 10, 2 );
        }

        public function validate_needed( $condition, $cart_data ) {

            $rule_needs_shipping = $condition[ 'needs_shipping' ];

            $needs_shipping = $cart_data[ 'wc' ][ 'needs_shipping' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_yes_no( $needs_shipping, $rule_needs_shipping );
        }

        public function validate_zones( $condition, $cart_data ) {

            $rule_shipping_zones = $condition[ 'shipping_zones' ];

            if ( !is_array( $rule_shipping_zones ) ) {
                return false;
            }

            $zone_ids = $this->get_zone_by_rates( $cart_data[ 'wc' ][ 'shipping_rates' ] );

            if ( !count( $zone_ids ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $zone_ids, $rule_shipping_zones, $rule_compare );
        }

        public function validate_methods( $condition, $cart_data ) {

            $rule_shipping_methods = $condition[ 'shipping_methods' ];

            if ( !is_array( $rule_shipping_methods ) ) {
                return false;
            }

            $method_ids = array();

            foreach ( $cart_data[ 'wc' ][ 'shipping_rates' ] as $rate ) {
                $method_ids[] = $rate[ 'method_id' ];
            }


            if ( !count( $method_ids ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $method_ids, $rule_shipping_methods, $rule_compare );
        }

        public function validate_rates( $condition, $cart_data ) {
            $rule_shipping_rates = $condition[ 'shipping_rates' ];

            if ( !is_array( $rule_shipping_rates ) ) {
                return false;
            }

            $rate_ids = $this->get_rate_ids( $cart_data[ 'wc' ][ 'shipping_rates' ] );

            if ( !count( $rate_ids ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $rate_ids, $rule_shipping_rates, $rule_compare );
        }

        private function get_zone_by_rates( $cart_rates ) {

            $zone_ids = array();

            if ( !count( $cart_rates ) ) {
                return $zone_ids;
            }

            $instance_ids = array();

            foreach ( $cart_rates as $cart_rate ) {
                $instance_ids[] = esc_sql( $cart_rate[ 'instance_id' ] );
            }

            global $wpdb;

            $instance_ids_sql = implode( ',', $instance_ids );

            $sql_hash = 'partialcod_zones_query_' . md5( $instance_ids_sql );

            $zones_cache = get_transient( $sql_hash );
            if ( $zones_cache ) {
                return $zones_cache;
            }

            try {


                $sql = "SELECT zone_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods "
                        . "WHERE instance_id IN(" . $instance_ids_sql . ")";

                $results = $wpdb->get_results( $sql, ARRAY_A );
                foreach ( $results as $row ) {
                    $zone_ids[] = $row[ 'zone_id' ];
                }
                set_transient( $sql_hash, $zone_ids, MINUTE_IN_SECONDS + 30 );
            } catch ( Exception $ex ) {
                return $zone_ids;
            }

            return $zone_ids;
        }

        private function get_rate_ids( $cart_rates ) {

            $rate_ids = array();

            if ( !count( $cart_rates ) ) {

                return $rate_ids;
            }

            foreach ( $cart_rates as $rate ) {

                $rate_id = $rate[ 'instance_id' ];

                $rate_ids[] = $rate_id;
            }

            if ( has_filter( 'woopcd_partialcod/get-shipping-rate-ids' ) ) {

                $rate_ids = apply_filters( 'woopcd_partialcod/get-shipping-rate-ids', $rate_ids, $cart_rates );
            }

            return $rate_ids;
        }

    }

    new WOOPCD_PartialCOD_Conditions_Shipping();
}