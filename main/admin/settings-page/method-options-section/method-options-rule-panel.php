<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Method_Rule_Panel')) {

    class WOOPCD_PartialCOD_Admin_Method_Rule_Panel {

        public static function init() {
            add_filter('woopcd_partialcod-admin/method-options/get-rule-panels', array(new self(), 'get_Panel'), 10, 2);
            add_filter('woopcd_partialcod-admin/method-options/get-rule-panel-fields', array(new self(), 'get_Panel_fields'), 10, 2);
        }

        public static function get_Panel($in_fields, $method_id) {

            $in_fields[] = array(
                'id' => 'rule_id',
                'type' => 'autoid',
                'autoid' => 'partialcod',
            );
                        
            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'last' => true,
                'fields' => apply_filters('woopcd_partialcod-admin/method-options/get-' . $method_id . '-rule-panel-fields', apply_filters('woopcd_partialcod-admin/method-options/get-rule-panel-fields', $in_fields, $method_id), $method_id),
            );

            return $in_fields;
        }

        public static function get_Panel_fields($in_fields, $method_id) {
            
            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 1,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Adds a private note for reference purposes', 'partial-cod-payment-gateway-restrictions-fees'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Admin Note', 'partial-cod-payment-gateway-restrictions-fees'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'partial-cod-payment-gateway-restrictions-fees'),
                        'width' => '100%',
                    ),
                ),
            );
            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Method_Rule_Panel::init();
}
