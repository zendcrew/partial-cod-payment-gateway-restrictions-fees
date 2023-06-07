"use strict";
jQuery(document).ready(function($) {

    setTimeout(function() {
        init_shipped_notice();
    }, 50);

    function init_shipped_notice() {

        $('.woopcd-partialcod-notice').each(function() {

            var obj = $(this);

            var notice_id = 'lite';

            if (obj.attr('data-woopcd_partialcod_id')) {

                notice_id = obj.attr('data-woopcd_partialcod_id');
            }

            obj.find('.notice-dismiss,.woopcd-partialcod-btn-secondary').each(function() {

                var btn = $(this);

                var remind_me = 'no';

                if (btn.attr('data-woopcd_partialcod_remind')) {

                    remind_me = btn.attr('data-woopcd_partialcod_remind');
                }

                btn.on('click', function(event) {

                    var btn_is_wp = true;


                    if (obj.attr('data-woopcd_partialcod_id'))
                        if (btn.hasClass('woopcd-partialcod-btn-secondary')) {

                            event.preventDefault();
                            btn_is_wp = false;

                        }

                    $.post(ajaxurl, {
                        action: 'woopcd_partialcod_dismiss_notice',
                        dismiss_notice_id: notice_id,
                        mayme_later: remind_me,
                    });

                    if (!btn_is_wp) {

                        obj.animate({height: '0px', opacity: 0}, 300, 'swing', function() {

                            obj.remove();
                        });
                    }

                });
            });

        });
    }

});