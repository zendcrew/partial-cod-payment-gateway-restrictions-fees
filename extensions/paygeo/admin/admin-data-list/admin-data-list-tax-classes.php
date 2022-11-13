<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Data_List_Tax_Classes')) {

    class PGEO_PayGeo_Admin_Data_List_Tax_Classes {

        public static function init() {
            add_filter('paygeo-admin/get-data-list-tax_options', array(new self(), 'get_data_list'), 10, 2);
        }

        public static function get_data_list($result, $data_args) {

            $result['--1'] = esc_html__('Not taxable', 'pgeo-paygeo');
            $result['--0'] = esc_html__('Based on items tax options', 'pgeo-paygeo');
            $result[''] = esc_html__('Standard rates', 'pgeo-paygeo');


            if (!defined('PGEO_PAYGEO_PREMIUM')) {
                $tax_object = new WC_Tax();
                $tax_classes = $tax_object->get_tax_classes();
                $tax_classes_slug = $tax_object->get_tax_class_slugs();


                for ($i = 0; $i < count($tax_classes); $i++) {
                    $result['prem_' . ($i + 1)] = $tax_classes[$i] . esc_html__(' (Premium)', 'pgeo-paygeo');
                }
            }

            return $result;
        }

    }

    PGEO_PayGeo_Admin_Data_List_Tax_Classes::init();
}