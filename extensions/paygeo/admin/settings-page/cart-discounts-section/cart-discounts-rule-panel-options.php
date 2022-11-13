<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Discount_Rule_Panel_Options')) {

    class PGEO_PayGeo_Admin_Discount_Rule_Panel_Options {

        public static function init() {

            add_filter('paygeo-admin/cart-discounts/get-rule-panel-option-fields', array(new self(), 'get_fields'), 10, 2);
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
                        'tooltip' => esc_html__('Controls discount titles on cart and checkout pages', 'pgeo-paygeo'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Title', 'pgeo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'pgeo-paygeo'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Adds a private note for reference purposes', 'pgeo-paygeo'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Admin Note', 'pgeo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'pgeo-paygeo'),
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
                        'tooltip' => esc_html__('Controls discount description on cart and checkout pages', 'pgeo-paygeo'),
                        'column_size' => 3,
                        'column_title' => esc_html__('Description', 'pgeo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'pgeo-paygeo'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'apply_as_coupon',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Apply As Coupon', 'pgeo-paygeo'),
                        'tooltip' => esc_html__('Applies cart discount as coupon on to the cart', 'pgeo-paygeo'),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__('No', 'pgeo-paygeo'),
                            'yes' => esc_html__('Yes', 'pgeo-paygeo'),
                        ),
                        'width' => '100%',
                        'fold_id' => 'apply_as_coupon',
                    ),
                    array(
                        'id' => 'coupon_code',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Applies coupon code to the cart', 'pgeo-paygeo'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Coupon Code', 'pgeo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('xxx-xxxxx', 'pgeo-paygeo'),
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
                        'column_title' => esc_html__('Tax Calculation', 'pgeo-paygeo'),
                        'tooltip' => esc_html__('Determines how cart discount taxes should be calculated', 'pgeo-paygeo'),
                        'default' => 'yes',
                        'options' => array(
                            'no' => esc_html__('Apply discount exclusive of taxes', 'pgeo-paygeo'),
                            'yes' => esc_html__('Apply discount inclusive of taxes', 'pgeo-paygeo'),
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
            
            if (!defined('PGEO_PAYGEO_PREMIUM')) {
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
                            'column_title' => esc_html__('Checkout Notification', 'pgeo-paygeo'),
                            'tooltip' => esc_html__('Controls discount notifications on cart and checkout pages', 'pgeo-paygeo'),
                            'text' => PGEO_PayGeo_Admin_Page::get_premium_messages('short_message'),
                            'width' => '100%',
                            'css_class' => array('paygeo-big-message'),
                            'box_width' => '100%',
                        ),
                    ),
                );
            }
            
            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Discount_Rule_Panel_Options::init();
}