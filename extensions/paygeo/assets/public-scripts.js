jQuery(document).ready(function($) {
    "use strict";

    if ('' != pgeo_paygeo.update_triggers) {
        $('form.checkout').on('change', pgeo_paygeo.update_triggers, function() {
            update_checkout();
        });
    }

    $(document.body).on("updated_checkout", function(e, data) {
        process_method_noti(data);
        run_paygeo_tips();
    });

    $(document.body).on('updated_cart_totals', function() {
        run_paygeo_tips();
    });

    run_paygeo_tips();
    process_method_noti();

    function update_checkout() {

        $('body').trigger('update_checkout');

    }

    function process_method_noti(data) {

        var data_str = '';

        if (data && data.fragments['#paygeo_msgs']) {
            data_str = $(data.fragments['#paygeo_msgs']).html();
        } else {

            var pmsg = $('#paygeo_msgs');

            if (pmsg.length > 0) {
                data_str = pmsg.html();
                pmsg.remove();
            }

        }

        if (data_str != '') {
            $('#payment').find('.paygeo-unavailable').remove();
            $('#payment').prepend(data_str);
        }

    }

    function run_paygeo_tips() {
        $('.fee').each(function() {
            var paygeo_fee = $(this);
            var paygeo_fee_tips = paygeo_fee.find('.paygeo-desc-mv');
            paygeo_fee.find('th').append(paygeo_fee_tips);
            paygeo_fee.find('td').find('.paygeo-desc-mv').remove();
            paygeo_fee_tips.removeClass('paygeo-desc-mv');
        });

        $('.paygeo-cart-desc').tipTip({defaultPosition: 'top'});
    }
});