<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Method_Options_Account_Details' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Method_Options_Account_Details {

        public static function init() {


            add_filter( 'paygeo-admin/method-options/get-bacs-rule-options', array( new self(), 'get_option' ), 20, 2 );
            add_filter( 'paygeo-admin/method-options/get-rule-option-account_details-fields', array( new self(), 'get_fields' ), 10, 2 );
        }

        public static function get_option( $in_options, $method_id ) {

            $in_options[ 'account_details' ] = array(
                'list_title' => esc_html__( 'Order - Account Details (Premium)', 'pgeo-paygeo' ),
                'title' => esc_html__( 'Order - Account Details (Premium)', 'pgeo-paygeo' ),
                'group_id' => 'order',
            );

            return $in_options;
        }

        public static function get_fields( $in_fields, $method_id ) {

            $in_fields[] = array(
                'id' => 'is_any',
                'type' => 'textblock',
                'show_box' => false,
                'text' => PGEO_PayGeo_Admin_Page::get_premium_messages( 'short_message' ),
                'width' => '100%',
                'box_width' => '100%',
            );

            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Method_Options_Account_Details::init();
}