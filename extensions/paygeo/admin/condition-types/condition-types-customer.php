<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Customer' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Customer {

        public static function init() {
            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 100, 2 );

            add_filter( 'paygeo-admin/get-customer-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );

            add_filter( 'paygeo-admin/get-user_roles-condition-fields', array( new self(), 'get_user_roles_fields' ), 10 );
        }

        public static function get_groups( $in_groups, $args ) {
            $in_groups[ 'customer' ] = esc_html__( 'Customer', 'pgeo-paygeo' );
            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_38' ] = esc_html__( 'Customers (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_39' ] = esc_html__( 'Customer Is Logged In (Premium)', 'pgeo-paygeo' );
            $in_list[ 'user_roles' ] = esc_html__( 'User Roles', 'pgeo-paygeo' );
            $in_list[ 'prem_41' ] = esc_html__( 'User Capabilities (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_42' ] = esc_html__( 'User Meta Field (Premium)', 'pgeo-paygeo' );

            return $in_list;
        }

        public static function get_user_roles_fields( $in_fields ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'pgeo-paygeo' ),
                    'in_all_list' => esc_html__( 'All in the list', 'pgeo-paygeo' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'pgeo-paygeo' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'pgeo-paygeo' ),
                    'none' => esc_html__( 'None in the list', 'pgeo-paygeo' ),
                ),
                'width' => '98%',
                'box_width' => '25%',
            );

            $in_fields[] = array(
                'id' => 'user_roles',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 1,
                'placeholder' => esc_html__( 'Search user roles...', 'pgeo-paygeo' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'roles',
                    'ajax' => true,
                ),
                'width' => '100%',
                'box_width' => '75%',
            );



            return $in_fields;
        }

    }

    PGEO_PayGeo_Admin_Conditions_Customer::init();
}