/* Media Library Quick Actions JavaScript in Persian */

(function ($) {
    'use strict';

    $(document).ready(function () {
        $(document).on('click', '.wso-btn-optimize, .wso-btn-reoptimize', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('id');

            $btn.prop('disabled', true).text(wsoData.i18n.optimizing);

            $.post(wsoData.ajax_url, {
                action: 'wso_single_optimize',
                nonce: wsoData.nonce,
                attachment_id: id
            }).done(function (res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.data.message || wsoData.i18n.error);
                    $btn.prop('disabled', false).text('بهینه‌سازی');
                }
            }).fail(function () {
                alert(wsoData.i18n.error);
                $btn.prop('disabled', false).text('بهینه‌سازی');
            });
        });

        $(document).on('click', '.wso-btn-restore', function (e) {
            e.preventDefault();
            if (!confirm(wsoData.i18n.confirm_restore)) return;

            var $btn = $(this);
            var id = $btn.data('id');

            $btn.prop('disabled', true).text(wsoData.i18n.restoring);

            $.post(wsoData.ajax_url, {
                action: 'wso_single_restore',
                nonce: wsoData.nonce,
                attachment_id: id
            }).done(function (res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.data.message || wsoData.i18n.error);
                    $btn.prop('disabled', false).text('بازگردانی');
                }
            }).fail(function () {
                alert(wsoData.i18n.error);
                $btn.prop('disabled', false).text('بازگردانی');
            });
        });
    });

})(jQuery);
