<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Conditions_Customer' ) && !defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

    class WOOPCD_PartialCOD_Conditions_Customer {

        public function __construct() {

            add_filter( 'woopcd_partialcod/validate-user_roles-condition', array( $this, 'validate_user_roles' ), 10, 2 );
        }

        public function validate_user_roles( $condition, $cart_data ) {

            if ( !isset( $condition[ 'user_roles' ] ) ) {
                return false;
            }

            $rule_user_roles = $condition[ 'user_roles' ];

            if ( !count( $rule_user_roles ) ) {
                return false;
            }


            $user_roles = WOOPCD_PartialCOD_Customer_Util::get_user_roles( $cart_data );

            if ( !count( $user_roles ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            return WOOPCD_PartialCOD_Validation_Util::validate_list_list( $user_roles, $rule_user_roles, $rule_compare );
        }

    }

    new WOOPCD_PartialCOD_Conditions_Customer();
}