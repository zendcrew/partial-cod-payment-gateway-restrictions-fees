<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Amount_Types' ) ) {
    PGEO_PayGeo_Extension::required_paths( dirname( __FILE__ ), array( 'amount-types.php' ) );

    class PGEO_PayGeo_Admin_Amount_Types {

        private static $amount_types = array();

        public static function get_types( $args ) {

            if ( isset( self::$amount_types[ $args[ 'module' ] ] ) ) {
                return self::$amount_types[ $args[ 'module' ] ];
            }

            $type_groups = apply_filters( 'paygeo-admin/get-amount-type-groups', array(), $args );

            foreach ( $type_groups as $key => $type_group ) {
                self::$amount_types[ $args[ 'module' ] ][ $key ][ 'label' ] = $type_group;
                self::$amount_types[ $args[ 'module' ] ][ $key ][ 'options' ] = apply_filters( 'paygeo-admin/get-amount-group-types-' . $key, array(), $args );
            }

            return self::$amount_types[ $args[ 'module' ] ];
        }

        public static function get_type_fields( $args ) {
            $type_fields = array();

            $target = $args[ 'fold_id' ];


            $amount_type_ids = self::get_type_ids( $args );

            $disabled_list = PGEO_PayGeo_Admin_Page::get_disabled_list( array(), $amount_type_ids );

            foreach ( $amount_type_ids as $amount_type_id ) {

                if ( in_array( $amount_type_id, $disabled_list ) ) {
                    continue;
                }

                $type_flds = apply_filters( 'paygeo-admin/get-amount-type-' . $amount_type_id . '-fields', array(), $args );

                foreach ( $type_flds as $type_fld ) {
                    $type_fld[ 'fold' ] = array(
                        'target' => $target,
                        'attribute' => 'value',
                        'value' => $amount_type_id,
                        'oparator' => 'eq',
                        'clear' => false,
                    );

                    $type_fields[] = $type_fld;
                }
            }

            return $type_fields;
        }

        public static function get_other_fields( $in_fields, $args ) {
            return apply_filters( 'paygeo-admin/get-amount-type-fields', $in_fields, $args );
        }

        public static function get_based_on_required_ids( $args ) {

            return apply_filters( 'paygeo-admin/get-based-on-required-ids', array(), $args );
        }

        
        public static function get_amount_add_methods( $args ) {
            $options = array(
                'add' => esc_html__( 'Add to previous amounts', 'zcpg-woo-paygeo' )
            );

            if ( !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {
                $options[ 'prem_1' ] = esc_html__( 'Subtract from previous amounts (Premium)', 'zcpg-woo-paygeo' );
                $options[ 'prem_2' ] = esc_html__( 'Override previous amounts (Premium)', 'zcpg-woo-paygeo' );
            }

            return apply_filters( 'paygeo-admin/get-amount-add-methods', $options );
        }

        public static function get_type_ids( $args ) {

            $amount_type_ids = array();
            foreach ( self::get_types( $args ) as $key => $amount_type ) {

                if ( isset( $amount_type[ 'options' ] ) ) {
                    foreach ( $amount_type[ 'options' ] as $sub_key => $a_type ) {
                        $amount_type_ids[] = $sub_key;
                    }
                } else {
                    $amount_type_ids[] = $key;
                }
            }

            return $amount_type_ids;
        }

    }

}