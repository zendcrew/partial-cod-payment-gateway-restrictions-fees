<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Min_Max')) {

    class WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Min_Max {

        public static function init() {

            add_filter('woopcd_partialcod-admin/cart-discounts/get-rule-panel-min-max-fields', array(new self(), 'get_Panel_fields'), 10, 2);
            
            add_filter('woopcd_partialcod-admin/cart-discounts/get-rule-panel-discounts-fields', array(new self(), 'get_Panel'), 30, 2);
            

        }

               
        public static function get_Panel($in_fields, $method_id) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',          
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'last' => true,
                'css_class' => array('partialcod_calc_panel'),
                'fields' => apply_filters('woopcd_partialcod-admin/cart-discounts/get-' . $method_id . '-rule-panel-min-max-fields', apply_filters('woopcd_partialcod-admin/cart-discounts/get-rule-panel-min-max-fields', array(), $method_id), $method_id),
            );

            return $in_fields;
        }
     
        public static function get_Panel_fields($in_fields, $method_id) {

            $in_fields[] = array(
                'id' => 'discounts_limit',
                'type' => 'columns-field',
                'columns' => 4,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'amount_type',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Discounts Limit', 'woopcd-partialcod'),
                        'tooltip' => esc_html__('Controls cart discounts limit', 'woopcd-partialcod'),
                        'default' => 'no',
                        'disabled_list_filter' => 'woopcd_partialcod-admin/get-disabled-list',
                        'options' => array(
                            'no' => esc_html__('No limit', 'woopcd-partialcod'),
                            'prem_1' => esc_html__('Fixed amount (Premium)', 'woopcd-partialcod'),
                            'prem_2' => esc_html__('Percentage amount (Premium)', 'woopcd-partialcod'),
                        ),
                        'width' => '100%',
                        'fold_id' => 'max_discount_type',
                    ),
                ),
            );

            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Min_Max::init();
}

