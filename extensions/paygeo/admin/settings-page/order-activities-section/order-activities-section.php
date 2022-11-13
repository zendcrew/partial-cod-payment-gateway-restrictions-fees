<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Order_Activity_Rules_Page')) {

    if (defined('PGEO_PAYGEO_PREMIUM')) {
        PGEO_PayGeo_Extension::required_paths(dirname(__FILE__), array('order-activities-section.php', 'order-activities-rules-cart-totals.php'));
    } else {
        PGEO_PayGeo_Extension::required_paths(dirname(__FILE__), array('order-activities-section.php'));
    }

    class PGEO_PayGeo_Admin_Order_Activity_Rules_Page {

        public static function init() {

            $option_name = PGEO_PayGeo_Admin_Page::get_option_name();
            foreach (PGEO_PayGeo_Admin_Page::get_all_payment_method_ids() as $method_ids) {
                add_filter('get-option-page-' . $option_name . 'section-' . $method_ids . '-activitys-fields', array(new self(), 'get_order_activity_rules_page_fields'), 10, 2);
            }
        }

        public static function get_order_activity_rules_page_fields($in_fields, $section_id) {

            $method_id = PGEO_PayGeo_Admin_Page::get_payment_method_id_by_section_id($section_id, '', '-activitys');

            if (!defined('PGEO_PAYGEO_PREMIUM')) {

                $in_fields[] = array(
                    'id' => $method_id . 'any_id_activity',
                    'type' => 'panel',
                    'full_width' => true,
                    'center_head' => true,
                    'white_panel' => false,
                    'panel_size' => 'smaller',
                    'width' => '100%',
                    'merge_fields' => false,
                    'fields' => array(
                        array(
                            'id' => $method_id . 'any_id_activity',
                            'type' => 'paneltitle',
                            'full_width' => true,
                            'center_head' => true,
                            'title' => esc_html__('Order Autopilots', 'pgeo-paygeo'),
                            'desc' => esc_html__('Create unlimited number of order autopilots', 'pgeo-paygeo'),
                        ),
                        array(
                            'id' => 'is_any',
                            'type' => 'textblock',
                            'show_box' => false,
                            'full_width' => true,
                            'center_head' => true,
                            'text' => PGEO_PayGeo_Admin_Page::get_premium_messages(),
                            'width' => '100%',
                            'box_width' => '100%',
                        )
                    ),
                );
            }


            return $in_fields;
        }

    }

}
