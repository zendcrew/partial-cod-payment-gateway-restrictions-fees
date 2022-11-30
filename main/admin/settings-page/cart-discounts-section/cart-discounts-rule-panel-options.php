<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Options')) {

    class WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Options {

        public static function init() {

            add_filter('woopcd_partialcod-admin/cart-discounts/get-rule-panel-option-fields', array(new self(), 'get_fields'), 10, 2);
        }

        public static function get_fields($in_fields, $method_id) {
            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 2,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'title',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Controls discount titles on cart and checkout pages', 'woopcd-partialcod'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Title', 'woopcd-partialcod'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'woopcd-partialcod'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Adds a private note for reference purposes', 'woopcd-partialcod'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Admin Note', 'woopcd-partialcod'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'woopcd-partialcod'),
                        'width' => '100%',
                    ),
                ),
            );


            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 5,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'desc',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Controls discount description on cart and checkout pages', 'woopcd-partialcod'),
                        'column_size' => 3,
                        'column_title' => esc_html__('Description', 'woopcd-partialcod'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'woopcd-partialcod'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'apply_as_coupon',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Apply As Coupon', 'woopcd-partialcod'),
                        'tooltip' => esc_html__('Applies cart discount as coupon on to the cart', 'woopcd-partialcod'),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__('No', 'woopcd-partialcod'),
                            'yes' => esc_html__('Yes', 'woopcd-partialcod'),
                        ),
                        'width' => '100%',
                        'fold_id' => 'apply_as_coupon',
                    ),
                    array(
                        'id' => 'coupon_code',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Applies coupon code to the cart', 'woopcd-partialcod'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Coupon Code', 'woopcd-partialcod'),
                        'default' => '',
                        'placeholder' => esc_html__('xxx-xxxxx', 'woopcd-partialcod'),
                        'width' => '100%',
                        'fold' => array(
                            'target' => 'apply_as_coupon',
                            'attribute' => 'value',
                            'value' => 'yes',
                            'oparator' => 'eq',
                            'clear' => true,
                        ),
                    ),
                    
                ),
            );
            
            
            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 1,
                'merge_fields' => false,
                'fields' => array(                    
                    array(
                        'id' => 'inclusive_tax',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Tax Calculation', 'woopcd-partialcod'),
                        'tooltip' => esc_html__('Determines how cart discount taxes should be calculated', 'woopcd-partialcod'),
                        'default' => 'yes',
                        'options' => array(
                            'no' => esc_html__('Apply discount exclusive of taxes', 'woopcd-partialcod'),
                            'yes' => esc_html__('Apply discount inclusive of taxes', 'woopcd-partialcod'),
                        ),
                        'width' => '100%',                        
                    ),
                ),
                'fold' => array(
                            'target' => 'apply_as_coupon',
                            'attribute' => 'value',
                            'value' => 'no',
                            'oparator' => 'eq',
                            'clear' => false,
                        ),
            );
            
            if (!defined('WOOPCD_PARTIALCOD_PREMIUM')) {
                $in_fields[] = array(
                    'id' => 'any_ids',
                    'type' => 'columns-field',
                    'columns' => 1,
                    'merge_fields' => false,
                    'fields' => array(
                        array(
                            'id' => 'is_any',
                            'type' => 'textblock',
                            'show_box' => true,
                            'column_size' => 1,
                            'column_title' => esc_html__('Checkout Notification', 'woopcd-partialcod'),
                            'tooltip' => esc_html__('Controls discount notifications on cart and checkout pages', 'woopcd-partialcod'),
                            'text' => WOOPCD_PartialCOD_Admin_Page::get_premium_messages('short_message'),
                            'width' => '100%',
                            'css_class' => array('partialcod-big-message'),
                            'box_width' => '100%',
                        ),
                    ),
                );
            }
            
            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Options::init();
}