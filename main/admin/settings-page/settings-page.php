<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WOOPCD_PartialCOD_Admin_Page' ) ) {

    require_once dirname( __FILE__ ) . '/method-options-section/method-options-section.php';
    require_once dirname( __FILE__ ) . '/risk-free-section/risk-free-section.php';
    require_once dirname( __FILE__ ) . '/cart-discounts-section/cart-discounts-section.php';
    require_once dirname( __FILE__ ) . '/cart-fees-section/cart-fees-section.php';

    require_once dirname( __FILE__ ) . '/order-activities-section/order-activities-section.php';
    require_once dirname( __FILE__ ) . '/settings-section/settings-section.php';

    class WOOPCD_PartialCOD_Admin_Page {

        private static $option_name = WOOPCD_PARTIALCOD_OPTION_NAME;
        private static $menu_slug = "partialcod-settings";
        private static $methods = array();

        public static function init() {

            global $woopcd_partialcod;

            if ( !isset( $woopcd_partialcod ) ) {

                $woopcd_partialcod = get_option( WOOPCD_PartialCOD_Admin_Page::get_option_name() );
            }

            self::init_page();
            WOOPCD_PartialCOD_Admin_Rules_Page::init();
            WOOPCD_PartialCOD_Admin_Risk_Free_Rules_Page::init();
            WOOPCD_PartialCOD_Admin_Discount_Rules_Page::init();
            WOOPCD_PartialCOD_Admin_Fee_Rules_Page::init();
            WOOPCD_PartialCOD_Admin_Order_Activity_Rules_Page::init();
            WOOPCD_PartialCOD_Admin_Settings_Page::init();

            add_filter( 'reon/get-option-page-' . self::$option_name . '-sections', array( new self(), 'config_all_sections' ), 10 );

            add_filter( 'woopcd_partialcod-admin/get-disabled-list', array( new self(), 'get_disabled_list' ), 10, 2 );
            add_filter( 'woopcd_partialcod-admin/get-disabled-grouped-list', array( new self(), 'get_grouped_disabled_list' ), 10, 2 );

            // Reon framework custom sanitizer for partialcod
            add_filter( 'reon/sanitize-partialcod_kses_post', array( new self(), 'sanitize_partialcod_kses_post_box' ), 1, 4 );

            add_filter( 'plugin_action_links_' . plugin_basename( WOOPCD_PARTIALCOD_FILE ), array( new self(), 'get_plugin_links' ), 10, 1 );
        }

        public static function get_option_name() {

            return self::$option_name;
        }

        public static function get_page_slug() {

            return self::$menu_slug;
        }

        public static function get_all_payment_methods() {

            if ( count( self::$methods ) > 0 ) {

                return self::$methods;
            }

            if ( !self::is_admin_page() ) {

                return array();
            }

            ob_start();

            foreach ( WC()->payment_gateways()->payment_gateways() as $method_id => $method ) {

                if ( $method->enabled != 'yes' ) {

                    continue;
                }

                self::$methods[ $method_id ][ 'id' ] = $method->id;
                self::$methods[ $method_id ][ 'plugin_id' ] = $method->plugin_id;
                self::$methods[ $method_id ][ 'method_title' ] = $method->method_title;
                self::$methods[ $method_id ][ 'title' ] = $method->title;
                self::$methods[ $method_id ][ 'order_button_text' ] = $method->order_button_text;
                self::$methods[ $method_id ][ 'icon' ] = $method->icon;
                self::$methods[ $method_id ] = apply_filters( 'woopcd_partialcod-admin/get-method-' . $method_id . '-props', self::$methods[ $method_id ] );
            }

            ob_clean();

            $stored_methods = get_option( 'woopcd_partialcod_methods', false );

            if ( !$stored_methods ) {

                return self::$methods;
            }

            foreach ( $stored_methods as $stored_method_id => $stored_method ) {

                if ( isset( self::$methods[ $stored_method_id ] ) ) {

                    continue;
                }

                self::$methods[ $stored_method_id ] = $stored_method;
            }

            return self::$methods;
        }

        public static function get_all_payment_method_ids() {

            $methods = self::get_all_payment_methods();

            if ( count( $methods ) > 0 ) {

                return array_keys( $methods );
            }

            return array();
        }

        public static function get_payment_method( $method_id ) {

            $methods = self::get_all_payment_methods();

            if ( isset( $methods[ $method_id ] ) ) {

                return $methods[ $method_id ];
            }

            return array();
        }

        public static function get_payment_method_id_by_section_id( $section_id, $prefix = '', $postfix = '' ) {

            foreach ( self::get_all_payment_method_ids() as $method_id ) {

                if ( $section_id == $prefix . $method_id . $postfix ) {

                    return $method_id;
                }
            }

            return '';
        }

        public static function init_page() {
            /* translators: 1: plugin version */
            $version_text = sprintf( esc_html__( 'Lite v%s', 'partial-cod-payment-gateway-restrictions-fees' ), WOOPCD_PARTIALCOD_VERSION );

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {
                /* translators: 1: plugin version */
                $version_text = sprintf( esc_html__( 'Premium v%s', 'partial-cod-payment-gateway-restrictions-fees' ), WOOPCD_PARTIALCOD_VERSION );
            }

            $args = array(
                'option_name' => self::$option_name,
                'database' => 'option',
                'slug' => self::$menu_slug,
                'url_base' => 'admin.php',
                'default_min_height' => '700px',
                'enable_section_title' => true,
                'width' => 'auto',
                'page_id' => 'woopcd_partialcod_page',
                'aside_width' => '210px',
                'display' => array(
                    'enabled' => true,
                    'image' => WOOPCD_PARTIALCOD_ASSETS_URL . 'images/aside_logo.png',
                    'title' => esc_html__( 'Partial COD', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'sub_title' => 'Gateway Restrictions & Fees',
                    'version' => $version_text,
                    'styles' => array(
                        'bg_image' => WOOPCD_PARTIALCOD_ASSETS_URL . 'images/aside_bg.png',
                        'bg_color' => '#0073aa',
                        'color' => '#fff',
                        'height' => '195px',
                    ),
                ),
                'ajax' => array(
                    'save_msg' => esc_html__( 'Done!!', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'save_error_msg' => esc_html__( 'Unable to save your settings', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'reset_msg' => esc_html__( 'Done!!', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'reset_error_msg' => esc_html__( 'Unable to reset reset your settings', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'nonce_error_msg' => esc_html__( 'invalid nonce', 'partial-cod-payment-gateway-restrictions-fees' ),
                ),
                'menu' => array(
                    'enable' => true,
                    'title' => esc_html__( 'Partial COD - Gateway Restrictions & Fees', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'page_title' => esc_html__( 'Partial COD - Payment Gateway Restrictions & Fees', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'dashicons-admin-generic',
                    'priority' => 2,
                    'parent' => 'woocommerce',
                    'capability' => 'manage_woocommerce',
                ),
                'import_export' => array(
                    'enable' => true,
                    'min_height' => '565px',
                    'title' => esc_html__( 'Import / Export', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'header_title' => esc_html__( 'Import / Export', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'import' => array(
                        'title' => esc_html__( 'Import Settings', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'desc' => esc_html__( 'Here you can import new settings. Simply paste the settings url or data on the field below.', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'url_button_text' => esc_html__( 'Import from url', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'url_textbox_desc' => esc_html__( "Paste the url to another site's settings below and click the 'Import Now' button.", 'partial-cod-payment-gateway-restrictions-fees' ),
                        'url_textbox_hint' => esc_html__( "Paste the url to another site's settings here...", 'partial-cod-payment-gateway-restrictions-fees' ),
                        'data_button_text' => esc_html__( 'Import Data', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'data_textbox_desc' => esc_html__( "Paste your backup settings below and click the 'Import Now' button.", 'partial-cod-payment-gateway-restrictions-fees' ),
                        'data_textbox_hint' => esc_html__( 'Paste your backup settings here...', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'import_button_text' => esc_html__( 'Import Now', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'warn_text' => esc_html__( 'Warning! This will override all existing settings. proceed with caution!', 'partial-cod-payment-gateway-restrictions-fees' ),
                    ),
                    'export' => array(
                        'title' => esc_html__( 'Export Settings', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'desc' => esc_html__( 'Here you can backup your current settings. You can later use it to restore your settings.', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'download_button_text' => esc_html__( 'Download Data', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'url_button_text' => esc_html__( 'Export url', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'url_textbox_desc' => esc_html__( 'Copy the url below, use it to transfer the settings from this site.', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'data_button_text' => esc_html__( 'Export Data', 'partial-cod-payment-gateway-restrictions-fees' ),
                        'data_textbox_desc' => esc_html__( 'Copy the data below, use it as your backup.', 'partial-cod-payment-gateway-restrictions-fees' ),
                    ),
                ),
                'header_buttons' => array(
                    'reset_all_text' => esc_html__( 'Reset All', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'reset_section_text' => esc_html__( 'Reset Section', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'save_section_text' => esc_html__( 'Save Section', 'partial-cod-payment-gateway-restrictions-fees' ),
                ),
                'footer_buttons' => array(
                    'reset_all_text' => esc_html__( 'Reset All', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'reset_section_text' => esc_html__( 'Reset Section', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'save_section_text' => esc_html__( 'Save Section', 'partial-cod-payment-gateway-restrictions-fees' ),
                ),
                'page_links' => self::get_page_links(),
                'social_links' => self::get_social_links(),
            );

            Reon::set_option_page( $args );
        }

        public static function config_all_sections( $in_sections ) {

            $group_id = 2;

            foreach ( self::get_all_payment_method_ids() as $method_id ) {

                $in_sections = self::config_section( $in_sections, $method_id, $group_id );

                $group_id += 5;
            }


            $in_sections[] = array(
                'title' => esc_html__( 'General Settings', 'partial-cod-payment-gateway-restrictions-fees' ),
                'id' => 'settings',
                'group' => 1,
            );

            return $in_sections;
        }

        public static function config_section( $in_sections, $method_id, $group_id ) {

            $method = self::get_payment_method( $method_id );

            $group_id++;

            $in_sections[] = array(
                'title' => $method[ 'method_title' ],
                'header_title' => esc_html__( 'Settings & Restrictions', 'partial-cod-payment-gateway-restrictions-fees' ),
                'id' => $method_id . '-main',
                'group' => $group_id,
            );

            $in_sections[] = array(
                'title' => esc_html__( 'Settings & Restrictions', 'partial-cod-payment-gateway-restrictions-fees' ),
                'header_title' => esc_html__( 'Settings & Restrictions', 'partial-cod-payment-gateway-restrictions-fees' ),
                'id' => $method_id . '-rules',
                'group' => $group_id,
                'subsection' => true
            );

            $group_id++;

            if ( WOOPCD_PartialCOD_Main::is_risky_method( $method_id ) ) {
                
                $in_sections[] = array(
                    'title' => esc_html__( 'Partial Payments', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'header_title' => esc_html__( 'Partial Payments', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'id' => $method_id . '-riskfree-rules',
                    'group' => $group_id,
                    'subsection' => true
                );
            }

            $group_id++;

            $in_sections[] = array(
                'title' => esc_html__( 'Gateway Fees', 'partial-cod-payment-gateway-restrictions-fees' ),
                'header_title' => esc_html__( 'Gateway Fees', 'partial-cod-payment-gateway-restrictions-fees' ),
                'id' => $method_id . '-fee-rules',
                'group' => $group_id,
                'subsection' => true
            );

            $group_id++;

            $in_sections[] = array(
                'title' => esc_html__( 'Cart Discounts', 'partial-cod-payment-gateway-restrictions-fees' ),
                'header_title' => esc_html__( 'Cart Discounts', 'partial-cod-payment-gateway-restrictions-fees' ),
                'id' => $method_id . '-discount-rules',
                'group' => $group_id,
                'subsection' => true
            );

            $group_id++;

            $in_sections[] = array(
                'title' => esc_html__( 'Order Autopilots', 'partial-cod-payment-gateway-restrictions-fees' ),
                'header_title' => esc_html__( 'Order Autopilots', 'partial-cod-payment-gateway-restrictions-fees' ),
                'id' => $method_id . '-activitys',
                'group' => $group_id,
                'subsection' => true
            );

            return $in_sections;
        }

        public static function get_premium_messages( $message_id = '' ) {

            $premium_url = "https://codecanyon.net/item/woocommerce-partial-cod-payment-gateway-restrictions-fees/41741012?ref=zendcrew";

            $message = esc_html__( 'This feature is available on premium version', 'partial-cod-payment-gateway-restrictions-fees' );

            $link_text = esc_html__( 'Premium Feature', 'partial-cod-payment-gateway-restrictions-fees' );


            switch ( $message_id ) {
                case 'short_message':
                    $message = esc_html__( 'Available on premium version', 'partial-cod-payment-gateway-restrictions-fees' );
                    return '<a href="' . esc_url( $premium_url ) . '" target="_blank">' . $link_text . '</a> - ' . $message;

                default:

                    return '<a href="' . esc_url( $premium_url ) . '" target="_blank">' . $link_text . '</a> - ' . $message;
            }
        }

        public static function get_disabled_list_keys( $options ) {

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

                return array();
            }

            $d_list = array();

            $prem_max = count( $options );

            for ( $i = 1; $i <= $prem_max; $i++ ) {

                $d_key = 'prem_' . $i;

                if ( is_array( $d_key, $options ) ) {

                    $d_list[] = $d_key;
                }
            }

            return $d_list;
        }

        public static function get_disabled_list( $list, $options ) {

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

                return array();
            }

            $d_list = array();

            $prem_max = count( $options );

            for ( $i = 1; $i <= $prem_max; $i++ ) {

                $d_key = 'prem_' . $i;

                if ( isset( $options[ $d_key ] ) ) {

                    $d_list[] = $d_key;
                }
            }

            return $d_list;
        }

        public static function get_grouped_disabled_list( $list, $grouped_options ) {
            $options = array();

            foreach ( $grouped_options as $grouped_option ) {

                if ( !isset( $grouped_option[ 'options' ] ) ) {
                    continue;
                }

                foreach ( $grouped_option[ 'options' ] as $key => $option ) {
                    $options[ $key ] = $option;
                }
            }

            return self::get_disabled_list( $list, $options );
        }

        public static function sanitize_partialcod_kses_post_box( $sanitized_option, $raw_option, $field_id, $children ) {

            $allowed_html = WOOPCD_PartialCOD_Main::get_allow_html();

            return wp_kses( $raw_option, $allowed_html );
        }

        public static function get_plugin_links( $links ) {

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

                unset( $links[ 'deactivate' ] );

                $add_on_text = esc_html__( 'WooCommerce Partial COD - Premium (Add-On)', 'partial-cod-payment-gateway-restrictions-fees' );
                /* translators: 1: plugin name */
                $required_text = sprintf( esc_html__( 'Required by %s', 'partial-cod-payment-gateway-restrictions-fees' ), $add_on_text );

                $no_deactivate_tag = '<span style="color: #313639">' . $required_text . '</span>';

                array_unshift( $links, $no_deactivate_tag );

                return $links;
            }

            $doc_link = '<a href="' . esc_url( 'https://support.zendcrew.cc/portal/en/kb/woocommerce-payment-gateway-restrictions-fees' ) . '" target="_blank">' . esc_html__( 'Documentation', 'partial-cod-payment-gateway-restrictions-fees' ) . '</a>';

            array_unshift( $links, $doc_link );

            $settings_url = admin_url( 'admin.php?page=partialcod-settings' );

            $settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'partial-cod-payment-gateway-restrictions-fees' ) . '</a>';

            array_unshift( $links, $settings_link );

            return $links;
        }

        private static function get_page_links() {

            $page_links = array(
                array(
                    'id' => 'woopcd_documentation',
                    'title' => esc_html__( 'Documentation', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-file-text',
                    'href' => esc_url( 'https://support.zendcrew.cc/portal/en/kb/woocommerce-payment-gateway-restrictions-fees' ),
                    'target' => '_blank',
                    'show_in' => 'both'
                ),
            );

            if ( defined( 'WOOPCD_PARTIALCOD_PREMIUM' ) ) {

                $page_links[] = array(
                    'id' => 'woopcd_help',
                    'title' => esc_html__( 'Help', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-question-circle',
                    'href' => esc_url( 'https://support.zendcrew.cc/portal/en/newticket' ),
                    'target' => '_blank',
                    'show_in' => 'both'
                );
            } else {

                $page_links[] = array(
                    'id' => 'woopcd_get_premium',
                    'title' => esc_html__( 'Premium Version', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-file-text-o',
                    'href' => esc_url( 'https://codecanyon.net/item/woocommerce-partial-cod-payment-gateway-restrictions-fees/41741012?ref=zendcrew' ),
                    'target' => '_blank',
                    'show_in' => 'both'
                );
            }

            return $page_links;
        }

        private static function get_social_links() {

            return array(
                array(
                    'id' => 'woopcd_facebook',
                    'title' => esc_html__( 'Facebook', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-facebook',
                    'href' => esc_url( 'http://www.facebook.com/zendcrew' ),
                    'target' => '_blank',
                ),
                array(
                    'id' => 'woopcd_linkedin',
                    'title' => esc_html__( 'LinkedIn', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-linkedin',
                    'href' => esc_url( 'https://www.linkedin.com/company/zendcrew' ),
                    'target' => '_blank',
                ),
                array(
                    'id' => 'woopcd_stack_overflow',
                    'title' => esc_html__( 'Stack Overflow', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-stack-overflow',
                    'href' => esc_url( 'https://stackoverflow.com/users/8692713/zendcrew' ),
                    'target' => '_blank',
                ),
                array(
                    'id' => 'woopcd_instagram',
                    'title' => esc_html__( 'Instagram', 'partial-cod-payment-gateway-restrictions-fees' ),
                    'icon' => 'fa fa-instagram',
                    'href' => esc_url( 'https://www.instagram.com/zendcrew/' ),
                    'target' => '_blank',
                ),
            );
        }

        private static function is_admin_page() {

            $options_name = '';

            if ( isset( $_POST[ 'option_name' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

                $options_name = sanitize_key( wp_unslash( $_POST[ 'option_name' ] ) );// phpcs:ignore WordPress.Security.NonceVerification.Missing
            }

            if ( self::get_option_name() == $options_name ) {

                return true;
            }

            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == self::get_page_slug() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                return true;
            }

            return false;
        }

    }

    WOOPCD_PartialCOD_Admin_Page::init();
}


