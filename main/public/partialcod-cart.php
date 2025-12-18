<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Cart' ) ) {

    class WOOPCD_PartialCOD_Cart {

        private static $instance;
        private static $method_ids = array();
        private $parent_products;

        public function __construct() {

            $this->parent_products = array();
        }

        public static function get_instance(): self {

            if ( is_null( self::$instance ) ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public static function get_session_data( $key, $default ) {

            $session_data = WC()->session->get( 'partialcod', array() );

            if ( isset( $session_data[ $key ] ) ) {

                return $session_data[ $key ];
            }

            return $default;
        }

        public static function set_session_data( $key, $object ) {

            $session_data = WC()->session->get( 'partialcod', array() );
            $session_data[ $key ] = $object;

            WC()->session->set( 'partialcod', $session_data );
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

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

                return WOOPCD_PartialCOD_Premium_Cart::get_data( array( 'source' => $source ), $cart );
            }

            return self::get_instance()->get_contents_data( array( 'source' => $source ), $cart );
        }

        private function get_contents_data( $cart_data, $cart ) {

            $cart_data[ 'cart_items' ] = array();

            foreach ( $cart->cart_contents as $key => $item ) {

                $cart_data[ 'cart_items' ][ $key ] = $this->get_item( $item );

                $id = $item[ 'product_id' ];
                $v_id = $item[ 'variation_id' ];

                if ( !$id ) {

                    continue;
                }

                if ( $v_id > 0 ) {

                    $product = $this->get_parent_product( $id );

                    if ( !$product ) {

                        continue;
                    }

                    $cart_data[ 'cart_items' ][ $key ][ 'data' ] = $this->get_product_data( $product, $item[ 'data' ], $key );
                } else {

                    $cart_data[ 'cart_items' ][ $key ][ 'data' ] = $this->get_product_data( $item[ 'data' ], null, $key );
                }
            }

            return $this->get_totals_data( $cart_data, $cart );
        }

        private function get_item( $cart_item ) {

            $item = array(
                'product_id' => $cart_item[ 'product_id' ],
                'variation_id' => $cart_item[ 'variation_id' ],
                'quantity' => $cart_item[ 'quantity' ],
            );

            if ( has_filter( 'woopcd_partialcod/get-cart-item' ) ) {

                $item = apply_filters( 'woopcd_partialcod/get-cart-item', $item, $cart_item );
            }

            return $item;
        }

        private function get_product_data( $product, $variation, $item_key ) {

            $data = $this->get_stock_data( array(), $product, $variation );

            //TODO: update filter later
            if ( has_filter( 'woopcd_partialcod/get-cart-product-data' ) ) {

                return apply_filters( 'woopcd_partialcod/get-cart-product-data', $data, $product, $item_key );
            }

            return $data;
        }

        private function get_stock_data( $data, $product, $variation ) {

            $stock_data = array(
                'stock_quantity' => $product->get_stock_quantity(),
            );

            if ( $variation ) {

                $v_stock_quantity = $variation->get_stock_quantity();

                if ( is_numeric( $v_stock_quantity ) ) {

                    $stock_data[ 'stock_quantity' ] = $v_stock_quantity;
                }
            }

            return $this->get_tax_data( $this->combine_el( $data, $stock_data ), $product, $variation );
        }

        private function get_tax_data( $data, $product, $variation ) {

            $tax_data = array(
                'is_taxable' => $product->is_taxable(),
                'tax_status' => $product->get_tax_status(),
                'tax_class' => $product->get_tax_class(),
            );

            if ( $variation ) {

                $tax_data[ 'tax_class' ] = $variation->get_tax_class();
            }

            return $this->get_shipping_class( $this->combine_el( $data, $tax_data ), $product, $variation );
        }

        private function get_shipping_class( $data, $product, $variation ) {

            $shipping_class_id = $product->get_shipping_class_id();


            if ( $variation ) {

                $v_shipping_class_id = $variation->get_shipping_class_id();

                if ( $v_shipping_class_id ) {

                    $shipping_class_id = $v_shipping_class_id;
                }
            }

            $data[ 'shipping_class_id' ] = $shipping_class_id;

            return $this->get_weight( $data, $product, $variation );
        }

        private function get_weight( $data, $product, $variation ) {

            $weight = $product->get_weight();

            if ( $variation ) {

                $v_weight = $variation->get_weight();

                if ( !empty( $v_weight ) ) {

                    $weight = $v_weight;
                }
            }

            $data[ 'weight' ] = $weight;

            return $this->get_terms( $data, $product, $variation );
        }

        private function get_terms( $data, $product, $variation ) {

            $terms_data = array(
                'category_ids' => $product->get_category_ids(),
            );

            return $this->get_attributes( $this->combine_el( $data, $terms_data ), $product, $variation );
        }

        private function get_attributes( $data, $product, $variation ) {

            $attrs = $this->get_simple_attribute_ids( $product );

            if ( $variation ) {

                $attrs = $this->get_variation_attribute_ids( $product, $variation );
            }

            $data[ 'attribute_ids' ] = $attrs;

            return $data;
        }

        private function get_simple_attribute_ids( $product ) {

            $db_attrs = $product->get_attributes();

            $attrs = array();

            foreach ( $db_attrs as $db_attr ) {

                foreach ( $db_attr->get_options() as $attr_id ) {

                    $attrs[] = $attr_id;
                }
            }

            return $attrs;
        }

        private function get_variation_attribute_ids( $product, $variation ) {

            $attrs = array();

            if ( !$product ) {

                return $attrs;
            }

            if ( !$variation ) {

                return $attrs;
            }

            $db_attrs = $product->get_attributes();

            $variation_terms = array();

            foreach ( $variation->get_attributes() as $v_taxonomy => $v_slug ) {

                $variation_terms[] = $v_taxonomy . ':' . $v_slug;
            }

            $terms = array();

            foreach ( $db_attrs as $db_attr ) {

                if ( !method_exists( $db_attr, 'get_terms' ) ) {

                    continue;
                }

                foreach ( $db_attr->get_terms() as $db_term ) {

                    $terms[ $db_term->term_id ] = $db_term->taxonomy . ':' . $db_term->slug;
                }
            }

            foreach ( $terms as $key => $term ) {

                if ( !in_array( $term, $variation_terms ) ) {

                    continue;
                }

                $attrs[] = $key;
            }

            return $attrs;
        }

        private function get_totals_data( $cart_data, $cart ) {

            $totals_data = array();

            $cart_totals = $cart->get_totals();

            $totals_data[ 'subtotal' ] = $cart_totals[ 'subtotal' ];
            $totals_data[ 'subtotal_tax' ] = $cart_totals[ 'subtotal_tax' ];

            $cart_data[ 'totals' ] = $totals_data;

            if ( has_filter( 'woopcd_partialcod/get-cart-totals' ) ) {

                $cart_data[ 'totals' ] = apply_filters( 'woopcd_partialcod/get-cart-totals', $cart_data[ 'totals' ], $cart_data[ 'source' ], $cart );
            }

            return $this->get_applied_coupons( $cart_data, $cart );
        }

        private function get_applied_coupons( $cart_data, $cart ) {

            $cart_data[ 'partialcod_coupons' ] = array();
            $cart_data[ 'applied_coupons' ] = array();


            $coupons = WC()->session->get( 'applied_coupons', array() );

            $partialcod = WOOPCD_PartialCOD_Cart::get_session_data( 'coupons', array() );


            foreach ( $coupons as $coupon ) {

                if ( in_array( $coupon, $partialcod ) ) {

                    $cart_data[ 'partialcod_coupons' ][] = $coupon;
                }

                $cart_coupon = array(
                    'coupon_code' => $coupon,
                );

                $cart_data[ 'applied_coupons' ][] = $cart_coupon;
            }

            if ( has_filter( 'woopcd_partialcod/get-cart-applied-coupons' ) ) {

                $cart_data[ 'applied_coupons' ] = apply_filters( 'woopcd_partialcod/get-cart-applied-coupons', $cart_data[ 'applied_coupons' ], $cart_data[ 'source' ], $cart );
            }

            return $this->get_shipping_rate( $cart_data, $cart );
        }

        private function get_shipping_rate( $cart_data, $cart ) {

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
                            $instance_id = $shipping_rate->get_instance_id();
                            $method_id = $shipping_rate->get_method_id();
                            $zone_id = 0;

                            $zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $instance_id );
                            
                            if ( method_exists( $zone, 'get_id' ) ) {

                                $zone_id = $zone->get_id();
                            }

                            $cart_data[ 'shipping_rates' ][] = array(
                                'id' => $rate_id,
                                'method_id' => $method_id,
                                'instance_id' => $instance_id,
                                'zone_id' => $zone_id,
                            );
                        }
                    }
                }

                $cnt++;
            }


            if ( has_filter( 'woopcd_partialcod/get-cart-shipping-rates' ) ) {

                $cart_data[ 'shipping_rates' ] = apply_filters( 'woopcd_partialcod/get-cart-shipping-rates', $cart_data[ 'shipping_rates' ], $cart_data[ 'source' ], $cart );
            }

            return $this->get_payment_method( $cart_data, $cart );
        }

        private function get_payment_method( $cart_data, $cart ) {

            if ( class_exists( 'WOOPCD_PartialCOD_Cart_Session' ) ) {

                $cart_session = WOOPCD_PartialCOD_Cart_Session::get_instance();

                $cart_data[ 'method_ids' ] = array(
                    'method_id' => $cart_session->get_payment_method_id()
                );
            } else {

                $cart_data[ 'method_ids' ] = array(
                    'method_id' => WC()->session->get( 'chosen_payment_method', '' )
                );
            }

            if ( has_filter( 'woopcd_partialcod/get-cart-method-ids' ) ) {

                $cart_data[ 'method_ids' ] = apply_filters( 'woopcd_partialcod/get-cart-method-ids', $cart_data[ 'method_ids' ], $cart_data[ 'source' ], $cart );
            }

            return $this->get_customer( $cart_data, $cart );
        }

        private function get_customer( $cart_data, $cart ) {

            $cart_data[ 'customer' ] = array();

            $customer = WC()->session->get( 'customer', false );

            if ( $customer ) {

                $cart_data[ 'customer' ] = array(
                    'id' => $customer[ 'id' ],
                    'email' => $customer[ 'email' ],
                );
            }

            if ( has_filter( 'woopcd_partialcod/get-cart-customer' ) ) {

                $cart_data[ 'customer' ] = apply_filters( 'woopcd_partialcod/get-cart-customer', $cart_data[ 'customer' ], $cart );
            }

            return $cart_data;
        }

        private function get_parent_product( $parent_id ) {

            if ( isset( $this->parent_products[ $parent_id ] ) ) {

                return $this->parent_products[ $parent_id ];
            }

            $this->parent_products[ $parent_id ] = wc_get_product( $parent_id );

            return $this->parent_products[ $parent_id ];
        }

        private function combine_el( $data, $new_data ) {

            foreach ( $new_data as $key => $new_item ) {

                $data[ $key ] = $new_item;
            }

            return $data;
        }

    }

}