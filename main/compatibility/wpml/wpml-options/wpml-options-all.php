<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WOOPCD_PartialCOD_WPML_Method_Options_All' ) ) {

    class WOOPCD_PartialCOD_WPML_Method_Options_All {

        public function translate_option( $option, $prefix ) {

            if ( isset( $option[ 'title' ] ) ) {

                $title_string_id = $prefix . 'title';

                $option[ 'title' ] = WOOPCD_PartialCOD_WPML::get_translated_string( $option[ 'title' ], $title_string_id );
            }
            
            if ( isset( $option[ 'desc' ] ) ) {

                $desc_string_id = $prefix . 'desc';

                $option[ 'desc' ] = WOOPCD_PartialCOD_WPML::get_translated_string( $option[ 'desc' ], $desc_string_id );
            }
            
            if ( isset( $option[ 'message' ] ) ) {

                $message_string_id = $prefix . 'message';

                $option[ 'message' ] = WOOPCD_PartialCOD_WPML::get_translated_string( $option[ 'message' ], $message_string_id );
            }
                        
            return $option;
        }

        public function get_strings( $option_strings, $method_option, $prefix ) {

            $option_strings[ $prefix . 'title' ] = array(
                'value' => $method_option[ 'title' ],
                'is_multiline' => true
            );

            $option_strings[ $prefix . 'desc' ] = array(
                'value' => $method_option[ 'desc' ],
                'is_multiline' => true
            );

            $option_strings[ $prefix . 'message' ] = array(
                'value' => $method_option[ 'message' ],
                'is_multiline' => true
            );

            return $option_strings;
        }

    }

}
