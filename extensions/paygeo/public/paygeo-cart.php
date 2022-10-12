<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'PGEO_PayGeo_Cart' ) ) {

    class PGEO_PayGeo_Cart {

        private static $method_ids = array();
        private static $parent_products = array();

        public static function get_session_data( $key, $default ) {

            $session_data = WC()->session->get( 'paygeo', array() );
            if ( isset( $session_data[ $key ] ) ) {
                return $session_data[ $key ];
            }

            return $default;
        }

        public static function set_session_data( $key, $object ) {

            $session_data = WC()->session->get( 'paygeo', array() );
            $session_data[ $key ] = $object;
            WC()->session->set( 'paygeo', $session_data );
        }

        public static function get_method_ids() {
            // get cached method ids
            if ( count( self::$method_ids ) ) {
                return self::$method_ids;
            }

            // get method ids from option database
            $method_ids = get_option( 'woocommerce_gateway_order', array() );

            if ( count( $method_ids ) ) {
                self::$method_ids = array_keys( $method_ids );
                return self::$method_ids;
            }
         
            // get method ids from wc save it into transient database
            ob_start();
            foreach ( WC()->payment_gateways()->payment_gateways() as $method ) {
                if ( $method->enabled != 'yes' ) {
                    continue;
                }
                $method_ids[] = $method->id;
            }
            
            ob_clean();

            self::$method_ids = $method_ids;
            
            return self::$method_ids;
        }

        public static function get_data( $source, $cart ) {

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $cart_data = PGEO_PayGeo_Premium_Cart::get_data( array( 'source' => $source ), $cart );
            } else {
                $cart_data = self::get_contents_data( array( 'source' => $source ), $cart );
            }
            return $cart_data;
        }

        private static function get_contents_data( $cart_data, $cart ) {

            $cart_data[ 'cart_items' ] = array();


            foreach ( $cart->cart_contents as $key => $item ) {
                $cart_data[ 'cart_items' ][ $key ] = self::get_item( $cart->cart_contents[ $key ] );
                $cart_data[ 'cart_items' ][ $key ][ 'data' ] = self::get_product_data( $cart->cart_contents[ $key ][ 'data' ], $key );
            }

            return self::get_totals_data( $cart_data, $cart );
        }

        private static function get_item( $cart_item ) {
            $item = array(
                'product_id' => $cart_item[ 'product_id' ],
                'quantity' => $cart_item[ 'quantity' ],
            );

            if ( has_filter( 'paygeo/get-cart-item' ) ) {
                $item = apply_filters( 'paygeo/get-cart-item', $item, $cart_item );
            }
            return $item;
        }

        private static function get_product_data( $product, $item_key ) {
            $item_data = array(
                'stock_quantity' => $product->get_stock_quantity(),
                'is_taxable' => $product->is_taxable(),
                'tax_status' => $product->get_tax_status(),
                'tax_class' => $product->get_tax_class(),
                'shipping_class_id' => $product->get_shipping_class_id(),
                'weight' => $product->get_weight(),
            );

            if ( $product->get_parent_id() > 0 ) {

                $v_product = self::get_parent_product( $product->get_parent_id() );

                $item_data[ 'category_ids' ] = $v_product->get_category_ids();

                $db_attrs = $v_product->get_attributes();
            } else {

                $item_data[ 'category_ids' ] = $product->get_category_ids();

                $db_attrs = $product->get_attributes();
            }

            $attrs = array();
            foreach ( $db_attrs as $db_attr ) {
                foreach ( $db_attr->get_options() as $attr_option ) {
                    $attrs[] = $attr_option;
                }
            }

            if ( has_filter( 'paygeo/get-cart-product-data' ) ) {
                $item_data = apply_filters( 'paygeo/get-cart-product-data', $item_data, $product, $item_key );
            }
            return $item_data;
        }

        private static function get_totals_data( $cart_data, $cart ) {

            $totals_data = array();

            $cart_totals = $cart->get_totals();

            $totals_data[ 'subtotal' ] = $cart_totals[ 'subtotal' ];
            $totals_data[ 'subtotal_tax' ] = $cart_totals[ 'subtotal_tax' ];

            $cart_data[ 'totals' ] = $totals_data;
            if ( has_filter( 'paygeo/get-cart-totals' ) ) {
                $cart_data[ 'totals' ] = apply_filters( 'paygeo/get-cart-totals', $cart_data[ 'totals' ], $cart_data[ 'source' ], $cart );
            }

            return self::get_applied_coupons( $cart_data, $cart );
        }

        private static function get_applied_coupons( $cart_data, $cart ) {

            $cart_data[ 'paygeo_coupons' ] = array();
            $cart_data[ 'applied_coupons' ] = array();


            $coupons = WC()->session->get( 'applied_coupons', array() );

            $paygeo = PGEO_PayGeo_Cart::get_session_data( 'coupons', array() );


            foreach ( $coupons as $coupon ) {
                if ( in_array( $coupon, $paygeo ) ) {
                    $cart_data[ 'paygeo_coupons' ][] = $coupon;
                }
                $cart_coupon = array(
                    'coupon_code' => $coupon,
                );

                $cart_data[ 'applied_coupons' ][] = $cart_coupon;
            }

            if ( has_filter( 'paygeo/get-cart-applied-coupons' ) ) {
                $cart_data[ 'applied_coupons' ] = apply_filters( 'paygeo/get-cart-applied-coupons', $cart_data[ 'applied_coupons' ], $cart_data[ 'source' ], $cart );
            }

            return self::get_shipping_rate( $cart_data, $cart );
        }

        private static function get_shipping_rate( $cart_data, $cart ) {

            $needs_shipping = $cart->needs_shipping();

            $cart_data[ 'needs_shipping' ] = $needs_shipping;

            $cart_data[ 'shipping_rates' ] = array();

            $chosen_rate = WC()->session->get( 'chosen_shipping_methods', array() );

            $cnt = 0;

            while ( $package = WC()->session->get( 'shipping_for_package_' . $cnt, false ) ) {

                if ( isset( $package[ 'rates' ] ) && $needs_shipping ) {
                    foreach ( $package[ 'rates' ] as $key => $shipping_rate ) {

                        if ( in_array( $shipping_rate->get_id(), $chosen_rate ) ) {
                            $rate_id = $shipping_rate->get_id();
                            $cart_data[ 'shipping_rates' ][] = array(
                                'id' => $rate_id,
                                'method_id' => $shipping_rate->get_method_id(),
                                'instance_id' => $shipping_rate->get_instance_id(),
                            );
                        }
                    }
                }
                $cnt++;
            }


            if ( has_filter( 'paygeo/get-cart-shipping-rates' ) ) {
                $cart_data[ 'shipping_rates' ] = apply_filters( 'paygeo/get-cart-shipping-rates', $cart_data[ 'shipping_rates' ], $cart_data[ 'source' ], $cart );
            }

            return self::get_payment_method( $cart_data, $cart );
        }

        private static function get_payment_method( $cart_data, $cart ) {

            $cart_data[ 'method_ids' ] = array(
                'method_id' => WC()->session->get( 'chosen_payment_method', '' )
            );

            if ( has_filter( 'paygeo/get-cart-method-ids' ) ) {
                $cart_data[ 'method_ids' ] = apply_filters( 'paygeo/get-cart-method-ids', $cart_data[ 'method_ids' ], $cart_data[ 'source' ], $cart );
            }

            return $cart_data;
        }

        private static function get_parent_product( $parent_id ) {
            if ( isset( self::$parent_products[ $parent_id ] ) ) {
                return self::$parent_products[ $parent_id ];
            }
            self::$parent_products[ $parent_id ] = wc_get_product( $parent_id );
            return self::$parent_products[ $parent_id ];
        }

    }

}