<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Fee_Rule_Panel_Fees' ) ) {

    class PGEO_PayGeo_Admin_Fee_Rule_Panel_Fees {

        public static function init() {
            add_filter( 'paygeo-admin/cart-fees/get-rule-panel-fees-fields', array( new self(), 'get_Panel_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/cart-fees/get-rule-panels', array( new self(), 'get_Panel' ), 20, 2 );
            add_filter( 'reon/get-repeater-field-fee_amounts-templates', array( new self(), 'get_option_templates' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-fee_amounts-fee_amount-fields', array( new self(), 'get_fee_fields' ), 10, 2 );
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
                'css_class' => 'paygeo_panel_calcs',
                'field_css_class' => array( 'paygeo_panel_calcs_field' ),
                'last' => true,
                'fields' => apply_filters( 'paygeo-admin/cart-fees/get-' . $method_id . '-rule-panel-fees-fields', apply_filters( 'paygeo-admin/cart-fees/get-rule-panel-fees-fields', array(), $method_id ), $method_id ),
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
                'css_class' => 'paygeo_calcs_title',
                'fields' => array(
                    array(
                        'id' => 'any_id',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__( 'Fee Amounts', 'zcpg-woo-paygeo' ),
                        'desc' => esc_html__( 'List of fee amounts to apply, empty list will apply zero fee', 'zcpg-woo-paygeo' ),
                    )
                ),
            );
            $in_fields[] = array(
                'id' => 'fee_amounts',
                'filter_id' => 'fee_amounts',
                'field_args' => $method_id,
                'type' => 'repeater',
                'white_repeater' => true,
                'repeater_size' => 'smaller',
                'collapsible' => false,
                'accordions' => false,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => false,
                'css_class' => 'paygeo_calcs',
                'field_css_class' => array( 'paygeo_calcs_field' ),
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
                    'button_text' => esc_html__( 'Add Amount', 'zcpg-woo-paygeo' ),
                ),
            );

            return $in_fields;
        }

        public static function get_option_templates( $in_templates, $repeater_args ) {

            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == PGEO_PayGeo_Admin_Page::get_option_name() ) {

                $in_templates[] = array(
                    'id' => 'fee_amount',
                    'head' => array(
                        'title' => '',
                        'defaut_title' => esc_html__('Handling fee','zcpg-woo-paygeo' ),
                        'title_field' => 'amount_type',
                    )
                );
            }

            return $in_templates;
        }

        public static function get_fee_fields( $in_fields, $repeater_args ) {

            $method_id = $repeater_args[ 'field_args' ];

            $args = array(
                'method_id' => $method_id,
                'module' => 'cart-fees',
                'sub_module' => true,
                'fold_id' => 'fee_type',
            );

            $amount_types = PGEO_PayGeo_Admin_Amount_Types::get_types( $args );

            $in_fields[] = array(
                'id' => 'id',
                'type' => 'autoid',
                'autoid' => 'paygeo',
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 6,
                'merge_fields' => false,
                'field_css_class' => array( 'rn-first' ),
                'fields' => array(
                    array(
                        'id' => 'amount_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Fee Type', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls fee amount type', 'zcpg-woo-paygeo' ),
                        'disabled_list_filter' => 'paygeo-admin/get-disabled-grouped-list',
                        'default' => apply_filters( 'paygeo-admin/get-amount-types-default', 'cart_fixed', $args ),
                        'options' => $amount_types,
                        'attributes' => array(
                            'data-repeater-title' => 'fee_amounts',
                        ),
                        'width' => '100%',
                        'fold_id' => 'fee_type',
                    ),
                    array(
                        'id' => 'add_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'tooltip' => esc_html__( 'Controls how the amount should be added to previously calculated amounts', 'zcpg-woo-paygeo' ),
                        'column_title' => esc_html__( 'Addition / Subtraction', 'zcpg-woo-paygeo' ),
                        'default' => 'add',
                        'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                        'options' => PGEO_PayGeo_Admin_Amount_Types::get_amount_add_methods( $args ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'set_conditions',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Validates amount conditions before amount calculation', 'zcpg-woo-paygeo' ),
                        'column_title' => esc_html__( 'Apply Conditions', 'zcpg-woo-paygeo' ),
                        'default' => 'no',
                        'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
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


            foreach ( PGEO_PayGeo_Admin_Amount_Types::get_type_fields( $args ) as $type_field ) {
                $in_fields[] = $type_field;
            }

            $in_fields = PGEO_PayGeo_Admin_Amount_Types::get_other_fields( $in_fields, $args );

            return $in_fields;
        }

        private static function get_amount_box_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'amount',
                'type' => 'textbox',
                'input_type' => 'number',
                'tooltip' => esc_html__( 'Controls the amount to apply, based on fee type', 'zcpg-woo-paygeo' ),
                'column_size' => 2,
                'column_title' => esc_html__( 'Amount', 'zcpg-woo-paygeo' ),
                'default' => '0.00',
                'placeholder' => esc_html__( '0.00', 'zcpg-woo-paygeo' ),
                'width' => '100%',
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
            );
            
            
            foreach ( apply_filters( 'paygeo-admin/get-amount-box-fields', array(), $args ) as $field ) {
                $in_fields[] = $field;
            }

            
            $in_fields[] = array(
                'id' => 'base_on',
                'type' => 'select2',
                'column_size' => 4,
                'column_title' => esc_html__( 'Based On', 'zcpg-woo-paygeo' ),
                'tooltip' => esc_html__( 'Controls amount based on cart subtotals', 'zcpg-woo-paygeo' ),
                'default' => '2234343',
                'data' => 'paygeo:cartfees_carttotals',
                'width' => '100%',
                'fold' => array(
                    'target' => 'fee_type',
                    'attribute' => 'value',
                    'value' => PGEO_PayGeo_Admin_Amount_Types::get_based_on_required_ids( $args ),
                    'oparator' => 'eq',
                    'clear' => true,
                    'empty' => '2234343',
                ),
            );

            
            $in_fields[] = array(
                'id' => 'taxable',
                'type' => 'select2',
                'column_size' => 4,
                'column_title' => esc_html__( 'Taxable', 'zcpg-woo-paygeo' ),
                'tooltip' => esc_html__( 'Controls fee tax class', 'zcpg-woo-paygeo' ),
                'default' => '--1',
                'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                'data' => 'paygeo:tax_options',
                'width' => '100%',
            );

            return $in_fields;
        }

        private static function get_set_condition_options() {
            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                return array(
                    'no' => esc_html__( 'No', 'zcpg-woo-paygeo' ),
                    'prem_1' => esc_html__( 'Yes (Premium)', 'zcpg-woo-paygeo' ),
                );
            }
            return array(
                'no' => esc_html__( 'No', 'zcpg-woo-paygeo' ),
                'yes' => esc_html__( 'Yes', 'zcpg-woo-paygeo' ),
            );
        }

    }

    PGEO_PayGeo_Admin_Fee_Rule_Panel_Fees::init();
}
