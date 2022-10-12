<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Fee_Rule_Panel_Options')) {

    class PGEO_PayGeo_Admin_Fee_Rule_Panel_Options {

        public static function init() {
            add_filter('paygeo-admin/cart-fees/get-rule-panel-option-fields', array(new self(), 'get_fields'), 10, 2);
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
                        'tooltip' => esc_html__('Controls handling fee titles on cart and checkout pages', 'zcpg-woo-paygeo'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Title', 'zcpg-woo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'zcpg-woo-paygeo'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__('Adds a private note for reference purposes', 'zcpg-woo-paygeo'),
                        'column_size' => 1,
                        'column_title' => esc_html__('Admin Note', 'zcpg-woo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'zcpg-woo-paygeo'),
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
                        'tooltip' => esc_html__('Controls handling fee description on cart and checkout pages', 'zcpg-woo-paygeo'),
                        'column_size' => 2,
                        'column_title' => esc_html__('Description', 'zcpg-woo-paygeo'),
                        'default' => '',
                        'placeholder' => esc_html__('Type here...', 'zcpg-woo-paygeo'),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'inclusive_tax',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Fee / Taxes', 'zcpg-woo-paygeo'),
                        'tooltip' => esc_html__('Controls handling fee and tax calculations', 'zcpg-woo-paygeo'),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__('Apply fee exclusive of taxes', 'zcpg-woo-paygeo'),
                            'yes' => esc_html__('Apply fee inclusive of taxes', 'zcpg-woo-paygeo'),
                        ),
                        'width' => '100%',
                    ),
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
                            'column_title' => esc_html__('Checkout Notification', 'zcpg-woo-paygeo'),
                            'tooltip' => esc_html__('Controls handling fee notifications on cart and checkout pages', 'zcpg-woo-paygeo'),
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

    PGEO_PayGeo_Admin_Fee_Rule_Panel_Options::init();
}
