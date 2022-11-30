<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Fee_Rule_Panel')) {

    class WOOPCD_PartialCOD_Admin_Fee_Rule_Panel {

        public static function init() {
            add_filter('woopcd_partialcod-admin/cart-fees/get-rule-panels', array(new self(), 'get_Panel'), 10, 2);
            add_filter('woopcd_partialcod-admin/cart-fees/get-rule-panel-fields', array(new self(), 'get_Panel_fields'), 10, 2);
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
                'fields' => apply_filters('woopcd_partialcod-admin/cart-fees/get-' . $method_id . '-rule-panel-fields', apply_filters('woopcd_partialcod-admin/cart-fees/get-rule-panel-fields', array(), $method_id), $method_id),
            );

            return $in_fields;
        }

        public static function get_Panel_fields($in_fields, $method_id) {
            return apply_filters('woopcd_partialcod-admin/cart-fees/get-' . $method_id . '-rule-panel-option-fields', apply_filters('woopcd_partialcod-admin/cart-fees/get-rule-panel-option-fields', $in_fields, $method_id), $method_id);
        }

    }

    WOOPCD_PartialCOD_Admin_Fee_Rule_Panel::init();
}
