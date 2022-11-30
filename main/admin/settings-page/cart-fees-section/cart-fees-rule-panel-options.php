<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Fee_Rule_Panel_Options')) {

    class WOOPCD_PartialCOD_Admin_Fee_Rule_Panel_Options {

        public static function init() {
            add_filter('woopcd_partialcod-admin/cart-fees/get-rule-panel-option-fields', array(new self(), 'get_fields'), 10, 2);
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
                        'tooltip' => esc_html__('Controls gateway fee titles on cart and checkout pages', 'woopcd-partialcod'),
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
                'columns' => 3,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'desc',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Controls gateway fee description on cart and checkout pages', 'woopcd-partialcod'),
                        'column_size' => 2,
                        'column_title' => esc_html__('Description', 'woopcd-partialcod'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'woopcd-partialcod'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'inclusive_tax',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Tax Calculation', 'woopcd-partialcod'),
                        'tooltip' => esc_html__('Determines how gateway fee taxes should be calculated', 'woopcd-partialcod'),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__('Apply fee exclusive of taxes', 'woopcd-partialcod'),
                            'yes' => esc_html__('Apply fee inclusive of taxes', 'woopcd-partialcod'),
                        ),
                        'width' => '100%',
                    ),
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
                            'tooltip' => esc_html__('Controls gateway fee notifications on cart and checkout pages', 'woopcd-partialcod'),
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

    WOOPCD_PartialCOD_Admin_Fee_Rule_Panel_Options::init();
}
