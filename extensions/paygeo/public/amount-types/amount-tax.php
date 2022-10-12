<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PGEO_PayGeo_Amount_Tax')) {

    class PGEO_PayGeo_Amount_Tax {

        private static function get_instance() {
            return new self();
        }

        public static function calculate($amount, $tax_args, $cart_items = array()) {

            if (0 == $amount) {
                return array();
            }
            if (!wc_tax_enabled()) {
                return array();
            }
            if ('--1' == $tax_args['tax_class']) {
                return array();
            } else if ('--0' == $tax_args['tax_class']) {
                return self::get_instance()->calculate_based_on_items($amount, $tax_args, $cart_items);
            } else {
                return self::get_instance()->calculate_rates($amount, $tax_args);
            }
        }

        private function calculate_based_on_items($amount, $tax_args, $cart_items) {

            if (count($cart_items) == 0) {
                return array();
            }

            $total_qty = 0;
            $item_rates = array();

            // get item tax class and quantities
            foreach ($cart_items as $key => $cart_item) {

                if (isset($cart_item['data']['is_taxable']) && $cart_item['data']['is_taxable'] == true) {
                    $item_rates[$key] = array(
                        'tax_class' => $cart_item['data']['tax_class'],
                        'quantity' => $cart_item['quantity'],
                    );
                    $total_qty += $cart_item['quantity'];
                }
            }

            $sliced_amounts = self::get_instance()->get_sliced_amounts($amount, $item_rates, $total_qty);
            $taxes = array();

            //calculate item taxes
            foreach ($sliced_amounts as $sliced_amount) {
                $tax_args['tax_class'] = $sliced_amount['tax_class'];

                $txs = self::calculate_rates($sliced_amount['amount'], $tax_args);
                foreach ($txs as $key => $tx) {
                    if(isset($taxes[$key])){
                        $taxes[$key] += $tx;
                    }else{
                        $taxes[$key] = $tx;
                    }
                    
                }
            }

            return $taxes;
        }

        private function calculate_rates($amount, $tax_args) {
          
            // calculate rates on premium version
            if (defined('PGEO_PAYGEO_PREMIUM')) {
                return PGEO_PayGeo_Premium_Amount_Tax::calculate_rates($amount, $tax_args);
            }

            // calculate standard rates
            $tax_rates = WC_Tax::get_rates('');
            $taxes = WC_Tax::calc_tax($amount, $tax_rates, $tax_args['inclusive_tax']);

            return $taxes;
        }

        private function get_sliced_amounts($amount, $item_rates, $total_quantity) {

            // slices the total amount based on item quantities
            $sl_amount = array();

            $sl = PGEO_PayGeo_Util::round_num(($amount / $total_quantity), wc_get_price_decimals());


            for ($i = 0; $i < $total_quantity; $i++) {

                if ($amount - $sl >= 0) {
                    $sl_amount[] = $sl;
                    $amount = $amount - $sl;
                }
            }

            if (PGEO_PayGeo_Util::round_num($amount, wc_get_price_decimals()) > 0) {
                $sl_amount[] = PGEO_PayGeo_Util::round_num($amount, wc_get_price_decimals());
            }

            if (count($sl_amount) > $total_quantity && $total_quantity > 1) {
                $sl_amount[$total_quantity - 1] = $sl_amount[$total_quantity - 1] + $sl_amount[$total_quantity];
                unset($sl_amount[$total_quantity]);
            }



            // merge amounts based on quantities
            $index = 0;
            $keys = array_keys($item_rates);
            foreach ($keys as $key) {
                if (!isset($item_rates[$key]['amount'])) {
                    $item_rates[$key]['amount'] = 0;
                }
                for ($i = 0; $i < $item_rates[$key]['quantity']; $i++) {

                    $item_rates[$key]['amount'] += $sl_amount[$index];

                    $index++;
                }
            }
            return $item_rates;
        }

    }

}
