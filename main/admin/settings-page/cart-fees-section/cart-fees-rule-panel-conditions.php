<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Fee_Rule_Panel_Conditions')) {

    class WOOPCD_PartialCOD_Admin_Fee_Rule_Panel_Conditions {

        public static function init() {
            add_filter('woopcd_partialcod-admin/cart-fees/get-rule-panels', array(new self(), 'get_Panel'), 60, 2);
        }

        public static function get_Panel($in_fields, $method_id) {

            $args = array(
                'method_id' => $method_id,
                'module' => 'cart-fees',
                'sub_module' => false,
            );

            $in_fields[] = array(
                'id' => 'rule_conditions',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => true,
                'last' => true,
                'fields' => apply_filters('woopcd_partialcod-admin/get-rule-panel-conditions-fields', array(), $args),
            );

            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Fee_Rule_Panel_Conditions::init();
}