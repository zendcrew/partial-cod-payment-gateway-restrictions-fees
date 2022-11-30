<?php

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Option_Types' ) ) {

    WOOPCD_PartialCOD_Main::required_paths( dirname( __FILE__ ), array( 'option-types.php' ) );

    class WOOPCD_PartialCOD_Admin_Option_Types {

        public static function get_option_groups( $in_groups, $method_id ) {
            return apply_filters( 'woopcd_partialcod-admin/method-options/get-' . $method_id . '-rule-option-groups', apply_filters( 'woopcd_partialcod-admin/method-options/get-rule-option-groups', $in_groups, $method_id ), $method_id );
        }

        public static function get_options( $in_options, $method_id ) {

            return apply_filters( 'woopcd_partialcod-admin/method-options/get-' . $method_id . '-rule-options', apply_filters( 'woopcd_partialcod-admin/method-options/get-rule-options', $in_options, $method_id ), $method_id );
        }

    }

}



