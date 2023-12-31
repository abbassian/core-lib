//AssetBundle
Autoborna.assetOnLoad = function (container) {
    if (typeof autobornaAssetUploadEndpoint !== 'undefined' && typeof Autoborna.assetDropzone == 'undefined' && mQuery('div#dropzone').length) {
        Autoborna.initializeDropzone();
    }
};

Autoborna.assetOnUnload = function(id) {
    if (id === '#app-content') {
        delete Autoborna.assetDropzone;
    }
};

Autoborna.updateRemoteBrowser = function(provider, path) {
    path = typeof path !== 'undefined' ? path : '';

    var spinner = mQuery('<i class="fa fa-fw fa-spinner fa-spin"></i>');
    spinner.appendTo('#tab' + provider + ' a');

    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: "POST",
        data: "action=asset:fetchRemoteFiles&provider=" + provider + "&path=" + path,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                mQuery('div#remoteFileBrowser').html(response.output);

                mQuery('.remote-file-search').quicksearch('#remoteFileBrowser .remote-file-list a');
            } else {
                // TODO - Add error handler
            }
        },
        error: function (request, textStatus, errorThrown) {
            Autoborna.processAjaxError(request, textStatus, errorThrown);
        },
        complete: function() {
            spinner.remove();
        }
    })
};

Autoborna.selectRemoteFile = function(url) {
    mQuery('#asset_remotePath').val(url);
    mQuery('#RemoteFileModal').modal('hide');
};

Autoborna.changeAssetStorageLocation = function() {
    if (mQuery('#asset_storageLocation_0').prop('checked')) {
        mQuery('#storage-local').removeClass('hide');
        mQuery('#storage-remote').addClass('hide');
        mQuery('#remote-button').addClass('hide');
    } else {
        mQuery('#storage-local').addClass('hide');
        mQuery('#storage-remote').removeClass('hide');
        mQuery('#remote-button').removeClass('hide');
    }
};

Autoborna.initializeDropzone = function() {
    var options = {
        url: autobornaAssetUploadEndpoint,
        uploadMultiple: false,
        filesizeBase: 1024,
        init: function() {
            this.on("addedfile", function() {
                if (this.files[1] != null) {
                    this.removeFile(this.files[0]);
                }
            });
        }
    };

    if (typeof autobornaAssetUploadMaxSize !== 'undefined') {
        options.maxFilesize = autobornaAssetUploadMaxSize;
    }

    if (typeof autobornaAssetUploadMaxSizeError !== 'undefined') {
        options.dictFileTooBig = autobornaAssetUploadMaxSizeError;
    }

    if (typeof autobornaAssetUploadExtensions !== 'undefined') {
        options.acceptedFiles = autobornaAssetUploadExtensions;
    }

    if (typeof autobornaAssetUploadExtensionError !== 'undefined') {
        options.dictInvalidFileType = autobornaAssetUploadExtensionError;
    }

    Autoborna.assetDropzone = new Dropzone("div#dropzone", options);
    var preview = mQuery('.preview div.text-center');

    Autoborna.assetDropzone.on("sending", function (file, request, formData) {
        request.setRequestHeader('X-CSRF-Token', autobornaAjaxCsrf);
        formData.append('tempId', mQuery('#asset_tempId').val());
    }).on("addedfile", function (file) {
        preview.fadeOut('fast');
    }).on("success", function (file, response, progress) {
        if (response.tmpFileName) {
            mQuery('#asset_tempName').val(response.tmpFileName);
        }

        var messageArea = mQuery('.mdropzone-error');
        if (response.error || !response.tmpFileName) {
            if (!response.error) {
                var errorText = '';
            } else {
                var errorText = (typeof response.error == 'object') ? response.error.text : response.error;
            }

            messageArea.text(errorText);
            messageArea.closest('.form-group').addClass('has-error').removeClass('is-success');

            // invoke the error
            var node, _i, _len, _ref, _results;
            file.previewElement.classList.add('dz-error');
            _ref = file.previewElement.querySelectorAll('data-dz-errormessage');
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i];
                _results.push(node.textContent = errorText);
            }
            return _results;
        } else {
            messageArea.text('');
            messageArea.closest('.form-group').removeClass('has-error').addClass('is-success');
        }

        var titleInput = mQuery('#asset_title');
        if (file.name && !titleInput.val()) {
            titleInput.val(file.name);
        }

        if (file.name) {
            mQuery('#asset_originalFileName').val(file.name);
        }
    }).on("error", function (file, response) {
        preview.fadeIn('fast');
        var messageArea = mQuery('.mdropzone-error');

        // Dropzone error is just a text in the response var
        if (typeof response == "string") {
            response = {'error': response};
        }

        if (response.error) {
            if (!response.error) {
                var errorText = '';
            } else {
                var errorText = (typeof response.error == 'object') ? response.error.text : response.error;
            }

            messageArea.text(errorText);
            messageArea.closest('.form-group').addClass('has-error').removeClass('is-success');

            // invoke the error
            var node, _i, _len, _ref, _results;
            file.previewElement.classList.add('dz-error');
            _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]');
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i];
                _results.push(node.textContent = errorText);
            }
            return _results;
        }
    }).on("thumbnail", function (file, url) {
        if (file.accepted === true) {
            var extension = file.name.substr((file.name.lastIndexOf('.') +1)).toLowerCase();
            var previewContent = '';

            if (mQuery.inArray(extension, ['jpg', 'jpeg', 'gif', 'png']) !== -1) {
                previewContent = mQuery('<img />').addClass('img-thumbnail').attr('src', url);
            } else if (extension === 'pdf') {
                previewContent = mQuery('<iframe />').attr('src', url);
            }

            preview.empty().html(previewContent);
            preview.fadeIn('fast');
        }

    });
}