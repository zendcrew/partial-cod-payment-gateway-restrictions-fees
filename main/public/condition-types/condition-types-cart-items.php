<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Conditions_Cart_Items' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Conditions_Cart_Items {

        public function __construct() {
            add_filter( 'woopcd_partialcod/validate-cart_item_products-condition', array( $this, 'validate_cart_item_products' ), 10, 2 );
            add_filter( 'woopcd_partialcod/validate-cart_item_categories-condition', array( $this, 'validate_cart_item_categories' ), 10, 2 );
            add_filter( 'woopcd_partialcod/validate-cart_item_shipping_classes-condition', array( $this, 'validate_cart_item_shipping_classes' ), 10, 2 );
        }

        public function validate_cart_item_products( $condition, $cart_data ) {

            if ( !isset( $condition[ 'product_slugs' ] ) ) {
                return false;
            }

            $rule_product_ids = WOOPCD_PartialCOD_Util::get_product_ids_by_slugs( $condition[ 'product_slugs' ] );

            if ( !count( $rule_product_ids ) ) {
                return false;
            }

            $product_ids = WOOPCD_PartialCOD_Cart_Util::get_product_ids( $cart_data );

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $product_ids, $rule_product_ids, $rule_compare );
        }

        public function validate_cart_item_categories( $condition, $cart_data ) {

            if ( !isset( $condition[ 'category_slugs' ] ) ) {
                return false;
            }

            $rule_category_ids = WOOPCD_PartialCOD_Util::get_product_term_ids_by_slugs( $condition[ 'category_slugs' ], 'product_cat' );

            if ( !count( $rule_category_ids ) ) {
                return false;
            }

            $category_ids = WOOPCD_PartialCOD_Cart_Util::get_category_ids( $cart_data );

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $category_ids, $rule_category_ids, $rule_compare );
        }

        public function validate_cart_item_shipping_classes( $condition, $cart_data ) {


            if ( !isset( $condition[ 'shipping_classes' ] ) ) {
                return false;
            }

            $rule_shipping_classes = WOOPCD_PartialCOD_Util::get_product_term_ids_by_slugs( $condition[ 'shipping_classes' ], 'product_shipping_class' );

            if ( !count( $rule_shipping_classes ) ) {
                return false;
            }

            $shipping_classes = WOOPCD_PartialCOD_Cart_Util::get_shipping_classes( $cart_data );

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $shipping_classes, $rule_shipping_classes, $rule_compare );
        }

    }

    new WOOPCD_PartialCOD_Conditions_Cart_Items();
}
