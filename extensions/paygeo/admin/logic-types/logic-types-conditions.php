<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'PGEO_PayGeo_Admin_Logic_Types_Conditions' ) ) {

    class PGEO_PayGeo_Admin_Logic_Types_Conditions {

        public static function init() {
            add_filter( 'paygeo-admin/get-rule-panel-conditions-fields', array( new self(), 'get_rule_conditions_title' ), 70, 2 );
            add_filter( 'paygeo-admin/get-rule-panel-conditions-fields', array( new self(), 'get_condition_fields' ), 71, 2 );

            add_filter( 'reon/get-simple-repeater-field-conditions-templates', array( new self(), 'get_condition_templates' ), 10, 2 );
            add_filter( 'roen/get-simple-repeater-template-conditions-condition-fields', array( new self(), 'get_condition_template_fields' ), 10, 2 );
        }

        public static function get_rule_conditions_title( $in_fields, $args ) {

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $in_fields[] = array(
                    'id' => 'match_mode',
                    'type' => 'select2',
                    'full_width' => true,
                    'center_head' => true,
                    'title' => esc_html__( 'Shop Conditions', 'zcpg-woo-paygeo' ),
                    'desc' => self::get_conditions_desc( $args ),
                    'default' => 'match_all',
                    'disabled_list_filter' => 'paygeo-admin/get-disabled-list',
                    'options' => array(
                        'match_all' => esc_html__( 'All conditions should match', 'zcpg-woo-paygeo' ),
                        'prem_1' => esc_html__( 'At least one condition should match (Premium)', 'zcpg-woo-paygeo' ),
                    ),
                    'width' => '320px',
                );
            }

            return $in_fields;
        }

        public static function get_condition_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'conditions',
                'type' => 'simple-repeater',
                'new_field_args' => $args,
                'white_repeater' => false,
                'repeater_size' => 'smaller',
                'buttons_sep' => false,
                'buttons_box_width' => '65px',
                'width' => '100%',
                'css_class' => array( 'paygeo_gateway_conditions' ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => false,
                    'button_text' => esc_html__( 'Add Condition', 'zcpg-woo-paygeo' ),
                ),
            );


            return $in_fields;
        }

        public static function get_condition_templates( $in_templates, $repeater_args ) {

            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == PGEO_PayGeo_Admin_Page::get_option_name() ) {
                $in_templates[] = array(
                    'id' => 'condition',
                );
            }

            return $in_templates;
        }

        public static function get_condition_template_fields( $in_fields, $repeater_args ) {

            $args = $repeater_args[ 'field_args' ];

            $list = array();

            $groups = PGEO_PayGeo_Admin_Condition_Types::get_groups( $args );

            foreach ( $groups as $key => $group_label ) {
                $list[ $key ][ 'label' ] = $group_label;
                $list[ $key ][ 'options' ] = PGEO_PayGeo_Admin_Condition_Types::get_conditions( $key, $args );
            }



            $in_fields[] = array(
                'id' => 'condition_type',
                'type' => 'select2',
                'default' => '',
                'disabled_list_filter' => 'paygeo-admin/get-disabled-grouped-list',
                'options' => $list,
                'width' => '98%',
                'box_width' => '33%',
                'dyn_switcher_id' => 'condition_type',
            );


            $conds = array();
            foreach ( $list as $grp ) {
                if ( count( $grp[ 'options' ] ) > 0 ) {
                    $conds = array_merge( $conds, array_keys( $grp[ 'options' ] ) );
                }
            }

            $disabled_list = PGEO_PayGeo_Admin_Page::get_disabled_list( array(), $conds );

            foreach ( $conds as $cond ) {
                if ( in_array( $cond, $disabled_list ) ) {
                    continue;
                }

                $in_fields[] = array(
                    'id' => 'condition_type_' . $cond,
                    'type' => 'group-field',
                    'dyn_switcher_target' => 'condition_type',
                    'dyn_switcher_target_value' => $cond,
                    'fluid-group' => true,
                    'width' => '67%',
                    'css_class' => array( 'rn-last' ),
                    'last' => true,
                    'fields' => PGEO_PayGeo_Admin_Condition_Types::get_condition_fields( $cond, $args ),
                );
            }

            return $in_fields;
        }

        private static function get_conditions_desc( $args ) {

            $module_text = esc_html__( 'settings', 'zcpg-woo-paygeo' );

            if ( 'risk-free' == $args[ 'module' ] ) {

                $module_text = esc_html__( 'partial payment', 'zcpg-woo-paygeo' );
            }

            if ( 'method-options' == $args[ 'module' ] ) {

                $module_text = esc_html__( 'method settings', 'zcpg-woo-paygeo' );
            }

            if ( 'cart-discounts' == $args[ 'module' ] ) {

                $module_text = esc_html__( 'cart discount', 'zcpg-woo-paygeo' );
            }

            if ( 'cart-fees' == $args[ 'module' ] ) {

                $module_text = esc_html__( 'handling fee', 'zcpg-woo-paygeo' );
            }

            return sprintf( esc_html__( 'List of conditions in which this %s should apply, empty conditions will apply in all cases', 'zcpg-woo-paygeo' ), $module_text );
        }

    }

    PGEO_PayGeo_Admin_Logic_Types_Conditions::init();
}