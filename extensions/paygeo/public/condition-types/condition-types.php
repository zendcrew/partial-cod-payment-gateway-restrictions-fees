<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_Condition_Types' ) ) {
    PGEO_PayGeo_Extension::required_paths( dirname( __FILE__ ), array( 'condition-types.php' ) );

    class PGEO_PayGeo_Condition_Types {

        public static function validate_rule_conditions( $rule_conditions, $cart_data ) {

            if ( defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                return PGEO_PayGeo_Premium_Condition_Types::validate_rule_conditions( $rule_conditions, $cart_data );
            }

            // empty conditions will apply in all cases
            if ( !isset( $rule_conditions[ 'conditions' ] ) ) {
                return true;
            }

            // go through each conditions and validate them
            foreach ( $rule_conditions[ 'conditions' ] as $cond ) {

                $condition_type = $cond[ 'condition_type' ];

                $condition = array(
                    'condition_type' => $condition_type,
                );

                $condition = self::get_condition_args( $condition, $cond[ 'condition_type_' . $condition_type ] );

                $condition[ 'module' ] = $rule_conditions[ 'module' ];

                if ( false == apply_filters( 'paygeo/validate-' . $condition_type . '-condition', $condition, $cart_data ) ) {
                    return false;
                }
            }
            return true;
        }

        private static function get_condition_args( $condition, $rule_condition ) {

            foreach ( $rule_condition as $key => $condition_arg ) {

                if ( $key == 'condition_type' ) {
                    continue;
                }

                $condition[ $key ] = $condition_arg;
            }

            return $condition;
        }

    }

}
