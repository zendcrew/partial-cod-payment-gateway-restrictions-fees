<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//PayGeo
if ( !class_exists( 'PGEO_PayGeo_Extension' ) ) {

    include_once ('paygeo/paygeo-extension.php');

    PGEO_PayGeo_Extension::init();
}
