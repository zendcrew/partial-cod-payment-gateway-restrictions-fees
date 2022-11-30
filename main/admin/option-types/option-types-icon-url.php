<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Method_Options_Icon_Url' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Method_Options_Icon_Url {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/method-options/get-rule-options', array( new self(), 'get_option' ), 50, 2 );
            add_filter( 'woopcd_partialcod-admin/method-options/get-rule-option-icon_url-fields', array( new self(), 'get_fields' ), 10, 2 );
        }

        public static function get_option( $in_options, $method_id ) {

            $in_options[ 'icon_url' ] = array(
                'list_title' => esc_html__( 'Checkout - Icon Url (Premium)', 'woopcd-partialcod' ),
                'title' => esc_html__( 'Checkout - Icon Url (Premium)', 'woopcd-partialcod' ),
                'group_id' => 'checkout',
                'tooltip' => esc_html__( 'Controls payment method icon url on checkout page', 'woopcd-partialcod' ),
            );

            return $in_options;
        }

        public static function get_fields( $in_fields, $method_id ) {

            $in_fields[] = array(
                'id' => 'is_any',
                'type' => 'textblock',
                'show_box' => false,
                'text' => WOOPCD_PartialCOD_Admin_Page::get_premium_messages( 'short_message' ),
                'width' => '100%',
                'box_width' => '100%',
            );

            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Method_Options_Icon_Url::init();
}


