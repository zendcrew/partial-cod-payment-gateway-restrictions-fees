<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'PGEO_PayGeo_Method_Options' ) ) {
    PGEO_PayGeo_Extension::required_paths( dirname( __FILE__ ), array( 'method-options.php' ) );

    class PGEO_PayGeo_Method_Options {

        private $options_engine;

        public function __construct() {

            $this->options_engine = new PGEO_PayGeo_Method_Options_Engine();

            add_filter( 'paygeo/process-cart-data', array( $this->options_engine, 'process_cart_data' ), 20, 1 );
        }

        public function apply_options( $method, $cart_data ) {

            $method_options = $this->get_method_options( $cart_data, $method->id );

            foreach ( $method_options as $option_id => $option ) {

                // allows other plugins to modify option data before applying them
                if ( has_filter( 'paygeo/method-option' ) ) {
                    $option = apply_filters( 'paygeo/method-option', $option, $option_id );
                }

                // allows other plugins to apply their stuff
                $method = apply_filters( 'paygeo/apply-' . $option[ 'option_type' ] . '-method-option', $method, $option );
            }


            return $method;
        }

        public function get_messages( $cart_data ) {

            $options = array();

            if ( isset( $cart_data[ 'options' ] ) ) {

                foreach ( $cart_data[ 'options' ] as $rule_id => $opt ) {

                    $options = $this->get_options_from_rule( $options, $opt, '', $rule_id );
                }
            }

            $m_messages = array();
            foreach ( $options as $option_id => $option ) {

                $m_messages = apply_filters( 'paygeo/get-' . $option[ 'option_type' ] . '-messages', $m_messages, $option, $option_id );
            }


            $messages = array();

            foreach ( $m_messages as $m_message ) {
                $messages[ $m_message[ 'option_id' ] ] = $m_message[ 'message' ];
            }


            // allows other plugins to modify payment method messages
            if ( has_filter( 'paygeo/apply-method-messages' ) ) {
                $messages = apply_filters( 'paygeo/apply-method-messages', $messages );
            }

            return $messages;
        }

        public function add_order_method_ids( $order_id, $method_ids ) {
            update_post_meta( $order_id, 'pgeo_paygeo_methods', $method_ids );
        }

        public function get_order_method_ids( $order_id ) {

            $method_ids = get_post_meta( $order_id, 'pgeo_paygeo_methods', true );
            if ( $method_ids ) {
                return $method_ids;
            }
            return array();
        }

        public function prepare_rest_data( $response ) {


            $data = $response->get_data();
            $pgeo_paygeo_methods = array();

            //get the available method ids from meta
            if ( isset( $data[ 'meta_data' ] ) ) {
                $meta_data_list = array();
                foreach ( $data[ 'meta_data' ] as $key => $meta_data ) {
                    if ( 'pgeo_paygeo_methods' == $meta_data->key ) {
                        $pgeo_paygeo_methods = $meta_data->value;
                        continue;
                    }
                    $meta_data_list[] = $meta_data;
                }
                $data[ 'meta_data' ] = $meta_data_list;
            }

            //get the available method ids from order metadata
            if ( !count( $pgeo_paygeo_methods ) && isset( $data[ 'id' ] ) ) {
                $order_id = $data[ 'id' ];

                $paygeo_methods = get_post_meta( $order_id, 'pgeo_paygeo_methods', true );
                if ( $paygeo_methods ) {
                    $pgeo_paygeo_methods = $paygeo_methods;
                }
            }

            //add the available method ids to order keys
            if ( count( $pgeo_paygeo_methods ) ) {
                $rest_data = array();
                foreach ( $data as $key => $data_value ) {

                    if ( $key == 'payment_method' ) {
                        $rest_data[ 'pgeo_paygeo_methods' ] = $pgeo_paygeo_methods;
                    }
                    $rest_data[ $key ] = $data_value;
                }
                $response->set_data( $rest_data );
            }

            return $response;
        }

        private function get_method_options( $cart_data, $method_id ) {

            $options = array();

            if ( isset( $cart_data[ 'options' ] ) ) {

                foreach ( $cart_data[ 'options' ] as $rule_id => $rule_options ) {

                    $options = $this->get_options_from_rule( $options, $rule_options, $method_id, $rule_id );
                }
            }
            return $options;
        }

        private function get_options_from_rule( $options, $rule_options, $method_id, $rule_id ) {

            foreach ( $rule_options as $key => $option ) {

                if ( $method_id == $option[ 'method_id' ] || $method_id == '' ) {

                    $option[ 'rule_id' ] = $rule_id;

                    $options[ $key ] = $option;
                }
            }

            return $options;
        }

    }

}