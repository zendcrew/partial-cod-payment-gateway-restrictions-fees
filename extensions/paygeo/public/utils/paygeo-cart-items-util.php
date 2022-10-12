<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'PGEO_PayGeo_Cart_Util' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Cart_Util {

        public static function get_product_ids( $cart_data ) {
            $product_ids = array();

            if ( !isset( $cart_data[ 'wc' ][ 'cart_items' ] ) ) {
                return $product_ids;
            }

            foreach ( $cart_data[ 'wc' ][ 'cart_items' ] as $cart_item ) {

                if ( isset( $cart_item[ 'product_id' ] ) && 0 < $cart_item[ 'product_id' ] ) {
                    $product_ids[] = $cart_item[ 'product_id' ];
                }
            }

            return $product_ids;
        }

        public static function get_category_ids( $cart_data ) {
            $category_ids = array();

            if ( !isset( $cart_data[ 'wc' ][ 'cart_items' ] ) ) {
                return $category_ids;
            }

            foreach ( $cart_data[ 'wc' ][ 'cart_items' ] as $cart_item ) {

                if ( !isset( $cart_item[ 'data' ][ 'category_ids' ] ) ) {
                    continue;
                }

                foreach ( $cart_item[ 'data' ][ 'category_ids' ] as $category_id ) {
                    if ( !in_array( $category_id, $category_ids ) ) {
                        $category_ids[] = $category_id;
                    }
                }
            }

            return $category_ids;
        }

        public static function get_shipping_classes( $cart_data ) {
            $shipping_classes = array();


            if ( !isset( $cart_data[ 'wc' ][ 'cart_items' ] ) ) {
                return $shipping_classes;
            }

            foreach ( $cart_data[ 'wc' ][ 'cart_items' ] as $cart_item ) {
                if ( !isset( $cart_item[ 'data' ][ 'shipping_class_id' ] ) ) {
                    continue;
                }
                $shipping_class = $cart_item[ 'data' ][ 'shipping_class_id' ];

                if ( 0 < $shipping_class ) {
                    $shipping_classes[] = $shipping_class;
                }
            }

            return $shipping_classes;
        }

    }

}
