<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Amount_Type_Cart' ) ) {

    class WOOPCD_PartialCOD_Amount_Type_Cart {

        public function __construct() {
            add_filter( 'woopcd_partialcod/calculate-cart_fixed-amount', array( $this, 'process_fixed_amount' ), 1, 3 );
            add_filter( 'woopcd_partialcod/calculate-cart_shipping_classes-amount', array( $this, 'process_shipping_classes_amount' ), 1, 3 );
            add_filter( 'woopcd_partialcod/calculate-cart_per-amount', array( $this, 'process_percentage_amount' ), 1, 3 );
        }

        public function process_fixed_amount( $amount, $amount_args, $cart_data ) {

            $calc_amount = $amount_args[ 'amount' ];

            return WOOPCD_PartialCOD_Amount_Types::prepare_amount( $amount, $calc_amount );
        }

        public function process_shipping_classes_amount( $amount, $amount_args, $cart_data ) {

            if ( !isset( $amount_args[ 'shipping_classes_args' ] ) ) {
                return $amount;
            }

            $shipping_classes_args = $amount_args[ 'shipping_classes_args' ];

            $cart_items = $this->get_items_by_shipping_classes( $cart_data, $shipping_classes_args );

            $shipping_classes_count = $this->get_shipping_classes_count( $cart_items );

            $calc_amount = $amount_args[ 'amount' ] * $shipping_classes_count;

            return WOOPCD_PartialCOD_Amount_Types::prepare_amount( $amount, $calc_amount, $cart_items, $amount_args );
        }

        public function process_percentage_amount( $amount, $amount_args, $cart_data ) {

            $args = array(
                'module' => $amount_args[ 'module' ],
                'option_id' => $amount_args[ 'base_on' ]
            );

            $cart_totals = WOOPCD_PartialCOD_Cart_Total_Types::get_totals( $args, $cart_data );

            $calc_amount = ($amount_args[ 'amount' ] / 100) * $cart_totals;


            return WOOPCD_PartialCOD_Amount_Types::prepare_amount( $amount, $calc_amount );
        }

        private function get_shipping_classes_count( $cart_items ) {

            $shipping_classes = array();

            foreach ( $cart_items as $cart_item ) {

                $item_shipping_class_id = $cart_item[ 'data' ][ 'shipping_class_id' ];

                if ( in_array( $item_shipping_class_id, $shipping_classes ) ) {
                    continue;
                }

                if ( 0 >= $item_shipping_class_id ) {
                    continue;
                }

                $shipping_classes[] = $item_shipping_class_id;
            }

            return count( $shipping_classes );
        }

        private function get_items_by_shipping_classes( $cart_data, $shipping_classes_args ) {

            if ( !isset( $cart_data[ 'wc' ][ 'cart_items' ] ) ) {
                return array();
            }


            if ( !isset( $shipping_classes_args[ 'shipping_classes' ] ) || !is_array( $shipping_classes_args[ 'shipping_classes' ] ) ) {
                return $cart_data[ 'wc' ][ 'cart_items' ];
            }


            $shipping_classes = $shipping_classes_args[ 'shipping_classes' ];

            $shipping_class_ids = WOOPCD_PartialCOD_Util::get_product_term_ids_by_slugs( $shipping_classes, 'product_shipping_class' );

            $compare = 'in_list';
            if ( isset( $shipping_classes_args[ 'compare' ] ) ) {
                $compare = $shipping_classes_args[ 'compare' ];
            }

            $cart_items = array();

            foreach ( $cart_data[ 'wc' ][ 'cart_items' ] as $cart_item ) {

                if ( !isset( $cart_item[ 'data' ][ 'shipping_class_id' ] ) ) {
                    continue;
                }

                $item_shipping_class_id = $cart_item[ 'data' ][ 'shipping_class_id' ];

                if ( 0 >= $item_shipping_class_id ) {
                    continue;
                }

                if ( WOOPCD_PartialCOD_Validation_Util::validate_value_list( $item_shipping_class_id, $shipping_class_ids, $compare ) != true ) {
                    continue;
                }

                $cart_items[] = $cart_item;
            }

            return $cart_items;
        }

    }

    new WOOPCD_PartialCOD_Amount_Type_Cart();
}
