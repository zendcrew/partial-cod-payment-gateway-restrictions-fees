<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Method_Rule_Options_Description' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Method_Rule_Options_Description {

        public static function init() {
            add_filter( 'paygeo-admin/method-options/get-rule-options', array( new self(), 'get_option' ), 40, 2 );
            add_filter( 'paygeo-admin/method-options/get-rule-option-description-fields', array( new self(), 'get_fields' ), 10, 2 );
        }

        public static function get_option( $in_options, $method_id ) {

            $in_options[ 'description' ] = array(
                'list_title' => esc_html__( 'Checkout - Description (Premium)', 'pgeo-paygeo' ),
                'title' => esc_html__( 'Checkout - Description (Premium)', 'pgeo-paygeo' ),
                'group_id' => 'checkout',
                'tooltip' => esc_html__( 'Controls payment method description on checkout page', 'pgeo-paygeo' ),
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

    PGEO_PayGeo_Admin_Method_Rule_Options_Description::init();
}

