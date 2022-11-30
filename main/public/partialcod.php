<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WOOPCD_PartialCOD' ) ) {

    require_once dirname( __FILE__ ) . '/utils/utils.php';
    require_once dirname( __FILE__ ) . '/partialcod-cart.php';

    require_once dirname( __FILE__ ) . '/modules/method-options/method-options.php';
    require_once dirname( __FILE__ ) . '/modules/cart-discounts/cart-discounts.php';
    require_once dirname( __FILE__ ) . '/modules/cart-fees/cart-fees.php';

    require_once dirname( __FILE__ ) . '/condition-types/condition-types.php';
    require_once dirname( __FILE__ ) . '/amount-types/amount-types.php';
    require_once dirname( __FILE__ ) . '/option-types/option-types.php';

    require_once dirname( __FILE__ ) . '/cart-total-types/cart-total-types.php';

    class WOOPCD_PartialCOD {

        private $cart_discounts;
        private $cart_fees;
        private $method_options;
        private static $woopcd_partialcod = array();
        private static $cart_data_list = array();

        public function __construct() {

            $this->cart_discounts = new WOOPCD_PartialCOD_Cart_Discounts();
            $this->cart_fees = new WOOPCD_PartialCOD_Cart_Fees();
            $this->method_options = new WOOPCD_PartialCOD_Method_Options();

            //============================================
            // Some objects needs initialization from here
            //============================================
            new WOOPCD_PartialCOD_Conditions_Cart_Totals();


            //========================
            // Cart discounts and fees
            //========================
            add_action( 'woocommerce_cart_calculate_fees', array( $this, 'calculate_fees' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 1 );
            add_filter( 'woocommerce_cart_totals_get_fees_from_cart_taxes', array( $this, 'get_fee_taxes' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 3 );
            add_filter( 'woocommerce_cart_totals_fee_html', array( $this, 'cart_totals_fee_html' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 2 );
            add_filter( 'woocommerce_sort_fees_callback', array( $this, 'sort_fees_callback' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 3 );

            //====================
            // Cart discounts only
            //====================
            add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'get_coupon_data' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 2 );
            add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'get_coupon_label' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 2 );
            add_filter( 'woocommerce_cart_totals_coupon_html', array( $this, 'get_coupon_html' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 3 );



            add_filter( 'woocommerce_available_payment_gateways', array( $this, 'get_available_gateways' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 1 );
            add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 3 );
            add_action( 'woocommerce_checkout_create_order_fee_item', array( $this, 'create_order_fee_item' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 4 );

            add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'update_order_review_fragments' ), WOOPCD_PARTIALCOD_HOOKS_INDEX - 1, 1 );
            add_action( 'woocommerce_checkout_order_review', array( $this, 'checkout_order_review' ), WOOPCD_PARTIALCOD_HOOKS_INDEX );


            add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'rest_prepare_shop_order_object' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 3 );
            add_filter( 'woocommerce_rest_prepare_shop_order', array( $this, 'rest_prepare_shop_order' ), WOOPCD_PARTIALCOD_HOOKS_INDEX, 3 );
        }

        public function calculate_fees( $cart ) {

            if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
                return;
            }

            $this->pre_load_gateways();

            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            // Process cart discounts
            $this->cart_discounts->apply_discounts( $cart_data );

            // Process cart fees
            $this->cart_fees->apply_fees( $cart_data );


            // Process notifications

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                WOOPCD_PartialCOD_Notifications::process_messages( $cart_data );
            }
        }

        public function get_coupon_data( $coupon_data, $coupon_code = '' ) {

            $this->pre_load_gateways();

            if ( is_admin() ) {
                return $coupon_data;
            }

            // check if discount is enable on cart page
            $settings = WOOPCD_PartialCOD::get_option( 'cart_discount_settings', array( 'show_on_cart' => 'no' ) );
            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return $coupon_data;
            }


            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            // return the coupon data if available
            $cp_data = $this->cart_discounts->get_coupon_data( $coupon_code, $cart_data );

            if ( count( $cp_data ) > 0 ) {
                return $cp_data;
            }


            return $coupon_data;
        }

        public function get_coupon_label( $label, $coupon ) {

            $this->pre_load_gateways();

            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            $coupon_code = '';
            if ( is_array( $coupon ) ) {
                $coupon_code = $coupon[ 'code' ];
            } else {
                $coupon_code = $coupon->get_code();
            }

            // return the coupon label if available
            $cp_label = $this->cart_discounts->get_coupon_label( $coupon_code, $cart_data );

            if ( '' != $cp_label ) {
                return $cp_label;
            }


            return $label;
        }

        public function get_coupon_html( $coupon_html, $coupon, $discount_amount_html ) {

            $this->pre_load_gateways();

            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            $coupon_code = '';
            if ( is_array( $coupon ) ) {
                $coupon_code = $coupon[ 'code' ];
            } else {
                $coupon_code = $coupon->get_code();
            }

            // return the coupon html if available
            $cp_html = $this->cart_discounts->get_coupon_html( $discount_amount_html, $coupon_code, $cart_data );

            if ( '' != $cp_html ) {
                return $cp_html;
            }

            return $coupon_html;
        }

        public function get_fee_taxes( $fee_taxes, $fee, $cart_totals ) {

            $this->pre_load_gateways();

            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            // apply discount taxes (if applicable)
            $fee_ts = $this->cart_discounts->get_taxes( $fee_taxes, $fee->object->id, $cart_data );

            // apply fee taxes (if applicable)
            return $this->cart_fees->get_taxes( $fee_ts, $fee->object->id, $cart_data );
        }

        public function cart_totals_fee_html( $cart_totals_fee_html, $fee ) {

            $this->pre_load_gateways();

            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            // apply total discount html (if applicable)
            $cart_totals_html = $this->cart_discounts->get_amount_html( $cart_totals_fee_html, $fee->id, $cart_data );

            // apply total fee html (if applicable)
            return $this->cart_fees->get_amount_html( $cart_totals_html, $fee->id, $cart_data );
        }

        public function sort_fees_callback( $a_g_b, $a, $b ) {

            if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
                return;
            }

            $this->pre_load_gateways();

            $sort_order = array( 'a' => 0, 'b' => 0 );

            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );


            // get discounts sort order (if applicable)
            $d_sort_order = $this->cart_discounts->get_sort_order( $sort_order, $a, $b, $cart_data );

            // get fees sort order (if applicable)
            $f_sort_order = $this->cart_fees->get_sort_order( $d_sort_order, $a, $b, $cart_data );



            // apply sort orders
            if ( $f_sort_order[ 'a' ] < $f_sort_order[ 'b' ] ) {
                return -1;
            }
            if ( $f_sort_order[ 'b' ] < $f_sort_order[ 'a' ] ) {
                return 1;
            }


            return $a_g_b;
        }

        public function get_available_gateways( $gateways ) {

            if ( is_admin() ) {
                return $gateways;
            }
            
            $this->store_available_gateways( $gateways );
            
            $methods = array();

            // Returns order available methods
            if ( is_checkout_pay_page() ) {
                return $this->get_order_available_gateways( $gateways );
            }

            if ( !is_checkout() && !is_cart() ) {
                return $gateways;
            }

            // Get cart data
            $cart_data = self::process_cart_data( 'gateways' );

            // Apply method options;
            $keys = array_keys( $gateways );

            foreach ( $keys as $key ) {

                $method = $this->method_options->apply_options( $gateways[ $key ], $cart_data );
                if ( 'yes' == $method->enabled ) {
                    $methods[ $key ] = $method;
                }
            }


            // applies premium features
            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                $methods = WOOPCD_PartialCOD_Premium::get_available_gateways( $methods, $cart_data );
            }

            if ( !defined( 'PARTIALCOD_GATEWAYS_LOADED' ) ) {
                define( 'PARTIALCOD_GATEWAYS_LOADED', true );
            }

            return $methods;
        }

        public function checkout_order_processed( $order_id, $posted_data, $order ) {

            // Get cart data
            $methods = WC()->payment_gateways->get_available_payment_gateways();


            if ( count( $methods ) ) {
                $method_ids = array_keys( $methods );

                if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                    $method_ids = WOOPCD_PartialCOD_Premium::get_order_available_methods( $method_ids );
                    $this->method_options->add_order_method_ids( $order_id, $method_ids );
                    WOOPCD_PartialCOD_Premium::checkout_order_processed( $order_id, $posted_data, $order );
                } else {
                    $this->method_options->add_order_method_ids( $order_id, $method_ids );
                }
            }
        }

        public function create_order_fee_item( $item, $fee_key, $fee, $order ) {
            // Get cart data
            $cart_data = self::process_cart_data( 'fees_discounts' );

            //updpate discount item
            $this->cart_discounts->update_order_fee_item( $item, $fee_key, $cart_data );

            //updpate fee item
            $this->cart_fees->update_order_fee_item( $item, $fee_key, $cart_data );
        }

        public function update_order_review_fragments( $fragments ) {
            ob_start();
            // Get cart data
            $cart_data = self::process_cart_data( 'gateways' );

            // Get messages
            $messages = $this->method_options->get_messages( $cart_data );

            if ( count( $messages ) > 0 ) {

                include 'views/cart-messages.php';
                $html = ob_get_clean();
                $fragments[ '#partialcod_msgs' ] = $html;
            }


            return $fragments;
        }

        public function checkout_order_review() {
            // Get cart data
            $cart_data = self::process_cart_data( 'gateways' );

            // Get messages
            $messages = $this->method_options->get_messages( $cart_data );

            //Output messages
            if ( count( $messages ) > 0 ) {
                include 'views/cart-messages.php';
            }
        }

        public function rest_prepare_shop_order_object( $response, $object, $request ) {
            if ( is_admin() ) {
                return $response;
            }

            $response = $this->method_options->prepare_rest_data( $response );

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                $response = WOOPCD_PartialCOD_Premium::rest_prepare( $response );
            }

            return $response;
        }

        public function rest_prepare_shop_order( $response, $post, $request ) {

            if ( is_admin() ) {
                return $response;
            }

            $response = $this->method_options->prepare_rest_data( $response );

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                $response = WOOPCD_PartialCOD_Premium::rest_prepare( $response );
            }

            return $response;
        }

        public static function get_option( $option_key, $default ) {

            if ( isset( self::$woopcd_partialcod[ $option_key ] ) ) {
                return self::$woopcd_partialcod[ $option_key ];
            }

            self::$woopcd_partialcod = get_option( 'woopcd_partialcod', array() );

            if ( isset( self::$woopcd_partialcod[ $option_key ] ) ) {
                return self::$woopcd_partialcod[ $option_key ];
            }

            return $default;
        }

        public static function get_scrip_params() {

            $cart_triggers = array( 'input[name^="payment_method"]' );

            if ( has_filter( 'woopcd_partialcod/get-update-triggers' ) ) {
                $cart_triggers = apply_filters( 'woopcd_partialcod/get-update-triggers', $cart_triggers );
            }

            $script_params = array(
                'update_triggers' => implode( ",", $cart_triggers ),
            );

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                return WOOPCD_PartialCOD_Premium::get_scrip_params( $script_params );
            }

            return $script_params;
        }

        public static function process_cart_data( $source, $cart = null ) {

            if ( isset( self::$cart_data_list[ $source ] ) ) {
                return self::$cart_data_list[ $source ];
            }

            if ( method_exists( WC()->cart, 'is_empty' ) && WC()->cart->is_empty() ) {
                return array();
            }

            // Create cart object (if NOT already created)
            if ( $cart == null ) {
                $cart = WC()->cart;
            }

            if ( !$cart ) {
                return array();
            }

            // Pre-process all data
            $cart_data = array(
                'wc' => WOOPCD_PartialCOD_Cart::get_data( $source, $cart )
            );

            // Re-call previously processed data
            foreach ( self::$cart_data_list as $prev_datas ) {
                foreach ( $prev_datas as $key => $prev_data ) {
                    if ( $key != 'wc' ) {
                        $cart_data[ $key ] = $prev_data;
                    }
                }
            }

            // Process all data

            self::$cart_data_list[ $source ] = apply_filters( 'woopcd_partialcod/process-cart-data', $cart_data );


            // Return all processed data
            return self::$cart_data_list[ $source ];
        }

        private function get_order_available_gateways( $gateways ) {
            global $wp;

            $methods = array();

            if ( isset( $wp->query_vars[ 'order-pay' ] ) ) {
                $order_id = $wp->query_vars[ 'order-pay' ];
            }
            $method_ids = array();
            if ( $order_id ) {
                $method_ids = $this->method_options->get_order_method_ids( $order_id );
            }

            if ( !count( $method_ids ) ) {
                return $gateways;
            }

            foreach ( $method_ids as $method_id ) {
                if ( isset( $gateways[ $method_id ] ) ) {
                    $methods[ $method_id ] = $gateways[ $method_id ];
                }
            }
            if ( count( $method_ids ) ) {
                return $methods;
            }
            return $gateways;
        }

        private function pre_load_gateways() {

            if ( defined( 'PARTIALCOD_GATEWAYS_LOADED' ) ) {
                return;
            }

            WC()->payment_gateways()->get_available_payment_gateways();
        }

        private function store_available_gateways( $gateways ) {

            if ( !count( $gateways ) ) {

                return;
            }

            $methods = array();

            foreach ( $gateways as $gateway_id => $gateway ) {

                $methods[ $gateway_id ][ 'id' ] = $gateway->id;
                $methods[ $gateway_id ][ 'plugin_id' ] = $gateway->plugin_id;
                $methods[ $gateway_id ][ 'method_title' ] = $gateway->method_title;
                $methods[ $gateway_id ][ 'title' ] = $gateway->title;
                $methods[ $gateway_id ][ 'order_button_text' ] = $gateway->order_button_text;
                $methods[ $gateway_id ][ 'icon' ] = $gateway->icon;
                $methods[ $gateway_id ] = apply_filters( 'woopcd_partialcod-admin/get-method-' . $gateway_id . '-props', $methods[ $gateway_id ], $gateway );
            }

            $stored_methods = get_option( 'woopcd_partialcod_methods', array() );


            $stored_hash = md5( wp_json_encode( $stored_methods ) );

            $hash = md5( wp_json_encode( $methods ) );

            if ( $hash != $stored_hash ) {

                update_option( 'woopcd_partialcod_methods', $methods );
            }
        }

    }

    new WOOPCD_PartialCOD();
}

