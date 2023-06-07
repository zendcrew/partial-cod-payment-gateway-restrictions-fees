"use strict";
jQuery(document).ready(function($) {
    
    if ('' != woopcd_partialcod.update_triggers) {
        $('form.checkout').on('change', woopcd_partialcod.update_triggers, function() {
            update_checkout();
        });
    }

    $(document.body).on("updated_checkout", function(e, data) {
        process_method_noti(data);
        run_partialcod_tips();
    });

    $(document.body).on('updated_cart_totals', function() {
        run_partialcod_tips();
    });

    run_partialcod_tips();
    process_method_noti();

    function update_checkout() {

        $('body').trigger('update_checkout');

    }

    function process_method_noti(data) {

        var data_str = '';

        if (data && data.fragments['#partialcod_msgs']) {
            data_str = $(data.fragments['#partialcod_msgs']).html();
        } else {

            var pmsg = $('#partialcod_msgs');

            if (pmsg.length > 0) {
                data_str = pmsg.html();
                pmsg.remove();
            }

        }

        if (data_str != '') {
            $('#payment').find('.partialcod-unavailable').remove();
            $('#payment').prepend(data_str);
        }

    }

    function run_partialcod_tips() {
        $('.fee').each(function() {
            var partialcod_fee = $(this);
            var partialcod_fee_tips = partialcod_fee.find('.partialcod-desc-mv');
            partialcod_fee.find('th').append(partialcod_fee_tips);
            partialcod_fee.find('td').find('.partialcod-desc-mv').remove();
            partialcod_fee_tips.removeClass('partialcod-desc-mv');
        });

        $('.partialcod-cart-desc').tipTip({ defaultPosition: 'top' });
    }
});