<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'PGEO_PayGeo_Admin_Conditions_Cart_Items' ) && !defined( 'PGEO_PAYGEO_PREMIUM' ) ) {

    class PGEO_PayGeo_Admin_Conditions_Cart_Items {

        public static function init() {

            add_filter( 'paygeo-admin/get-condition-groups', array( new self(), 'get_groups' ), 70, 2 );

            add_filter( 'paygeo-admin/get-cart_items-group-conditions', array( new self(), 'get_conditions' ), 10, 2 );
            
            add_filter( 'paygeo-admin/get-cart_item_products-condition-fields', array( new self(), 'get_cart_item_products_fields' ), 10 );
            add_filter( 'paygeo-admin/get-cart_item_categories-condition-fields', array( new self(), 'get_cart_item_categories_fields' ), 10 );
            add_filter( 'paygeo-admin/get-cart_item_shipping_classes-condition-fields', array( new self(), 'get_cart_item_shipping_classes_fields' ), 10 );
        }

        public static function get_groups( $in_groups, $args ) {

            $in_groups[ 'cart_items' ] = esc_html__( 'Items In Cart', 'pgeo-paygeo' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'cart_item_products' ] = esc_html__( 'Products In Cart', 'pgeo-paygeo' );
            $in_list[ 'prem_12' ] = esc_html__( 'Variations In Cart (Premium)', 'pgeo-paygeo' );
            $in_list[ 'cart_item_categories' ] = esc_html__( 'Categories In Cart', 'pgeo-paygeo' );
            $in_list[ 'prem_14' ] = esc_html__( 'Tags In Cart (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_15' ] = esc_html__( 'Attributes In Cart (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_16' ] = esc_html__( 'Virtual Products In Cart (Premium)', 'pgeo-paygeo' );
            $in_list[ 'prem_17' ] = esc_html__( 'Tax Classes In Cart (Premium)', 'pgeo-paygeo' );
            $in_list[ 'cart_item_shipping_classes' ] = esc_html__( 'Shipping Classes In Cart', 'pgeo-paygeo' );
            $in_list[ 'prem_19' ] = esc_html__( 'Product Meta Fields In Cart (Premium)', 'pgeo-paygeo' );

            return $in_list;
        }

        public static function get_cart_item_products_fields( $in_fields ) {

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
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'product_slugs',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 2,
                'placeholder' => esc_html__( 'Search products...', 'pgeo-paygeo' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'wc:products',
                    'ajax' => true,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }

        public static function get_cart_item_categories_fields( $in_fields ) {

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
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'category_slugs',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 1,
                'placeholder' => esc_html__( 'Search categories...', 'pgeo-paygeo' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'categories:product_cat',
                    'ajax' => true,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }

        public static function get_cart_item_shipping_classes_fields( $in_fields ) {

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
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'shipping_classes',
                'type' => 'select2',
                'multiple' => true,
                'placeholder' => esc_html__( 'Select shipping classes...', 'pgeo-paygeo' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'wc:shipping_classes',
                    'ajax' => false,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }
    }

    PGEO_PayGeo_Admin_Conditions_Cart_Items::init();
}
