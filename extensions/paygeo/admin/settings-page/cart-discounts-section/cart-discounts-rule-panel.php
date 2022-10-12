<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Discount_Rule_Panel')) {

    class PGEO_PayGeo_Admin_Discount_Rule_Panel {

        public static function init() {
            add_filter('paygeo-admin/cart-discounts/get-rule-panels', array(new self(), 'get_Panel'), 10, 2);
            add_filter('paygeo-admin/cart-discounts/get-rule-panel-fields', array(new self(), 'get_Panel_fields'), 10, 2);
        }

        public static function get_Panel($in_fields, $method_id) {

            $in_fields[] = array(
                'id' => 'rule_id',
                'type' => 'autoid',
                'autoid' => 'paygeo',
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
                'fields' => apply_filters('paygeo-admin/cart-discounts/get-' . $method_id . '-rule-panel-fields', apply_filters('paygeo-admin/cart-discounts/get-rule-panel-fields', array(), $method_id), $method_id),
            );

            return $in_fields;
        }

        public static function get_Panel_fields($in_fields, $method_id) {
            return apply_filters('paygeo-admin/cart-discounts/get-' . $method_id . '-rule-panel-option-fields', apply_filters('paygeo-admin/cart-discounts/get-rule-panel-option-fields', $in_fields, $method_id), $method_id);
        }

    }

    PGEO_PayGeo_Admin_Discount_Rule_Panel::init();
}