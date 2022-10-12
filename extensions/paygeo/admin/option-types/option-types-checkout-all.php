<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Method_Options_Checkout_All' ) ) {

    class PGEO_PayGeo_Admin_Method_Options_Checkout_All {

        public static function init() {
            add_filter( 'paygeo-admin/method-options/get-rule-option-groups', array( new self(), 'get_groups' ), 10, 2 );
            add_filter( 'paygeo-admin/method-options/get-rule-options', array( new self(), 'get_option' ), 10, 2 );

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                add_filter( 'paygeo-admin/method-options/get-rule-option-all-fields', array( new self(), 'get_fields' ), 10, 2 );
                add_filter( 'paygeo-admin/process-option-type-all', array( new self(), 'process_options' ), 10, 3 );
            }
        }

        public static function get_groups( $in_groups, $method_id ) {
            $in_groups[ 'checkout' ] = esc_html__( 'Checkout Settings', 'zcpg-woo-paygeo' );
            return $in_groups;
        }

        public static function get_option( $in_options, $method_id ) {

            $in_options[ 'all' ] = array(
                'list_title' => esc_html__( 'Checkout - All Settings', 'zcpg-woo-paygeo' ),
                'title' => esc_html__( 'Checkout - All Settings', 'zcpg-woo-paygeo' ),
                'group_id' => 'checkout',
            );
            
            return $in_options;
        }

        public static function get_fields( $in_fields, $method_id ) {

            $method = PGEO_PayGeo_Admin_Page::get_payment_method( $method_id );

            $method_title = $method[ 'title' ];

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 5,
                'merge_fields' => false,
                'field_css_class' => array( 'rn-first' ),
                'fields' => array(
                    array(
                        'id' => 'enabled',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Restriction', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls payment method availability on checkout page', 'zcpg-woo-paygeo' ),
                        'default' => 'yes',
                        'options' => array(
                            'yes' => esc_html__( 'Is available', 'zcpg-woo-paygeo' ),
                            'no' => esc_html__( 'Is restricted', 'zcpg-woo-paygeo' ),
                        ),
                        'width' => '100%',
                        'fold_id' => 'all_enabled'
                    ),
                    array(
                        'id' => 'title',
                        'type' => 'textbox',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Title', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls payment method title on checkout page', 'zcpg-woo-paygeo' ),
                        'default' => '',
                        'placeholder' => $method_title,
                        'width' => '100%',
                        'fold' => array(
                            'target' => 'all_enabled',
                            'attribute' => 'value',
                            'value' => array( 'yes' ),
                            'oparator' => 'eq',
                            'clear' => false,
                        ),
                    ),
                    array(
                        'id' => 'is_any',
                        'type' => 'textblock',
                        'show_box' => true,
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Place Order Button Text', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls payment method order button text on checkout page', 'zcpg-woo-paygeo' ),
                        'text' => PGEO_PayGeo_Admin_Page::get_premium_messages( 'short_message' ),
                        'width' => '100%',
                        'box_width' => '100%',
                        'fold' => array(
                            'target' => 'all_enabled',
                            'attribute' => 'value',
                            'value' => array( 'yes' ),
                            'oparator' => 'eq',
                            'clear' => false,
                        ),
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 1,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'is_any',
                        'type' => 'textblock',
                        'show_box' => true,
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Icon Url', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls payment method icon url on checkout page', 'zcpg-woo-paygeo' ),
                        'text' => PGEO_PayGeo_Admin_Page::get_premium_messages( 'short_message' ),
                        'width' => '100%',
                        'box_width' => '100%',
                    ),
                ),
                'fold' => array(
                    'target' => 'all_enabled',
                    'attribute' => 'value',
                    'value' => array( 'yes' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 2,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'desc',
                        'type' => 'textarea',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Description', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls payment method description on checkout page', 'zcpg-woo-paygeo' ),
                        'default' => '',
                        'placeholder' => 'Descriptions....',
                        'width' => '100%',
                        'fold' => array(
                            'target' => 'all_enabled',
                            'attribute' => 'value',
                            'value' => array( 'yes' ),
                            'oparator' => 'eq',
                            'clear' => false,
                        ),
                    ),
                    array(
                        'id' => 'message',
                        'type' => 'textarea',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Restriction Message', 'zcpg-woo-paygeo' ),
                        'tooltip' => esc_html__( 'Controls payment method the restriction message on checkout page', 'zcpg-woo-paygeo' ),
                        'default' => '',
                        'placeholder' => 'Restriction message....',
                        'width' => '100%',
                        'fold' => array(
                            'target' => 'all_enabled',
                            'attribute' => 'value',
                            'value' => array( 'no' ),
                            'oparator' => 'eq',
                            'clear' => false,
                        ),
                    ),
                ),
            );
            return $in_fields;
        }

        public static function process_options( $options, $raw_options, $args = array() ) {

            $options[ 'enabled' ] = 'yes';

            if ( isset( $raw_options[ 'enabled' ] ) ) {
                $options[ 'enabled' ] = $raw_options[ 'enabled' ];
            }

            if ( $options[ 'enabled' ] == 'yes' ) {

                if ( !empty( $raw_options[ 'title' ] ) ) {
                    $options[ 'title' ] = $raw_options[ 'title' ];
                }

                if ( !empty( $raw_options[ 'desc' ] ) ) {
                    $options[ 'desc' ] = $raw_options[ 'desc' ];
                }
            } else {
                if ( !empty( $raw_options[ 'message' ] ) ) {
                    $options[ 'message' ] = $raw_options[ 'message' ];
                }
            }

            return $options;
        }

    }

    PGEO_PayGeo_Admin_Method_Options_Checkout_All::init();
}

