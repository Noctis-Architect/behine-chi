/* Custom Directory Scanner Controller in Persian */

(function ($) {
    'use strict';

    $(document).ready(function () {
        $('#wso_scan_folder_type').on('change', function () {
            if ($(this).val() === 'custom') {
                $('#wso-custom-path-row').show();
            } else {
                $('#wso-custom-path-row').hide();
            }
        });

        $('#wso-run-scanner').on('click', runDirectoryScan);
        $('#wso-queue-scanned').on('click', queueScannedFiles);

        // Select-all toggle for scanned results (delegated, results are dynamic).
        $(document).on('change', '#wso-scan-select-all', function () {
            $('.wso-scan-checkbox').prop('checked', $(this).is(':checked'));
        });
    });

    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    function runDirectoryScan() {
        var folderType = $('#wso_scan_folder_type').val();
        var customPath = $('#wso_custom_scan_path').val();
        var $btn = $('#wso-run-scanner');

        $btn.prop('disabled', true).text('در حال اسکن پوشه...');

        $.post(wsoData.ajax_url, {
            action: 'wso_scan_folder',
            nonce: wsoData.nonce,
            folder_type: folderType,
            custom_path: customPath
        }).done(function (response) {
            $btn.prop('disabled', false).text('اسکن پوشه');
            if (response.success) {
                $('#wso-scanner-results').show();
                $('#wso-queue-scanned').show();

                var files = response.data.files || [];
                var html = '<p>تعداد <strong>' + response.data.count + '</strong> تصویر در پوشه <code>' + escHtml(response.data.folder) + '</code> یافت شد.</p>';

                if (response.data.truncated) {
                    html += '<p class="wso-text-muted" style="font-size:12px;">نمایش محدود به ' + files.length + ' مورد اول است تا از فشار به سرور جلوگیری شود. موارد انتخاب‌شده به صف اضافه می‌شوند.</p>';
                }

                if (!files.length) {
                    html += '<p>فایل تصویری در این پوشه یافت نشد.</p>';
                    $('#wso-queue-scanned').hide();
                } else {
                    html += '<p><label style="display:flex;align-items:center;gap:6px;font-size:13px;"><input type="checkbox" id="wso-scan-select-all" checked /> انتخاب همه (' + files.length + ' مورد نمایشی)</label></p>';
                    html += '<ul class="wso-scan-list" style="max-height:260px;overflow:auto;">';
                    $.each(files, function (i, file) {
                        html += '<li><label style="display:flex;align-items:center;gap:8px;font-size:12px;"><input type="checkbox" class="wso-scan-checkbox" value="' + escHtml(file) + '" checked /> <code style="direction:ltr;display:inline-block;">' + escHtml(file) + '</code></label></li>';
                    });
                    html += '</ul>';
                }

                $('#wso-scanner-file-list').html(html);
            } else {
                if (window.wsoToast) {
                    window.wsoToast(response.data.message || 'اسکن پوشه با خطا مواجه شد.', 'error');
                } else {
                    alert(response.data.message || 'اسکن پوشه با خطا مواجه شد.');
                }
            }
        }).fail(function () {
            $btn.prop('disabled', false).text('اسکن پوشه');
            if (window.wsoToast) {
                window.wsoToast('اسکن پوشه با خطا مواجه شد.', 'error');
            } else {
                alert('اسکن پوشه با خطا مواجه شد.');
            }
        });
    }

    function queueScannedFiles() {
        var folderType = $('#wso_scan_folder_type').val();
        var customPath = $('#wso_custom_scan_path').val();
        var $btn = $('#wso-queue-scanned');

        // Collect user selection; fall back to folder re-scan when no checkboxes rendered.
        var selected = [];
        $('.wso-scan-checkbox:checked').each(function () {
            selected.push($(this).val());
        });

        $btn.prop('disabled', true).text('در حال افزودن به صف...');

        var payload = {
            action: 'wso_queue_scanned_folder',
            nonce: wsoData.nonce,
            folder_type: folderType,
            custom_path: customPath
        };
        if ($('.wso-scan-checkbox').length) {
            payload.files = selected;
            if (!selected.length) {
                $btn.prop('disabled', false).text('افزودن موارد یافت شده به صف بهینه‌سازی');
                if (window.wsoToast) {
                    window.wsoToast('لطفاً حداقل یک فایل را انتخاب کنید.', 'warning');
                } else {
                    alert('لطفاً حداقل یک فایل را انتخاب کنید.');
                }
                return;
            }
        }

        $.post(wsoData.ajax_url, payload).done(function (res) {
            $btn.prop('disabled', false).text('افزودن موارد یافت شده به صف بهینه‌سازی');
            if (res.success) {
                if (window.wsoToast) {
                    window.wsoToast(res.data.message, 'success');
                } else {
                    alert(res.data.message);
                }
                $('a[data-tab=tab-bulk]').click();
            } else {
                if (window.wsoToast) {
                    window.wsoToast(res.data.message || 'خطا در افزودن به صف.', 'error');
                } else {
                    alert(res.data.message || 'خطا در افزودن به صف.');
                }
            }
        }).fail(function () {
            $btn.prop('disabled', false).text('افزودن موارد یافت شده به صف بهینه‌سازی');
            if (window.wsoToast) {
                window.wsoToast('خطای ارتباط با سرور در افزودن به صف.', 'error');
            } else {
                alert('خطای ارتباط با سرور در افزودن به صف.');
            }
        });
    }

})(jQuery);
