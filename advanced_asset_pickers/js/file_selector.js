/**
 * block ajax
 */

!function(global, $) {
    'use strict';

    function CustomFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'mode': 'choose',
            'uploadElement': 'body',
            'bulkParameterName': 'fID'
        }, options);

        my.options = options;
        my._templateFileProgress = _.template('<div id="ccm-file-upload-progress" class="ccm-ui"><div id="ccm-file-upload-progress-bar">' +
            '<div class="progress progress-striped active"><div class="progress-bar" style="width: <%=progress%>%;"></div></div>' +
            '</div></div>');

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupFileDownloads();
        my.setupFileUploads();
        my.setupEvents();

        if ( options.mode === 'choose'  ) {
            $('.ccm-search-bulk-action option[value="choose"]').remove();
        }

        
    }

    CustomFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    CustomFileManager.prototype.setupFileDownloads = function() {
        var my = this;
        if (!$('#ccm-file-manager-download-target').length) {
            my.$downloadTarget = $('<iframe />', {
                'name': 'ccm-file-manager-download-target',
                'id': 'ccm-file-manager-download-target'
            }).appendTo(document.body);
        } else {
            my.$downloadTarget = $('#ccm-file-manager-download-target');
        }
    };

    CustomFileManager.prototype.setupFileUploads = function() {
        var my = this,
            $fileUploaders = $('.ccm-file-manager-upload'),
            $fileUploader = $fileUploaders.filter('#ccm-file-manager-upload-prompt'),
            errors = [],
            files = [],
            error_template = _.template(
                '<ul><% _(errors).each(function(error) { %>' +
                '<li><strong><%- error.name %></strong><p><%- error.error %></p></li>' +
                '<% }) %></ul>'),
            args = {
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
                dataType: 'json',
                formData: {'ccm_token': CCM_SECURITY_TOKEN},
                error: function(r) {
                    var message = r.responseText;
                    try {
                        message = jQuery.parseJSON(message).errors;
                        var name = this.files[0].name;
                        _(message).each(function(error) {
                            errors.push({ name:name, error:error });
                        });
                    } catch (e) {}
                },
                progressall: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#ccm-file-upload-progress-wrapper').html(my._templateFileProgress({'progress': progress}));
                },
                start: function() {
                    errors = [];
                    $('#ccm-file-upload-progress-wrapper').remove();
                    $('<div />', {'id': 'ccm-file-upload-progress-wrapper'}).html(my._templateFileProgress({'progress': 100})).appendTo(document.body);
                    $.fn.dialog.open({
                        title: ccmi18n_filemanager.uploadProgress,
                        width: 400,
                        height: 50,
                        element: $('#ccm-file-upload-progress-wrapper'),
                        modal: true
                    });
                },
                done: function(e, data)
                {
                    files.push(data.result[0]);
                },
                stop: function() {
                    jQuery.fn.dialog.closeTop();

                    if (errors.length) {
                        ConcreteAlert.dialog(ccmi18n_filemanager.uploadFailed, error_template({errors: errors}));
                    } else {
                        my.launchUploadCompleteDialog(files);
                        files = [];
                    }
                }
            };

        $fileUploader = $fileUploader.length ? $fileUploader : $fileUploaders.first();

        $fileUploader.fileupload(args);
    };

    CustomFileManager.prototype.launchUploadCompleteDialog = function(files) {
        var my = this;
        if (files && files.length && files.length > 0) {
            var data = '';
            _.each(files, function(file) {
               data += 'fID[]=' + file.fID + '&';
            });
            data = data.substring(0, data.length - 1);
            $.fn.dialog.open({
                width: '660',
                height: '500',
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/upload_complete',
                modal: true,
                data: data,
                onClose: function() {
                  my.refreshResults();
                },
                onOpen: function() {
                    var data = {filemanager: my}
                    ConcreteEvent.publish('CustomFileManagerUploadCompleteDialogOpen', data);
                },
                title: ccmi18n_filemanager.uploadComplete
            });
        }
    }

    CustomFileManager.prototype.setupEvents = function() {
        var my = this;
        ConcreteEvent.unsubscribe('CustomFileManagerAddFilesComplete');
        ConcreteEvent.subscribe('CustomFileManagerAddFilesComplete', function(e, data) {
            my.launchUploadCompleteDialog(data.files);
        });
        ConcreteEvent.unsubscribe('CustomFileManagerDeleteFilesComplete');
        ConcreteEvent.subscribe('CustomFileManagerDeleteFilesComplete', function(e, data) {
            my.refreshResults();
        });
    };

    CustomFileManager.prototype.updateResults = function(result) {
        var my = this;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);

        if (my.options.mode == 'choose') {
            my.$element.unbind('.CustomFileManagerHoverFile');
            my.$element.on('mouseover.CustomFileManagerHoverFile', 'tr[data-file-manager-file]', function() {
                $(this).addClass('ccm-search-select-hover');
            });
            my.$element.on('mouseout.CustomFileManagerHoverFile', 'tr[data-file-manager-file]', function() {
                $(this).removeClass('ccm-search-select-hover');
            });
            my.$element.unbind('.CustomFileManagerChooseFile').on('click.CustomFileManagerChooseFile', 'tr[data-file-manager-file]', function(e) {
                if ( 'checkbox' === $(e.target).prop('type') ) return;
                ConcreteEvent.publish('CustomFileManagerBeforeSelectFile', {fID: $(this).attr('data-file-manager-file')});
                ConcreteEvent.publish('CustomFileManagerSelectFile', {fID: $(this).attr('data-file-manager-file')});
                my.$downloadTarget.remove();
                return false;
            });

            $('.ccm-search-results-checkbox').remove();
        }
    };

    CustomFileManager.prototype.handleSelectedBulkAction = function(value, type, $option, $items) {
        var my = this, itemIDs = [];
        $.each($items, function(i, checkbox) {
            itemIDs.push({'name': 'item[]', 'value': $(checkbox).val()});
        });

        if (value == 'choose') {
            var items = itemIDs.map(function (value) { return value.value; });
            ConcreteEvent.publish('CustomFileManagerBeforeSelectFile', { fID: items });
            ConcreteEvent.publish('CustomFileManagerSelectFile', { fID: items });
        } else if (value == 'download') {
            my.$downloadTarget.get(0).src = CCM_TOOLS_PATH + '/files/download?' + jQuery.param(itemIDs);
        } else {
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items);
        }
    };



    /**
     * Static Methods
     */
    CustomFileManager.launchDialog = function(callback, opts ) {
        var w = $(window).width() - 53;
        var data = {};
        var i;

        $.fn.dialog.open({
            width: w,
            height: '100%',
            href: CCM_DISPATCHER_FILENAME + '/advanced_asset_pickers/file_search',
            modal: true,
            data: opts,
            title: 'Choose File',
            onOpen: function(dialog) {
                ConcreteEvent.unsubscribe('CustomFileManagerSelectFile');
                ConcreteEvent.subscribe('CustomFileManagerSelectFile', function(e, data) {
                    jQuery.fn.dialog.closeTop();
                    callback(data);
                });
            }
        });
    };

    CustomFileManager.getFileDetails = function(fID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/get_json',
            data: {'fID': fID},
            error: function(r) {
                ConcreteAlert.dialog('Error', r.responseText);
            },
            success: function(r) {
                callback(r);
            }
        });
    };

   

    // jQuery Plugin
    $.fn.customFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new CustomFileManager($(this), options);
        });
    };

    global.CustomFileManager = CustomFileManager;

}(window, $);
