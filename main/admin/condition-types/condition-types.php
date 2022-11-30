<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('WOOPCD_PartialCOD_Admin_Condition_Types')) {
    WOOPCD_PartialCOD_Main::required_paths(dirname(__FILE__), array('condition-types.php'));

    class WOOPCD_PartialCOD_Admin_Condition_Types {

        public static function get_groups($args) {
            return apply_filters('woopcd_partialcod-admin/get-condition-groups', array(), $args);
        }

        public static function get_conditions($group_id, $args) {
            
            $in_list = apply_filters('woopcd_partialcod-admin/get-' . $group_id . '-group-conditions', array(), $args);
            
            return $in_list;
        }

        public static function get_condition_fields($condition_id, $args) {
            $args['condition_id'] = $condition_id;
            return apply_filters('woopcd_partialcod-admin/get-' . $condition_id . '-condition-fields', array(), $args);
        }

    }

}
