<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Settings_Page')) {

    if (defined('PGEO_PAYGEO_PREMIUM')) {
        PGEO_PayGeo_Extension::required_paths(dirname(__FILE__),
                array(
                    'settings-section.php',
                    'settings-cart-totals-order-activities.php',
                    'settings-risk-free.php',
        ));
    } else {
        PGEO_PayGeo_Extension::required_paths(dirname(__FILE__), array('settings-section.php'));
    }

    class PGEO_PayGeo_Admin_Settings_Page {

        public static function init() {
            $option_name = PGEO_PayGeo_Admin_Page::get_option_name();

            add_filter('get-option-page-' . $option_name . 'section-settings-fields', array(new self(), 'get_page_fields'), 10);
        }

        public static function get_page_fields($in_fields) {
            return apply_filters('paygeo-admin/get-settings-section-fields', $in_fields);
        }

    }

}