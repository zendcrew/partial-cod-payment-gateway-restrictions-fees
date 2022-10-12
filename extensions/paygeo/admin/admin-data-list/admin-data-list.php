<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('PGEO_PayGeo_Admin_Data_List')) {
    
    PGEO_PayGeo_Extension::required_paths(dirname(__FILE__), array('admin-data-list.php'));

    class PGEO_PayGeo_Admin_Data_List {

        public static function init() {
            add_filter('reon/get-data-list', array(new self(), 'get_data_list'), 10, 2);
        }

        public static function get_data_list($result, $data_args) {

            $db_source = explode(':', $data_args['source']);
            $data_args['source'] = '';
            if (count($db_source) >= 2 && $db_source[0] == 'paygeo') {
                if (count($db_source) > 2) {
                    $n_src = array();
                    for ($i = 2; $i < count($db_source); $i++) {
                        $n_src[] = $db_source[$i];
                    }
                    $data_args['source'] = implode(':', $n_src);
                }

                return apply_filters('paygeo-admin/get-data-list-' . $db_source[1], $result, $data_args);
            }


            return $result;
        }

    }

    PGEO_PayGeo_Admin_Data_List::init();
}