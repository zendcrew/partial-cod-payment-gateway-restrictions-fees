<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Settings_Page')) {

    if (defined('WOOPCD_PARTIALCOD_PREMIUM')) {
        WOOPCD_PartialCOD_Main::required_paths(dirname(__FILE__),
                array(
                    'settings-section.php',
                    'settings-cart-totals-order-activities.php',
                    'settings-risk-free.php',
        ));
    } else {
        WOOPCD_PartialCOD_Main::required_paths(dirname(__FILE__), array('settings-section.php'));
    }

    class WOOPCD_PartialCOD_Admin_Settings_Page {

        public static function init() {
            $option_name = WOOPCD_PartialCOD_Admin_Page::get_option_name();

            add_filter('get-option-page-' . $option_name . 'section-settings-fields', array(new self(), 'get_page_fields'), 10);
        }

        public static function get_page_fields($in_fields) {
            return apply_filters('woopcd_partialcod-admin/get-settings-section-fields', $in_fields);
        }

    }

}