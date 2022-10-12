<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Conditions_Customer' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Conditions_Customer {

        public function __construct() {

            add_filter( 'paygeo/validate-user_roles-condition', array( $this, 'validate_user_roles' ), 10 );
        }

        public function validate_user_roles( $condition ) {

            if ( !isset( $condition[ 'user_roles' ] ) ) {
                return false;
            }

            $rule_user_roles = $condition[ 'user_roles' ];

            if ( !count( $rule_user_roles ) ) {
                return false;
            }


            $user_roles = PGEO_PayGeo_Customer_Util::get_user_roles();

            if ( !count( $user_roles ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            return PGEO_PayGeo_Validation_Util::validate_list_list( $user_roles, $rule_user_roles, $rule_compare );
        }

    }

    new PGEO_PayGeo_Conditions_Customer();
}