jQuery(document).ready(function($) {
    "use strict";


    setTimeout(function() {
        init_shipped_notice();
    }, 50);

    function init_shipped_notice() {

        $('.pgeo-paygeo-notice').each(function() {

            var obj = $(this);

            var notice_id = 'lite';

            if (obj.attr('data-pgeo_paygeo_id')) {

                notice_id = obj.attr('data-pgeo_paygeo_id');
            }

            obj.find('.notice-dismiss,.pgeo-paygeo-btn-secondary').each(function() {

                var btn = $(this);

                var remind_me = 'no';

                if (btn.attr('data-pgeo_paygeo_remind')) {

                    remind_me = btn.attr('data-pgeo_paygeo_remind');
                }

                btn.on('click', function(event) {

                    var btn_is_wp = true;


                    if (obj.attr('data-pgeo_paygeo_id'))
                        if (btn.hasClass('pgeo-paygeo-btn-secondary')) {

                            event.preventDefault();
                            btn_is_wp = false;

                        }

                    $.post(ajaxurl, {
                        action: 'pgeo_paygeo_dismiss_notice',
                        dismiss_notice_id: notice_id,
                        mayme_later: remind_me,
                    });

                    if (!btn_is_wp) {

                        obj.animate({ height: '0px', opacity: 0 }, 300, 'swing', function() {

                            obj.remove();
                        });
                    }

                });
            });

        });
    }

});