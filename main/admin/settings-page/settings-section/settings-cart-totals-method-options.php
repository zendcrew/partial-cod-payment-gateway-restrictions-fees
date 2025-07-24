<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Method_Rules_Cart_Totals')) {

    class WOOPCD_PartialCOD_Admin_Method_Rules_Cart_Totals {

        public static function init() {
            add_filter('woopcd_partialcod-admin/get-settings-section-fields', array(new self, 'get_fields'), 10);
            add_filter('reon/get-simple-repeater-field-method_cart_totals-templates', array(new self(), 'get_option_templates'), 10, 2);
            add_filter('roen/get-simple-repeater-template-method_cart_totals-calc_default-fields', array(new self(), 'get_template_fields'), 10, 2);
            add_filter('roen/get-simple-repeater-template-method_cart_totals-calc_option-fields', array(new self(), 'get_template_fields'), 10, 2);

            add_filter('woopcd_partialcod-admin/get-data-list-method_carttotals', array(new self(), 'get_data_list'), 10, 2);
        }

        public static function get_fields($in_fields) {



            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'any_idssas',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__('Settings & Restrictions - Cart Totals Calculations', 'partial-cod-payment-gateway-restrictions-fees'),
                        'desc' => esc_html__('Use these settings to control what are included in the "cart totals" conditions', 'partial-cod-payment-gateway-restrictions-fees'),
                    ),
                    array(
                        'id' => 'method_cart_totals',
                        'type' => 'simple-repeater',
                        'full_width' => true,
                        'center_head' => true,
                        'white_repeater' => true,
                        'repeater_size' => 'smaller',
                        'buttons_sep' => false,
                        'buttons_box_width' => '65px',
                        'width' => '100%',
                        'default' => self::get_default_options(),
                        'static_template' => 'calc_default',
                        'section_type_id' => 'calc_option_type',
                        'sortable' => array(
                            'enabled' => false,
                        ),
                        'template_adder' => array(
                            'position' => 'right',
                            'show_list' => false,
                            'button_text' => esc_html__('Add Option', 'partial-cod-payment-gateway-restrictions-fees'),
                        ),
                    ),
                ),
            );


            return $in_fields;
        }

        public static function get_option_templates($in_templates, $repeater_args) {
            if ($repeater_args['screen'] == 'option-page' && $repeater_args['option_name'] == WOOPCD_PartialCOD_Admin_Page::get_option_name()) {


                $in_templates[] = array(
                    'id' => 'calc_default',
                    'empy_button' => true,
                );

                $in_templates[] = array(
                    'id' => 'calc_option',
                );
            }

            return $in_templates;
        }

        public static function get_template_fields($in_fields, $repeater_args) {
            if ($repeater_args['screen'] == 'option-page' && $repeater_args['option_name'] == WOOPCD_PartialCOD_Admin_Page::get_option_name()) {

                $in_fields[] = array(
                    'id' => 'option_id',
                    'type' => 'autoid',
                    'autoid' => 'partialcod',
                );

                $in_fields[] = array(
                    'id' => 'title',
                    'type' => 'textbox',
                    'default' => esc_html__('Cart subtotal', 'partial-cod-payment-gateway-restrictions-fees'),
                    'placeholder' => esc_html__('Title here...', 'partial-cod-payment-gateway-restrictions-fees'),
                    'width' => '98%',
                    'box_width' => '36%',
                );


                $in_fields[] = array(
                    'id' => 'include',
                    'type' => 'select2',
                    'multiple' => true,
                    'default' => array('subtotal'),
                    'disabled_list_filter' => 'woopcd_partialcod-admin/get-disabled-list',
                    'options' => self::get_calc_options(),
                    'width' => '100%',
                    'box_width' => '64%',
                );
            }
            return $in_fields;
        }
        
        public static function get_calc_options() {

            $options = array(
                'subtotal' => esc_html__( 'Add subtotal', 'partial-cod-payment-gateway-restrictions-fees' ),
                'subtotal_tax' => esc_html__( 'Add subtotal tax', 'partial-cod-payment-gateway-restrictions-fees' ),
            );

            if ( !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

                $options[ 'prem_1' ] = esc_html__( 'Add coupons (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_2' ] = esc_html__( 'Add coupon taxes (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_3' ] = esc_html__( 'Subtract coupons (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_4' ] = esc_html__( 'Subtract coupon taxes (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_5' ] = esc_html__( 'Add fees (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_6' ] = esc_html__( 'Add fee taxes (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_7' ] = esc_html__( 'Add shipping cost (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );
                $options[ 'prem_8' ] = esc_html__( 'Add shipping cost tax (Premium)', 'partial-cod-payment-gateway-restrictions-fees' );                
            }

            return apply_filters( 'woopcd_partialcod-admin/method-options/get-cart-totals-options', $options );
        }

        public static function get_data_list($result, $data_args) {

            global $woopcd_partialcod;


            $options = array();
            if (isset($woopcd_partialcod['method_cart_totals'])) {
                $options = $woopcd_partialcod['method_cart_totals'];
            } else {
                $options = self::get_default_options();
            }

            foreach ($options as $option) {
                $result[$option['option_id']] = $option['title'];
            }

            return $result;
        }

        private static function get_default_options() {
            return array(
                array(
                    'calc_option_type' => 'calc_default',
                    'title' => esc_html__('Subtotal including tax', 'partial-cod-payment-gateway-restrictions-fees'),
                    'include' => array('subtotal', 'subtotal_tax'),
                    'option_id' => '2234343',
                ),
                array(
                    'calc_option_type' => 'calc_option',
                    'title' => esc_html__('Subtotal excluding tax', 'partial-cod-payment-gateway-restrictions-fees'),
                    'include' => array('subtotal'),
                    'option_id' => '2234344',
                ),
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Method_Rules_Cart_Totals::init();
}