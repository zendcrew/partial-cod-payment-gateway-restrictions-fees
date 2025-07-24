<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Discounts' ) ) {

    class WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Discounts {

        public static function init() {

            add_filter( 'woopcd_partialcod-admin/cart-discounts/get-rule-panel-discounts-fields', array( new self(), 'get_Panel_fields' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/cart-discounts/get-rule-panels', array( new self(), 'get_Panel' ), 20, 2 );
            add_filter( 'reon/get-repeater-field-discount_amounts-templates', array( new self(), 'get_option_templates' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-discount_amounts-discount_amount-fields', array( new self(), 'get_discount_fields' ), 10, 2 );
        }

        public static function get_Panel( $in_fields, $method_id ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'css_class' => 'partialcod_panel_calcs',
                'field_css_class' => array( 'partialcod_panel_calcs_field' ),
                'last' => true,
                'fields' => apply_filters( 'woopcd_partialcod-admin/cart-discounts/get-' . $method_id . '-rule-panel-discounts-fields', apply_filters( 'woopcd_partialcod-admin/cart-discounts/get-rule-panel-discounts-fields', array(), $method_id ), $method_id ),
            );

            return $in_fields;
        }

        public static function get_Panel_fields( $in_fields, $method_id ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'last' => true,
                'css_class' => 'partialcod_calcs_title',
                'fields' => array(
                    array(
                        'id' => 'any_id',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__( 'Discount Amounts', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'desc' => esc_html__( 'List of discount amounts to apply, empty list will apply zero discount', 'partial-cod-payment-gateway-restrictions-fees' ),
                    )
                ),
            );
            $in_fields[] = array(
                'id' => 'discount_amounts',
                'filter_id' => 'discount_amounts',
                'field_args' => $method_id,
                'type' => 'repeater',
                'white_repeater' => true,
                'repeater_size' => 'smaller',
                'collapsible' => false,
                'accordions' => false,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => false,
                'css_class' => 'partialcod_calcs',
                'field_css_class' => array( 'partialcod_calcs_field' ),
                'width' => '100%',
                'auto_expand' => array(
                    'all_section' => true,
                    'new_section' => true,
                    'default_section' => true,
                    'cloned_section' => true,
                ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'button_text' => esc_html__( 'Add Discount', 'partial-cod-payment-gateway-restrictions-fees' ),
                ),
            );

            return $in_fields;
        }

        public static function get_option_templates( $in_templates, $repeater_args ) {

            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == WOOPCD_PartialCOD_Admin_Page::get_option_name() ) {

                $in_templates[] = array(
                    'id' => 'discount_amount',
                    'head' => array(
                        'title' => '',
                        'defaut_title' => esc_html__( 'Discount', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'title_field' => 'amount_type',
                    )
                );
            }

            return $in_templates;
        }

        public static function get_discount_fields( $in_fields, $repeater_args ) {

            $method_id = $repeater_args[ 'field_args' ];


            $args = array(
                'method_id' => $method_id,
                'module' => 'cart-discounts',
                'sub_module' => true,
                'fold_id' => 'discount_type',
            );

            $amount_types = WOOPCD_PartialCOD_Admin_Amount_Types::get_types( $args );

            $in_fields[] = array(
                'id' => 'id',
                'type' => 'autoid',
                'autoid' => 'partialcod',
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 6,
                'field_css_class' => array( 'rn-first' ),
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'amount_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Discount Type', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'tooltip' => esc_html__( 'Controls discount amount type', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'disabled_list_filter' => 'woopcd_partialcod-admin/get-disabled-grouped-list',
                        'default' => apply_filters( 'woopcd_partialcod-admin/get-amount-types-default', 'cart_fixed', $args ),
                        'options' => $amount_types,
                        'attributes' => array(
                            'data-repeater-title' => 'discount_amounts',
                        ),
                        'width' => '100%',
                        'fold_id' => 'discount_type',
                    ),
                    array(
                        'id' => 'add_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'tooltip' => esc_html__( 'Controls how the amount should be added to previously calculated amounts', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'column_title' => esc_html__( 'Addition / Subtraction', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'default' => 'add',
                        'disabled_list_filter' => 'woopcd_partialcod-admin/get-disabled-list',
                        'options' => WOOPCD_PartialCOD_Admin_Amount_Types::get_amount_add_methods( $args ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'set_conditions',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Validates amount conditions before amount calculation', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'column_title' => esc_html__( 'Validate Conditions', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'default' => 'no',
                        'disabled_list_filter' => 'woopcd_partialcod-admin/get-disabled-list',
                        'options' => self::get_set_condition_options(),
                        'width' => '100%',
                        'fold_id' => 'set_conditions',
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 12,
                'merge_fields' => false,
                'fields' => self::get_amount_box_fields( array(), $args ),
            );

            foreach ( WOOPCD_PartialCOD_Admin_Amount_Types::get_type_fields( $args ) as $type_field ) {
                $in_fields[] = $type_field;
            }

            $in_fields = WOOPCD_PartialCOD_Admin_Amount_Types::get_other_fields( $in_fields, $args );

            return $in_fields;
        }

        private static function get_amount_box_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'amount',
                'type' => 'textbox',
                'input_type' => 'number',
                'tooltip' => esc_html__( 'Controls the amount to apply, based on discount type', 'partial-cod-payment-gateway-restrictions-fees' ),
                'column_size' => 2,
                'column_title' => esc_html__( 'Amount', 'partial-cod-payment-gateway-restrictions-fees' ),
                'default' => '0.00',
                'placeholder' => esc_html__( '0.00', 'partial-cod-payment-gateway-restrictions-fees' ),
                'width' => '100%',
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
            );


            foreach ( apply_filters( 'woopcd_partialcod-admin/get-amount-box-fields', array(), $args ) as $field ) {
                $in_fields[] = $field;
            }


            $in_fields[] = array(
                'id' => 'base_on',
                'type' => 'select2',
                'column_size' => 4,
                'column_title' => esc_html__( 'Based On', 'partial-cod-payment-gateway-restrictions-fees' ),
                'tooltip' => esc_html__( 'Controls amount based on cart subtotals', 'partial-cod-payment-gateway-restrictions-fees' ),
                'default' => '2234343',
                'data' => 'partialcod:cartdiscounts_carttotals',
                'width' => '100%',
                'fold' => array(
                    'target' => 'discount_type',
                    'attribute' => 'value',
                    'value' => WOOPCD_PartialCOD_Admin_Amount_Types::get_based_on_required_ids( $args ),
                    'oparator' => 'eq',
                    'clear' => true,
                    'empty' => '2234343',
                ),
            );

            $in_fields[] = array(
                'id' => 'taxable',
                'type' => 'select2',
                'column_size' => 4,
                'column_title' => esc_html__( 'Tax Class', 'partial-cod-payment-gateway-restrictions-fees' ),
                'tooltip' => esc_html__( 'Controls discount tax class', 'partial-cod-payment-gateway-restrictions-fees' ),
                'default' => '--1',
                'disabled_list_filter' => 'woopcd_partialcod-admin/get-disabled-list',
                'data' => 'partialcod:tax_options',
                'width' => '100%',
                'fold' => array(
                    'target' => 'apply_as_coupon',
                    'attribute' => 'value',
                    'value' => array( 'no' ),
                    'oparator' => 'eq',
                    'clear' => true,
                    'empty' => '--1',
                ),
            );

            return $in_fields;
        }

        private static function get_set_condition_options() {
            if ( !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                return array(
                    'no' => esc_html__( 'No', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'prem_1' => esc_html__( 'Yes (Premium)', 'partial-cod-payment-gateway-restrictions-fees' ),
                );
            }
            return array(
                'no' => esc_html__( 'No', 'partial-cod-payment-gateway-restrictions-fees' ),
                'yes' => esc_html__( 'Yes', 'partial-cod-payment-gateway-restrictions-fees' ),
            );
        }

    }

    WOOPCD_PartialCOD_Admin_Discount_Rule_Panel_Discounts::init();
}

