<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Risk_Free_Rules_Page')) {
    PGEO_PayGeo_Extension::required_paths(dirname(__FILE__), array('risk-free-section.php'));

    class PGEO_PayGeo_Admin_Risk_Free_Rules_Page {

        public static function init() {

            $option_name = PGEO_PayGeo_Admin_Page::get_option_name();
            foreach (PGEO_PayGeo_Admin_Page::get_all_payment_method_ids() as $method_ids) {
                if (PGEO_PayGeo_Extension::is_risky_method($method_ids)) {
                    add_filter('get-option-page-' . $option_name . 'section-' . $method_ids . '-riskfree-rules-fields', array(new self(), 'get_method_riskyfree_rules_page_fields'), 10, 2);
                }
            }
        }

        public static function get_method_riskyfree_rules_page_fields($in_fields, $section_id) {

            $method_id = PGEO_PayGeo_Admin_Page::get_payment_method_id_by_section_id($section_id, '', '-riskfree-rules');

            if (!defined('PGEO_PAYGEO_PREMIUM')) {

                $in_fields[] = array(
                    'id' => $method_id . 'any_id_riskfree',
                    'type' => 'panel',
                    'full_width' => true,
                    'center_head' => true,
                    'white_panel' => false,
                    'panel_size' => 'smaller',
                    'width' => '100%',
                    'merge_fields' => false,
                    'fields' => array(
                        array(
                            'id' => $method_id . 'any_id_sriskfree',
                            'type' => 'paneltitle',
                            'full_width' => true,
                            'center_head' => true,
                            'title' => esc_html__('Partial Payments', 'pgeo-paygeo'),
                            'desc' => esc_html__('Create unlimited number of partial payments', 'pgeo-paygeo'),
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
     