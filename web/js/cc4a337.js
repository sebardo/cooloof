(function($) {

    $(document).on('click', '[data-action^="delete"]', function(e) {
        e.preventDefault();
        if ($(this).data('action') == 'delete') {
            var file = $(this).closest('tr');
        }
        else {
            var file = $(this).closest('li');
        }
        $.ajax({
            type: 'delete',
            url: $(this).data('url'),
            success: function() {
                file.remove();
            },
            dataType: 'json'
        });
    });

    $(document).on('click', '[data-action="delete-row"]', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });

    $('#btn-upload-file').click(function (e) {
        e.preventDefault();
        $('#images').trigger('click');
    });

    $('#images').fileupload({
        dropZone: null,
        dataType: 'json',
        add: function (e, data) {
            $.each(data.files, function (index, file) {
                var template = $('#file-template').html();
                template = template.replace(/__filename__/g, file.name);
                template = template.replace(/__filesize__/g, formatFileSize(file.size));

                data.context = $(template);
                data.context.appendTo($('#files-table-body'));
                data.submit();
            });
        },
        done: function (e, data) {
            if (data)
            {
                if (data.result.length == 0) {
                    $(data.context).find('.progress').hide();
                    $(data.context).find('[data-action="delete"]').hide();
                    $(data.context).find('[data-action="delete-row"]').show();
                    $(data.context).find('span.message-exceed').show();
                }
                else {
                    $.each(data.result, function( index, info ) {
                        if (info.error) {
                            if (info.error == 'maxNumberOfFiles') {
                                $(data.context).find('span.message-limit').show();
                            }
                            else if (info.error == 'acceptFileTypes') {
                                $(data.context).find('span.message-type').show();
                            }
                            else if (info.error == 'minResolution') {
                                $(data.context).find('span.message-min-resolution').show();
                            }
                            $(data.context).find('.progress').hide();
                            $(data.context).find('[data-action="delete"]').hide();
                            $(data.context).find('[data-action="delete-row"]').show();
                        }
                        else {
                            $(data.context).find('a[href="__fileurl__"]').text(info.name);
                            if (info.serve_url) {
                                $(data.context).find('a[href="__fileurl__"]').attr('href', info.serve_url);
                                $(data.context).find('img[src="__fileurl__"]').attr('src', info.serve_url);
                            }
                            else {
                                $(data.context).find('a[href="__fileurl__"]').attr('href', '#');
                            }
                            $(data.context).find('[data-action="delete"]').attr('data-url', info.delete_url.replace('?file=', '&file='));
                            $(data.context).find('span.message-ok').show();
                            $(data.context).find('.progress').hide();
                        }
                    });
                }
            }
        },
        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(data.context).find('.progress-bar').css(
                'width',
                progress + '%'
            );
        }
    });

    $('#btn-upload-main-file').click(function (e) {
        e.preventDefault();
        if ($('#main-gallery > li').length) {
            $.SmartMessageBox({
                title : translations.max_uploads_title,
                content : translations.max_uploads_content
            });
            return;
        }
        $('#image').trigger('click');
    });

    $('#image').fileupload({
        dropZone: null,
        dataType: 'json',
        add: function (e, data) {
            $.each(data.files, function (index, file) {
                var template = $('#file-template').html();
                template = template.replace(/__filename__/g, file.name);
                template = template.replace(/__filesize__/g, formatFileSize(file.size));

                data.context = $(template);
                data.context.appendTo($('#files-table-image-body'));
                data.submit();
            });
        },
        done: function (e, data) {
            if (data)
            {
                if (data.result.length == 0) {
                    $(data.context).find('.progress').hide();
                    $(data.context).find('[data-action="delete"]').hide();
                    $(data.context).find('[data-action="delete-row"]').show();
                    $(data.context).find('span.message-exceed').show();
                }
                else {
                    $.each(data.result, function( index, info ) {
                        if (info.error) {
                            if (info.error == 'maxNumberOfFiles') {
                                $(data.context).find('span.message-limit').show();
                            }
                            else if (info.error == 'acceptFileTypes') {
                                $(data.context).find('span.message-type').show();
                            }
                            else if (info.error == 'minResolution') {
                                $(data.context).find('span.message-min-resolution').show();
                            }
                            $(data.context).find('.progress').hide();
                            $(data.context).find('[data-action="delete"]').hide();
                            $(data.context).find('[data-action="delete-row"]').show();
                        }
                        else {
                            $(data.context).find('a[href="__fileurl__"]').text(info.name);
                            if (info.serve_url) {
                                $(data.context).find('a[href="__fileurl__"]').attr('href', info.serve_url);
                                $(data.context).find('img[src="__fileurl__"]').attr('src', info.serve_url);
                            }
                            else {
                                $(data.context).find('a[href="__fileurl__"]').attr('href', '#');
                            }
                            $(data.context).find('[data-action="delete"]').attr('data-url', info.delete_url.replace('?file=', '&file='));
                            $(data.context).find('span.message-ok').show();
                            $(data.context).find('.progress').hide();
                        }
                    });
                }
            }
        },
        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(data.context).find('.progress-bar').css(
                'width',
                progress + '%'
            );
        }
    });

})(jQuery);

function formatFileSize(bytes)
{
    if (typeof bytes !== 'number') {
        return '';
    }
    if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
    }
    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    }

    return (bytes / 1000).toFixed(2) + ' KB';
}