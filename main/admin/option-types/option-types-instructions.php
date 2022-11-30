<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Method_Options_Instructions' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Admin_Method_Options_Instructions {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/method-options/get-rule-option-groups', array( new self(), 'get_groups' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/method-options/get-rule-options', array( new self(), 'get_option' ), 10, 2 );

            if ( !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                add_filter( 'woopcd_partialcod-admin/method-options/get-rule-option-instructions-fields', array( new self(), 'get_fields' ), 10, 2 );
            }
        }

        public static function get_groups( $in_groups, $method_id ) {
            $in_groups[ 'order' ] = esc_html__( 'Order Settings', 'woopcd-partialcod' );
            return $in_groups;
        }

        public static function get_option( $in_options, $method_id ) {

            $in_options[ 'instructions' ] = array(
                'list_title' => esc_html__( 'Order - Instructions (Premium)', 'woopcd-partialcod' ),
                'title' => esc_html__( 'Order - Instructions (Premium)', 'woopcd-partialcod' ),
                'group_id' => 'order',
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

    WOOPCD_PartialCOD_Admin_Method_Options_Instructions::init();
}