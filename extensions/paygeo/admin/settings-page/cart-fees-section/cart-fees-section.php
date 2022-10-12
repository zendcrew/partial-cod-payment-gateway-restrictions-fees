<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Fee_Rules_Page' ) ) {
    if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
        PGEO_PayGeo_Extension::required_paths( dirname( __FILE__ ), array( 'cart-fees-section.php', 'cart-fees-panel-min-max.php' ) );
    } else {
        PGEO_PayGeo_Extension::required_paths( dirname( __FILE__ ), array( 'cart-fees-section.php' ) );
    }

    class PGEO_PayGeo_Admin_Fee_Rules_Page {

        public static function init() {
            $option_name = PGEO_PayGeo_Admin_Page::get_option_name();
            foreach ( PGEO_PayGeo_Admin_Page::get_all_payment_method_ids() as $method_ids ) {
                add_filter( 'get-option-page-' . $option_name . 'section-' . $method_ids . '-fee-rules-fields', array( new self(), 'get_method_fee_rules_page_fields' ), 30, 2 );
            }

            add_filter( 'reon/get-repeater-field-method_fee_rules-templates', array( new self(), 'get_method_fee_rule_template' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-method_fee_rules-fee_rule-fields', array( new self(), 'get_method_fee_rule_fields' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-method_fee_rules-fee_rule-head-fields', array( new self(), 'get_method_fee_rule_head_fields' ), 10, 2 );

            add_filter( 'reon/process-save-options-' . $option_name, array( new self(), 'process_options' ), 10 );
        }

        public static function get_method_fee_rules_page_fields( $in_fields, $section_id ) {

            $method_id = PGEO_PayGeo_Admin_Page::get_payment_method_id_by_section_id( $section_id, '', '-fee-rules' );

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $in_fields[] = array(
                    'id' => $method_id . '_fee_rules_settings',
                    'type' => 'panel',
                    'last' => true,
                    'white_panel' => false,
                    'panel_size' => 'smaller',
                    'width' => '100%',
                    'field_css_class' => array( 'paygeo_rules_apply_mode' ),
                    'fields' => array(
                        array(
                            'id' => $method_id . 'any_id_fee',
                            'type' => 'columns-field',
                            'columns' => 5,
                            'merge_fields' => false,
                            'fields' => array(
                                array(
                                    'id' => 'max_fee_type',
                                    'type' => 'select2',
                                    'column_size' => 2,
                                    'column_title' => esc_html__( 'Handling Fees Limit', 'zcpg-woo-paygeo' ),
                                    'tooltip' => esc_html__( 'Controls cart fees limit', 'zcpg-woo-paygeo' ),
                                    'default' => 'no',
                                    'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                                    'options' => array(
                                        'no' => esc_html__( 'No limit', 'zcpg-woo-paygeo' ),
                                        'prem_1' => esc_html__( 'Fixed amount (Premium)', 'zcpg-woo-paygeo' ),
                                        'prem_2' => esc_html__( 'Percentage amount (Premium)', 'zcpg-woo-paygeo' ),
                                    ),
                                    'width' => '100%',
                                ),
                                array(
                                    'id' => 'mode',
                                    'type' => 'select2',
                                    'column_size' => 3,
                                    'column_title' => esc_html__( 'Apply Mode', 'zcpg-woo-paygeo' ),
                                    'tooltip' => esc_html__( 'Controls fees apply mode', 'zcpg-woo-paygeo' ),
                                    'default' => 'all',
                                    'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                                    'options' => apply_filters( 'paygeo-admin/cart-fees/get-rules-apply-methods', self::get_rules_apply_methods() ),
                                    'width' => '100%',
                                ),
                            ),
                        ),
                    ),
                );
            }


            $max_sections = 1;
            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $max_sections = 99999;
            }

            $in_fields[] = array(
                'id' => $method_id . '_fee_rules',
                'filter_id' => 'method_fee_rules',
                'field_args' => $method_id,
                'type' => 'repeater',
                'white_repeater' => false,
                'repeater_size' => 'small',
                'accordions' => true,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => true,
                'width' => '100%',
                'max_sections' => $max_sections,
                'max_sections_msg' => esc_html__( 'Please upgrade to premium version in order to add more fees', 'zcpg-woo-paygeo' ),
                'field_css_class' => array( 'paygeo_rules' ),
                'css_class' => 'paygeo_gateway_rules',
                'auto_expand' => array(
                    'new_section' => true,
                    'cloned_section' => true,
                ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => false,
                    'button_text' => esc_html__( 'New Handling Fee', 'zcpg-woo-paygeo' ),
                ),
            );




            return $in_fields;
        }

        public static function get_method_fee_rule_template( $in_templates, $repeater_args ) {
            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == PGEO_PayGeo_Admin_Page::get_option_name() ) {

                $method = PGEO_PayGeo_Admin_Page::get_payment_method( $repeater_args[ 'field_args' ] );

                $method_text = str_replace( '[0]', $method[ 'title' ], esc_html__( '[0] handling fee', 'zcpg-woo-paygeo' ) );

                $in_templates[] = array(
                    'id' => 'fee_rule',
                    'head' => array(
                        'title' => '',
                        'defaut_title' => $method_text,
                        'title_field' => 'title',
                        'subtitle_field' => 'admin_note',
                    )
                );
            }

            return $in_templates;
        }

        public static function get_method_fee_rule_fields( $in_fields, $repeater_args ) {

            $method_id = $repeater_args[ 'field_args' ];

            return apply_filters( 'paygeo-admin/cart-fees/get-' . $method_id . '-rule-panels', apply_filters( 'paygeo-admin/cart-fees/get-rule-panels', array(), $method_id ), $method_id );
        }

        public static function get_method_fee_rule_head_fields( $in_fields, $repeater_args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'group-field',
                'position' => 'right',
                'width' => '100%',
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'apply_mode',
                        'type' => 'select2',
                        'default' => 'with_others',
                        'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                        'options' => apply_filters( 'paygeo-admin/cart-fees/get-rules-modes', self::get_rules_modes() ),
                        'width' => '280px',
                    ),
                    array(
                        'id' => 'enable',
                        'type' => 'select2',
                        'default' => 'yes',
                        'options' => array(
                            'yes' => esc_html__( 'Enable', 'zcpg-woo-paygeo' ),
                            'no' => esc_html__( 'Disable', 'zcpg-woo-paygeo' ),
                        ),
                        'width' => '95px',
                    ),
                ),
            );



            return $in_fields;
        }

        public static function process_options( $options ) {

            foreach ( PGEO_PayGeo_Admin_Page::get_all_payment_method_ids() as $method_id ) {
                if ( isset( $options[ $method_id . '_fee_rules' ] ) ) {
                    $options[ $method_id . '_fee_rules' ] = self::process_rules( $options[ $method_id . '_fee_rules' ], $method_id );
                }
            }

            return $options;
        }

        private static function process_rules( $rules, $method_id ) {

            foreach ( $rules as $key => $rule ) {
                $rules[ $key ] = self::process_rule( $rule, $method_id );
            }
            return $rules;
        }

        private static function process_rule( $rule, $method_id ) {
            $args = array(
                'method_id' => $method_id,
                'module' => 'cart-fees',
            );

            if ( isset( $rule[ 'fee_amounts' ] ) ) {
                foreach ( $rule[ 'fee_amounts' ] as $key => $raw_amount ) {
                    $amount = array();
                    $amount[ 'amount_type' ] = $raw_amount[ 'amount_type' ];
                    $amount[ 'add_type' ] = $raw_amount[ 'add_type' ];
                    $amount[ 'set_conditions' ] = $raw_amount[ 'set_conditions' ];

                    $amount[ 'amount' ] = 0;

                    if ( is_numeric( $raw_amount[ 'amount' ] ) ) {
                        $amount[ 'amount' ] = $raw_amount[ 'amount' ];
                    }

                    $amount = apply_filters( 'paygeo-admin/process-amount-options', apply_filters( 'paygeo-admin/process-amount-type-' . $raw_amount[ 'amount_type' ] . '-options', $amount, $raw_amount, $args ), $raw_amount, $args );

                    $amount[ 'taxable' ] = $raw_amount[ 'taxable' ];

                    $rule[ 'fee_amounts' ][ $key ] = $amount;
                }
            }
            return $rule;
        }

        private static function get_rules_modes() {
            $rules_modes = array(
                'with_others' => esc_html__( 'Apply this and other fees', 'zcpg-woo-paygeo' ),
            );

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $rules_modes[ 'prem_1' ] = esc_html__( 'Apply only this fee (Premium)', 'zcpg-woo-paygeo' );
                $rules_modes[ 'prem_2' ] = esc_html__( 'Apply if other fees are valid (Premium)', 'zcpg-woo-paygeo' );
                $rules_modes[ 'prem_3' ] = esc_html__( 'Apply if no other valid fees (Premium)', 'zcpg-woo-paygeo' );
            }
            return $rules_modes;
        }

        private static function get_rules_apply_methods() {
            $rules_apply_methods = array(
                'all' => esc_html__( 'Apply all valid fees', 'zcpg-woo-paygeo' ),
            );

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $rules_apply_methods[ 'prem_1' ] = esc_html__( 'Apply first valid fee (Premium)', 'zcpg-woo-paygeo' );
                $rules_apply_methods[ 'prem_2' ] = esc_html__( 'Apply last valid fee (Premium)', 'zcpg-woo-paygeo' );
                $rules_apply_methods[ 'prem_3' ] = esc_html__( 'Apply the highest valid fee (Premium)', 'zcpg-woo-paygeo' );
                $rules_apply_methods[ 'prem_4' ] = esc_html__( 'Apply the lowest valid fee (Premium)', 'zcpg-woo-paygeo' );
            }
            $rules_apply_methods[ 'no' ] = esc_html__( 'Do not apply any fee', 'zcpg-woo-paygeo' );

            return $rules_apply_methods;
        }

    }

}
