<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD_Cart_Fees' ) ) {
    WOOPCD_PartialCOD_Main::required_paths( dirname( __FILE__ ), array( 'cart-fees.php' ) );

    class WOOPCD_PartialCOD_Cart_Fees {

        private $fee_engine;

        public function __construct() {

            $this->fee_engine = new WOOPCD_PartialCOD_Cart_Fees_Engine();
            add_filter( 'woopcd_partialcod/process-cart-data', array( $this->fee_engine, 'process_cart_data' ), 50, 1 );
        }

        public function apply_fees( $cart_data ) {

            $settings = WOOPCD_PartialCOD::get_option( 'cart_fee_settings', array( 'show_on_cart' => 'no' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return;
            }


            //Add fees to cart
            $fee_keys = array();
            if ( isset( $cart_data[ 'fees' ] ) ) {
                foreach ( $cart_data[ 'fees' ] as $key => $fee ) {
                    $this->apply_fee( $key, $fee );
                    $fee_keys[] = $key;
                }
                $this->set_applied_fees( $fee_keys );
            }
        }

        public function get_taxes( $fee_taxes, $fee_key, $cart_data ) {
            // do not modify wc taxes
            if ( !isset( $cart_data[ 'fees' ][ $fee_key ] ) ) {
                return $fee_taxes;
            }

            // mofify fee taxes
            $fee = $cart_data[ 'fees' ][ $fee_key ];


            $fee_taxes = array(); // return zero tax if not taxable
            $fee = $cart_data[ 'fees' ][ $fee_key ];


            if ( $fee[ 'amount_tax' ] > 0 ) {

                $d_taxes = apply_filters( 'woopcd_partialcod/fee-amount-taxes', $fee[ 'taxes' ], $fee_key, $fee );

                foreach ( $d_taxes as $key => $tax ) {
                    $fee_taxes[ $key ] = wc_add_number_precision_deep( $tax );
                }
            }

            return $fee_taxes;
        }

        public function update_order_fee_item( $item, $fee_key, $cart_data ) {

            // do not modify wc taxes
            if ( !isset( $cart_data[ 'fees' ][ $fee_key ] ) ) {
                return;
            }
            // update item data
            $fee_data = array(
                'id' => $fee_key,
                'type' => 'fee',
                'method_id' => $cart_data[ 'fees' ][ $fee_key ][ 'method_id' ],
            );

            $item->update_meta_data( 'woopcd_partialcod_data', $fee_data );
        }

        public function get_amount_html( $fee_html, $fee_key, $cart_data ) {

            if ( !isset( $cart_data[ 'fees' ][ $fee_key ] ) ) {
                return $fee_html;
            }

            $fee = $cart_data[ 'fees' ][ $fee_key ];

            $fee_amount = apply_filters( 'woopcd_partialcod/fee-amount', $fee[ 'amount' ], $fee_key, $fee );
            $fee_amount_tax = apply_filters( 'woopcd_partialcod/fee-amount-tax', $fee[ 'amount_tax' ], $fee_key, $fee );

            if ( WC()->cart->display_prices_including_tax() ) {

                $fee_html = wc_price( $fee_amount + $fee_amount_tax );

                if ( $fee_amount_tax > 0 && !wc_prices_include_tax() ) {
                    $fee_html .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            } else {

                $fee_html = wc_price( $fee_amount );

                if ( $fee_amount_tax > 0 && wc_prices_include_tax() ) {
                    $fee_html .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            }

            $desc = '';
            if ( '' != $cart_data[ 'fees' ][ $fee_key ][ 'desc' ] ) {

                $desc_content = apply_filters( 'woopcd_partialcod/fee-desc', $cart_data[ 'fees' ][ $fee_key ][ 'desc' ], $fee_key, $fee );

                $desc = '<span class="partialcod-cart-desc partialcod-desc-mv dashicons dashicons-editor-help" title="' . wc_sanitize_tooltip( $desc_content ) . '"></span>';
            }

            return $fee_html . $desc;
        }

        public function get_sort_order( $sort_order, $a, $b, $cart_data ) {

            $sort_count = -200;
            if ( isset( $cart_data[ 'fees' ] ) ) {
                $keys = array_keys( $cart_data[ 'fees' ] );

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

        private function apply_fee( $fee_key, $fee ) {

            $args = array(
                'id' => $fee_key,
                'name' => apply_filters( 'woopcd_partialcod/fee-title', $fee[ 'title' ], $fee_key, $fee ),
                'amount' => apply_filters( 'woopcd_partialcod/fee-amount', $fee[ 'amount' ], $fee_key, $fee ),
                'taxable' => ($fee[ 'amount_tax' ] > 0),
            );

            WC()->cart->fees_api()->add_fee( $args );
        }

        private function set_applied_fees( $fee_keys ) {

            WOOPCD_PartialCOD_Cart::set_session_data( 'fee_keys', $fee_keys );
        }

    }

}