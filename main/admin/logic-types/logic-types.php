<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Reon')) {
    return;
}

WOOPCD_PartialCOD_Main::required_paths(dirname(__FILE__), array('logic-types.php'));