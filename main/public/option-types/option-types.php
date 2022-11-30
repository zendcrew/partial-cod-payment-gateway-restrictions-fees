<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WOOPCD_PartialCOD_Option_Types')) {
    WOOPCD_PartialCOD_Main::required_paths(dirname(__FILE__), array('option-types.php'));

    class WOOPCD_PartialCOD_Option_Types {


        public static function get_options($options, $options_args, $cart_data) {

            // go through each of the option args and apply them
            foreach ($options_args as $option_args) {

                $option = array(
                    'option_type' => $option_args['option_type'],
                    'method_id' => $option_args['method_id'],
                );

                $option = apply_filters('woopcd_partialcod/get-' . $option_args['option_type'] . '-options', $option, $option_args, $cart_data);

                // allows other plugins to modify the option
                if (has_filter('woopcd_partialcod/gotten-option')) {
                    $option = apply_filters('woopcd_partialcod/gotten-option', $option, $option_args, $cart_data);
                }

                $options[$option_args['option_id']] = $option;
            }

            return $options;
        }

    }

}