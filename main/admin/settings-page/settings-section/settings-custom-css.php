<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Settings_Custom_CSS')) {

    class WOOPCD_PartialCOD_Admin_Settings_Custom_CSS {

        public static function init() {
            add_filter('woopcd_partialcod-admin/get-settings-section-fields', array(new self, 'get_fields'), 70);
        }

        public static function get_fields($in_fields) {
            $custom_css = "";


            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'white_panel' => false,
                'panel_size' => 'smaller',
                'merge_fields' => false,
                'last' => true,
                'width' => '100%',
                'fields' => array(
                    array(
                        'id' => 'custom_css',
                        'type' => 'textarea',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__('Custom CSS', 'woopcd-partialcod'),
                        'desc' => esc_html__('Additional css for discounts and fees html', 'woopcd-partialcod'),
                        'default' => $custom_css,
                        'placeholder' => esc_html__('Type here...', 'woopcd-partialcod'),
                        'width' => '100%',
                        'cols' => '80',
                        'rows' => '5',
                        'height' => '50px'
                    )
                ),
            );

            return $in_fields;
        }

    }

    WOOPCD_PartialCOD_Admin_Settings_Custom_CSS::init();
}


