<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Discount_Rule_Panel_Conditions')) {

    class PGEO_PayGeo_Admin_Discount_Rule_Panel_Conditions {

        public static function init() {
            add_filter('paygeo-admin/cart-discounts/get-rule-panels', array(new self(), 'get_Panel'), 60, 2);
        }

        public static function get_Panel($in_fields, $method_id) {

            $args = array(
                'method_id' => $method_id,
                'module' => 'cart-discounts',
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
                'fields' => apply_filters('paygeo-admin/get-rule-panel-conditions-fields', array(), $args),
            );

            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Discount_Rule_Panel_Conditions::init();
}

