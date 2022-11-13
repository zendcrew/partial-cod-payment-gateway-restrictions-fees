<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Fee_Rule_Panel_Min_Max')) {

    class PGEO_PayGeo_Admin_Fee_Rule_Panel_Min_Max {

        public static function init() {

            add_filter('paygeo-admin/cart-fees/get-rule-panel-min-max-fields', array(new self(), 'get_Panel_fields'), 10, 2);

            add_filter('paygeo-admin/cart-fees/get-rule-panel-fees-fields', array(new self(), 'get_Panel'), 30, 2);
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
                'css_class' => array('paygeo_calc_panel'),
                'fields' => apply_filters('paygeo-admin/cart-fees/get-' . $method_id . '-rule-panel-min-max-fields', apply_filters('paygeo-admin/cart-fees/get-rule-panel-min-max-fields', array(), $method_id), $method_id),
            );

            return $in_fields;
        }

        public static function get_Panel_fields($in_fields, $method_id) {

            $in_fields[] = array(
                'id' => 'fees_limit',
                'type' => 'columns-field',
                'columns' => 4,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'amount_type',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__('Fees Limit', 'pgeo-paygeo'),
                        'tooltip' => esc_html__('Controls cart fees limit', 'pgeo-paygeo'),
                        'default' => 'no',
                        'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                        'options' => array(
                            'no' => esc_html__('No limit', 'pgeo-paygeo'),
                            'prem_1' => esc_html__('Fixed amount (Premium)', 'pgeo-paygeo'),
                            'prem_2' => esc_html__('Percentage amount (Premium)', 'pgeo-paygeo'),
                        ),
                        'width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Fee_Rule_Panel_Min_Max::init();
}

