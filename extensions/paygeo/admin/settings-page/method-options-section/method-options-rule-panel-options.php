<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Method_Rule_Panel_Options' ) ) {

    class PGEO_PayGeo_Admin_Method_Rule_Panel_Options {

        public static function init() {

            add_filter( 'paygeo-admin/method-options/get-rule-panel-options-fields', array( new self(), 'get_Panel_fields' ), 10, 2 );
            add_filter( 'paygeo-admin/method-options/get-rule-panels', array( new self(), 'get_Panel' ), 20, 2 );
            add_filter( 'reon/get-repeater-field-method_options-templates', array( new self(), 'get_option_templates' ), 10, 2 );
            add_filter( 'reon/get-repeater-field-method_options-template-groups', array( new self(), 'get_option_template_groups' ), 10, 2 );
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
                'css_class' => 'paygeo_gateway_panel_options',
                'field_css_class' => array( 'paygeo_gateway_panel_options_field' ),
                'last' => true,
                'fields' => apply_filters( 'paygeo-admin/method-options/get-' . $method_id . '-rule-panel-options-fields', apply_filters( 'paygeo-admin/method-options/get-rule-panel-options-fields', array(), $method_id ), $method_id ),
            );

            return $in_fields;
        }

        public static function get_Panel_fields( $in_fields, $method_id ) {

            $list_width = '200px';
            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $list_width = '240px';
            }

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'last' => true,
                'css_class' => 'paygeo_gateway_options_title',
                'fields' => array(
                    array(
                        'id' => 'any_id',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__( 'Method Settings', 'pgeo-paygeo' ),
                        'desc' => esc_html__( 'List of payment method settings to apply, empty list will not apply any settings', 'pgeo-paygeo' ),
                    )
                ),
            );


            $in_fields[] = array(
                'id' => 'method_options',
                'filter_id' => 'method_options',
                'field_args' => $method_id,
                'type' => 'repeater',
                'white_repeater' => true,
                'repeater_size' => 'smaller',
                'collapsible' => false,
                'accordions' => false,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => false,
                'section_type_id' => 'option_type',
                'css_class' => 'paygeo_gateway_options',
                'field_css_class' => array( 'paygeo_gateway_options_field' ),
                'width' => '100%',
                'auto_expand' => array(
                    'all_section' => true,
                    'new_section' => true,
                    'default_section' => true,
                ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => true,
                    'list_icon' => 'fa fa-list',
                    'list_width' => $list_width,
                    'button_text' => esc_html__( 'Add Settings', 'pgeo-paygeo' ),
                ),
            );

            return $in_fields;
        }

        public static function get_option_templates( $in_templates, $repeater_args ) {

            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == PGEO_PayGeo_Admin_Page::get_option_name() ) {

                $method_id = $repeater_args[ 'field_args' ];

                $options = PGEO_PayGeo_Admin_Option_Types::get_options( array(), $method_id );

                foreach ( $options as $key => $option ) {
                    if ( !isset( $option[ 'title' ] ) ) {
                        $option[ 'title' ] = $option[ 'list_title' ];
                    }

                    $template = array(
                        'id' => $key,
                        'list_label' => $option[ 'list_title' ],
                        'head' => array(
                            'title' => $option[ 'title' ],
                        )
                    );

                    if ( isset( $option[ 'group_id' ] ) ) {
                        $template[ 'group_id' ] = $option[ 'group_id' ];
                    }
                    if ( isset( $option[ 'tooltip' ] ) ) {
                        $template[ 'head' ][ 'tooltip' ] = $option[ 'tooltip' ];
                    }

                    $in_templates[] = $template;
                    add_filter( 'roen/get-repeater-template-method_options-' . $key . '-fields', array( new self(), 'get_option_fields' ), 10, 2 );
                }
            }

            return $in_templates;
        }

        public static function get_option_template_groups( $in_groups, $repeater_args ) {

            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == PGEO_PayGeo_Admin_Page::get_option_name() ) {

                $method_id = $repeater_args[ 'field_args' ];
                $in_groups = PGEO_PayGeo_Admin_Option_Types::get_option_groups( $in_groups, $method_id );
            }


            return $in_groups;
        }

        public static function get_option_fields( $in_fields, $repeater_args ) {

            $method_id = $repeater_args[ 'field_args' ];
            $template_id = $repeater_args[ 'id' ];
            $template_fields = array();

            $template_fields[] = array(
                'id' => 'option_id',
                'type' => 'autoid',
                'autoid' => 'paygeo',
            );
            return apply_filters( 'paygeo-admin/method-options/get-rule-option-' . $template_id . '-fields', $template_fields, $method_id );
        }

    }

    PGEO_PayGeo_Admin_Method_Rule_Panel_Options::init();
}