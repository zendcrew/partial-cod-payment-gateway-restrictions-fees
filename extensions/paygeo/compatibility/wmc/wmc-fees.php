<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'PGEO_PayGeo_WMC_Fees' ) ) {

    class PGEO_PayGeo_WMC_Fees {

        public function __construct() {

            add_filter( 'paygeo/fee-amount', array( $this, 'get_fee_amount' ), 10, 3 );
            add_filter( 'paygeo/fee-amount-tax', array( $this, 'get_fee_amount_tax' ), 10, 3 );
            add_filter( 'paygeo/fee-amount-taxes', array( $this, 'get_fee_amount_taxes' ), 10, 3 );
        }

        public function get_fee_amount( $fee_amount, $fee_key, $fee ) {

            $converter = $this->get_converter();

            return $converter->convert_amount( $fee_amount );
        }
        
        public function get_fee_amount_tax( $fee_amount_tax, $fee_key, $fee ) {

            $converter = $this->get_converter();

            return $converter->convert_amount( $fee_amount_tax );
        }
        
        public function get_fee_amount_taxes( $fee_taxes, $fee_key, $fee ) {

            $converter = $this->get_converter();

            return $converter->convert_amount_list( $fee_taxes );
        }

        private function get_converter() {

            return PGEO_PayGeo_WMC::get_instance();
        }

    }

    new PGEO_PayGeo_WMC_Fees();
}
