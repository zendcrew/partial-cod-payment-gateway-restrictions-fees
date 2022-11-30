<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Cart_Discounts' ) ) {
    WOOPCD_PartialCOD_Main::required_paths( dirname( __FILE__ ), array( 'cart-discounts.php' ) );

    class WOOPCD_PartialCOD_Cart_Discounts {

        private $discount_engine;

        public function __construct() {

            $this->discount_engine = new WOOPCD_PartialCOD_Cart_Discounts_Engine();
            add_filter( 'woopcd_partialcod/process-cart-data', array( $this->discount_engine, 'process_cart_data' ), 40, 1 );
        }

        public function apply_discounts( $cart_data ) {

            $settings = WOOPCD_PartialCOD::get_option( 'cart_discount_settings', array( 'show_on_cart' => 'no' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                $this->remove_prev_coupons( $cart_data );
                return;
            }


            //Add discounts to cart
            $discount_keys = array();
            if ( isset( $cart_data[ 'discounts' ] ) ) {
                foreach ( $cart_data[ 'discounts' ] as $key => $discount ) {
                    if ( 'no' == $discount[ 'apply_as_coupon' ] ) {
                        $this->apply_discount( $key, $discount );
                        $discount_keys[] = $key;
                    } else {
                        $this->apply_coupon( $discount, $cart_data );
                    }
                }
                $this->set_applied_discounts( $discount_keys );
            }
        }

        public function get_taxes( $discount_taxes, $discount_key, $cart_data ) {
            // do not modify wc taxes
            if ( !isset( $cart_data[ 'discounts' ][ $discount_key ] ) ) {
                return $discount_taxes;
            }

            // mofify discount taxes
            $discount = $cart_data[ 'discounts' ][ $discount_key ];


            $discount_taxes = array(); // return zero tax if not taxable
            $discount = $cart_data[ 'discounts' ][ $discount_key ];


            if ( $discount[ 'amount_tax' ] > 0 ) {

                $d_taxes = apply_filters( 'woopcd_partialcod/discount-amount-taxes', $discount[ 'taxes' ], $discount_key, $discount );

                foreach ( $d_taxes as $key => $tax ) {
                    $discount_taxes[ $key ] = 0 - wc_add_number_precision_deep( $tax );
                }
            }

            return $discount_taxes;
        }

        public function update_order_fee_item( $item, $fee_key, $cart_data ) {

            // do not modify wc taxes
            if ( !isset( $cart_data[ 'discounts' ][ $fee_key ] ) ) {
                return;
            }
            // update item data
            $discount_data = array(
                'id' => $fee_key,
                'type' => 'discount',
                'method_id' => $cart_data[ 'discounts' ][ $fee_key ][ 'method_id' ],
            );

            $item->update_meta_data( 'woopcd_partialcod_data', $discount_data );
        }

        public function get_amount_html( $discount_html, $discount_key, $cart_data ) {

            if ( !isset( $cart_data[ 'discounts' ][ $discount_key ] ) ) {
                return $discount_html;
            }

            $discount = $cart_data[ 'discounts' ][ $discount_key ];

            $discount_amount = apply_filters( 'woopcd_partialcod/discount-amount', $discount[ 'amount' ], $discount_key, $discount );
            $discount_amount_tax = apply_filters( 'woopcd_partialcod/discount-amount-tax', $discount[ 'amount_tax' ], $discount_key, $discount );

            if ( WC()->cart->display_prices_including_tax() ) {

                $discount_html = '-' . wc_price( $discount_amount + $discount_amount_tax );

                if ( $discount_amount_tax > 0 && !wc_prices_include_tax() ) {
                    $discount_html .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            } else {

                $discount_html = '-' . wc_price( $discount_amount );

                if ( $discount_amount_tax > 0 && wc_prices_include_tax() ) {
                    $discount_html .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            }

            $desc = '';
            if ( '' != $cart_data[ 'discounts' ][ $discount_key ][ 'desc' ] ) {

                $desc_content = apply_filters( 'woopcd_partialcod/discount-desc', $cart_data[ 'discounts' ][ $discount_key ][ 'desc' ], $discount_key, $discount );

                $desc = '<span class="partialcod-cart-desc partialcod-desc-mv dashicons dashicons-editor-help" title="' . wc_sanitize_tooltip( $desc_content ) . '"></span>';
            }
            return $discount_html . $desc;
        }

        public function get_sort_order( $sort_order, $a, $b, $cart_data ) {

            $sort_count = -400;
            if ( isset( $cart_data[ 'discounts' ] ) ) {
                $keys = array_keys( $cart_data[ 'discounts' ] );

                foreach ( $keys as $key ) {

                    if ( $a->id == $key ) {
                        $sort_order[ 'a' ] = $sort_count;
                    }

                    if ( $b->id == $key ) {
                        $sort_order[ 'b' ] = $sort_count;
                    }

                    $sort_count++;
                }
            }
            return $sort_order;
        }

        public function get_coupon_data( $coupon_code, $cart_data ) {

            $discount = array();

            if ( isset( $cart_data[ 'discounts' ] ) ) {

                foreach ( $cart_data[ 'discounts' ] as $key => $disc ) {

                    if ( $coupon_code == $disc[ 'coupon_code' ] ) {
                        $discount = $disc;
                        $discount[ 'key' ] = $key;
                        break;
                    }
                }
            }

            if ( isset( $discount[ 'coupon_code' ] ) && $coupon_code == $discount[ 'coupon_code' ] ) {
                return array(
                    'code' => $coupon_code,
                    'amount' => apply_filters( 'woopcd_partialcod/discount-coupon-amount', $discount[ 'amount' ], $discount[ 'coupon_code' ], $discount[ 'key' ], $discount ),
                    'discount_type' => 'fixed_cart',
                    'individual_use' => false,
                    'product_ids' => array(),
                    'exclude_product_ids' => array(),
                    'usage_limit' => '',
                    'usage_limit_per_user' => '',
                    'limit_usage_to_x_items' => '',
                    'usage_count' => '',
                    'expiry_date' => '',
                    'free_shipping' => false,
                    'product_categories' => array(),
                    'exclude_product_categories' => array(),
                    'exclude_sale_items' => false,
                    'minimum_amount' => '',
                    'maximum_amount' => '',
                    'customer_email' => array(),
                    'virtual' => true,
                );
            }

            return array();
        }

        public function get_coupon_label( $coupon_code, $cart_data ) {


            $settings = WOOPCD_PartialCOD::get_option( 'cart_discount_settings', array( 'replace_coupon_labels' => 'no' ) );

            if ( 'no' == $settings[ 'replace_coupon_labels' ] ) {
                return '';
            }


            $discount = array();


            if ( isset( $cart_data[ 'discounts' ] ) ) {
                foreach ( $cart_data[ 'discounts' ] as $key => $disc ) {
                    if ( 'yes' == $disc[ 'apply_as_coupon' ] && $coupon_code == $disc[ 'coupon_code' ] ) {
                        $discount = $disc;
                        $discount[ 'key' ] = $key;
                        break;
                    }
                }
            }


            if ( isset( $discount[ 'title' ] ) ) {

                $title = apply_filters( 'woopcd_partialcod/discount-coupon-title', $discount[ 'title' ], $discount[ 'coupon_code' ], $discount[ 'key' ], $discount );

                if ( isset( $discount[ 'desc' ] ) && $discount[ 'desc' ] != '' ) {

                    $desc = apply_filters( 'woopcd_partialcod/discount-coupon-desc', $discount[ 'desc' ], $discount[ 'coupon_code' ], $discount[ 'key' ], $discount );

                    $title = $title . '<span class="partialcod-cart-desc dashicons dashicons-editor-help" title="' . wc_sanitize_tooltip( $desc ) . '"></span>';
                }

                return $title;
            }
            return '';
        }

        public function get_coupon_html( $discount_amount_html, $coupon_code, $cart_data ) {

            $found = false;

            if ( isset( $cart_data[ 'discounts' ] ) ) {
                foreach ( $cart_data[ 'discounts' ] as $key => $disc ) {
                    if ( 'yes' == $disc[ 'apply_as_coupon' ] && $coupon_code == $disc[ 'coupon_code' ] ) {
                        $found = true;
                        break;
                    }
                }
            }

            if ( true == $found ) {

                if ( WC()->cart->display_prices_including_tax() ) {

                    if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                        $discount_amount_html .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                    }
                } else {
                    if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                        $discount_amount_html .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                    }
                }


                return $discount_amount_html;
            }
            return '';
        }

        private function apply_discount( $discount_key, $discount ) {

            $args = array(
                'id' => $discount_key,
                'name' => apply_filters( 'woopcd_partialcod/discount-title', $discount[ 'title' ], $discount_key, $discount ),
                'amount' => 0 - apply_filters( 'woopcd_partialcod/discount-amount', $discount[ 'amount' ], $discount_key, $discount ),
                'taxable' => ($discount[ 'amount_tax' ] > 0),
            );

            WC()->cart->fees_api()->add_fee( $args );
        }

        private function apply_coupon( $discount, $cart_data ) {

            if ( !$this->is_coupon_applied( $discount[ 'coupon_code' ], $cart_data ) ) {
                WC()->cart->add_discount( $discount[ 'coupon_code' ] );
                $this->set_applied_counpons( $discount[ 'coupon_code' ] );
            }
        }

        private function is_coupon_applied( $coupon_code, $cart_data ) {
            $coupons = array();
            foreach ( $cart_data[ 'wc' ][ 'applied_coupons' ] as $coupon ) {
                $coupons[] = $coupon[ 'coupon_code' ];
            }

            return in_array( $coupon_code, $coupons );
        }

        private function set_applied_counpons( $coupon_code ) {
            $coupons = WOOPCD_PartialCOD_Cart::get_session_data( 'coupons', array() );
            $coupons[] = $coupon_code;
            WOOPCD_PartialCOD_Cart::set_session_data( 'coupons', $coupons );
        }

        private function set_applied_discounts( $discount_keys ) {
            WOOPCD_PartialCOD_Cart::set_session_data( 'discount_keys', $discount_keys );
        }

        private function remove_prev_coupons( $cart_data ) {

            if ( isset( $cart_data[ 'discounts' ] ) ) {

                foreach ( $cart_data[ 'discounts' ] as $key => $discount ) {
                    if ( 'yes' == $discount[ 'apply_as_coupon' ] ) {
                        $partialcod_coupons[] = $discount[ 'coupon_code' ];
                        WC()->cart->remove_coupon( $discount[ 'coupon_code' ] );
                    }
                }
            }
        }

    }

}
