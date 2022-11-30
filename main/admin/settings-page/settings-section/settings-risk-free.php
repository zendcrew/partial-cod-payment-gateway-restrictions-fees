<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_RiskFree_Settings' ) ) {

    class WOOPCD_PartialCOD_Admin_RiskFree_Settings {

        public static function init() {
            add_filter( 'woopcd_partialcod-admin/get-settings-section-fields', array( new self, 'get_fields' ), 20 );
        }

        public static function get_fields( $in_fields ) {



            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'any_idssa',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__( 'Partial Payments Settings', 'woopcd-partialcod' ),
                        'desc' => esc_html__( 'Use these settings to control partial paymants basic settings', 'woopcd-partialcod' ),
                    ),
                    array(
                        'id' => 'is_any',
                        'type' => 'textblock',
                        'show_box' => false,
                        'full_width' => true,
                        'center_head' => true,
                        'text' => WOOPCD_PartialCOD_Admin_Page::get_premium_messages(),
                        'width' => '100%',
                        'box_width' => '100%',
                    )
                ),
            );


            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_RiskFree_Settings::init();
}