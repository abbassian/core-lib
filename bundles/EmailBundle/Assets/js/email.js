/** EmailBundle **/
Autoborna.emailOnLoad = function (container, response) {
    Autoborna.internalDynamicContentItemCreateListeners = [];
    Autoborna.internalDynamicContentFilterCreateListeners = [];

    if (mQuery('#emailform_plainText').length) {
        // @todo initiate the token dropdown
        var plaintext = mQuery('#emailform_plainText');

        Autoborna.initAtWho(plaintext, plaintext.attr('data-token-callback'));
        Autoborna.initSelectTheme(mQuery('#emailform_template'));
        Autoborna.initEmailDynamicContent();

        Autoborna.prepareVersioning(
            function (content) {
                console.log('undo');
            },
            function (content) {
                console.log('redo');
            }
        );

        // Open the builder directly when saved from the builder
        if (response && response.inBuilder) {
            Autoborna.isInBuilder = true;
            Autoborna.launchBuilder('emailform');
            Autoborna.processBuilderErrors(response);
        }
    } else if (mQuery(container + ' #list-search').length) {
        Autoborna.activateSearchAutocomplete('list-search', 'email');
    }

    if (mQuery('table.email-list').length) {
        var ids = [];
        mQuery('td.col-stats').each(function () {
            var id = mQuery(this).attr('data-stats');
            ids.push(id);
        });

        // Get all stats numbers in batches of 10
        while (ids.length > 0) {
            let batchIds = ids.splice(0, 1);
            Autoborna.ajaxActionRequest(
                'email:getEmailCountStats',
                {ids: batchIds},
                function (response) {
                    if (response.success && response.stats) {
                        for (var i = 0; i < response.stats.length; i++) {
                            var stat = response.stats[i];
                            if (mQuery('#sent-count-' + stat.id).length) {
                                if (stat.pending) {
                                    mQuery('#pending-' + stat.id + ' > a').html(stat.pending);
                                    mQuery('#pending-' + stat.id).removeClass('hide');
                                }

                                if (stat.queued) {
                                    mQuery('#queued-' + stat.id + ' > a').html(stat.queued);
                                    mQuery('#queued-' + stat.id).removeClass('hide');
                                }

                                mQuery('#sent-count-' + stat.id + ' > a').html(stat.sentCount);
                                mQuery('#read-count-' + stat.id + ' > a').html(stat.readCount);
                                mQuery('#read-percent-' + stat.id + ' > a').html(stat.readPercent);
                            }
                        }
                    }
                },
                false,
                true
            );
        }
    }

    if (mQuery('#emailGraphStats').length) {
        // Email detail graph - loaded via AJAX not to block loading a whole page
        var graphUrl = mQuery('#emailGraphStats').attr('data-graph-url');
        mQuery("#emailGraphStats").load(graphUrl, function () {
            Autoborna.renderCharts();
            Autoborna.initDateRangePicker('#emailGraphStats #daterange_date_from', '#emailGraphStats #daterange_date_to');
        });
    }
};

Autoborna.emailOnUnload = function(id) {
    if (id === '#app-content') {
        delete Autoborna.listCompareChart;
    }

    if (typeof Autoborna.ajaxActionXhrQueue !== 'undefined') {
        delete Autoborna.ajaxActionXhrQueue['email:getEmailCountStats'];
    }
};

Autoborna.insertEmailBuilderToken = function(editorId, token) {
    var editor = Autoborna.getEmailBuilderEditorInstances();
    editor[instance].insertText(token);
};

Autoborna.getEmailAbTestWinnerForm = function(abKey) {
    if (abKey && mQuery(abKey).val() && mQuery(abKey).closest('.form-group').hasClass('has-error')) {
        mQuery(abKey).closest('.form-group').removeClass('has-error');
        if (mQuery(abKey).next().hasClass('help-block')) {
            mQuery(abKey).next().remove();
        }
    }

    Autoborna.activateLabelLoadingIndicator('emailform_variantSettings_winnerCriteria');
    var emailId = mQuery('#emailform_sessionId').val();

    var query = "action=email:getAbTestForm&abKey=" + mQuery(abKey).val() + "&emailId=" + emailId;

    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: "POST",
        data: query,
        dataType: "json",
        success: function (response) {
            if (typeof response.html != 'undefined') {
                if (mQuery('#emailform_variantSettings_properties').length) {
                    mQuery('#emailform_variantSettings_properties').replaceWith(response.html);
                } else {
                    mQuery('#emailform_variantSettings').append(response.html);
                }

                if (response.html != '') {
                    Autoborna.onPageLoad('#emailform_variantSettings_properties', response);
                }
            }
        },
        error: function (request, textStatus, errorThrown) {
            Autoborna.processAjaxError(request, textStatus, errorThrown);
        },
        complete: function() {
            Autoborna.removeLabelLoadingIndicator();
        }
    });
};

Autoborna.submitSendForm = function () {
    Autoborna.dismissConfirmation();
    mQuery('.btn-send').prop('disabled', true);
    mQuery('form[name=\'batch_send\']').submit();
};

Autoborna.emailSendOnLoad = function (container, response) {
    if (mQuery('.email-send-progress').length) {
        if (!mQuery('#emailSendProgress').length) {
            Autoborna.clearModeratedInterval('emailSendProgress');
        } else {
            Autoborna.setModeratedInterval('emailSendProgress', 'sendEmailBatch', 2000);
        }
    }
};

Autoborna.emailSendOnUnload = function () {
    if (mQuery('.email-send-progress').length) {
        Autoborna.clearModeratedInterval('emailSendProgress');
        if (typeof Autoborna.sendEmailBatchXhr != 'undefined') {
            Autoborna.sendEmailBatchXhr.abort();
            delete Autoborna.sendEmailBatchXhr;
        }
    }
};

Autoborna.sendEmailBatch = function () {
    var data = 'id=' + mQuery('.progress-bar-send').data('email') + '&pending=' + mQuery('.progress-bar-send').attr('aria-valuemax') + '&batchlimit=' + mQuery('.progress-bar-send').data('batchlimit');
    Autoborna.sendEmailBatchXhr = Autoborna.ajaxActionRequest('email:sendBatch', data, function (response) {
        if (response.progress) {
            if (response.progress[0] > 0) {
                mQuery('.imported-count').html(response.progress[0]);
                mQuery('.progress-bar-send').attr('aria-valuenow', response.progress[0]).css('width', response.percent + '%');
                mQuery('.progress-bar-send span.sr-only').html(response.percent + '%');
            }

            if (response.progress[0] >= response.progress[1]) {
                Autoborna.clearModeratedInterval('emailSendProgress');

                setTimeout(function () {
                    mQuery.ajax({
                        type: 'POST',
                        showLoadingBar: false,
                        url: window.location,
                        data: 'complete=1',
                        success: function (response) {

                            if (response.newContent) {
                                // It's done so pass to process page
                                Autoborna.processPageContent(response);
                            }
                        }
                    });
                }, 1000);
            }
        }

        Autoborna.moderatedIntervalCallbackIsComplete('emailSendProgress');
    });
};

Autoborna.autoGeneratePlaintext = function() {
    mQuery('.plaintext-spinner').removeClass('hide');

    Autoborna.ajaxActionRequest(
        'email:generatePlaintText',
        {
            id: mQuery('#emailform_sessionId').val(),
            custom: mQuery('#emailform_customHtml').val()
        },
        function (response) {
            mQuery('#emailform_plainText').val(response.text);
            mQuery('.plaintext-spinner').addClass('hide');
        }
    );
};

Autoborna.selectEmailType = function(emailType) {
    if (emailType == 'list') {
        mQuery('#leadList').removeClass('hide');
        mQuery('#segmentTranslationParent').removeClass('hide');
        mQuery('#templateTranslationParent').addClass('hide');
        mQuery('.page-header h3').text(autobornaLang.newListEmail);
    } else {
        mQuery('#segmentTranslationParent').addClass('hide');
        mQuery('#templateTranslationParent').removeClass('hide');
        mQuery('#leadList').addClass('hide');
        mQuery('.page-header h3').text(autobornaLang.newTemplateEmail);
    }

    mQuery('#emailform_emailType').val(emailType);

    mQuery('body').removeClass('noscroll');

    mQuery('.email-type-modal').remove();
    mQuery('.email-type-modal-backdrop').remove();
};

Autoborna.getTotalAttachmentSize = function() {
    var assets = mQuery('#emailform_assetAttachments').val();
    if (assets) {
        assets = {
            'assets': assets
        };
        Autoborna.ajaxActionRequest('email:getAttachmentsSize', assets, function(response) {
            mQuery('#attachment-size').text(response.size);
        });
    } else {
        mQuery('#attachment-size').text('0');
    }
};

Autoborna.standardEmailUrl = function(options) {
    if (options && options.windowUrl && options.origin) {
        var url = options.windowUrl;
        var editEmailKey = '/emails/edit/emailId';
        var previewEmailKey = '/email/preview/emailId';
        if (url.indexOf(editEmailKey) > -1 ||
            url.indexOf(previewEmailKey) > -1) {
            options.windowUrl = url.replace('emailId', mQuery(options.origin).val());
        }
    }

    return options;
};

/**
 * Enables/Disables email preview and edit. Can be triggered from campaign or form actions
 * @param opener
 * @param origin
 */
Autoborna.disabledEmailAction = function(opener, origin) {
    if (typeof opener == 'undefined') {
        opener = window;
    }
    var email = opener.mQuery(origin);
    if (email.length == 0) return;
    var emailId = email.val();
    var disabled = emailId === '' || emailId === null;

    opener.mQuery('[id$=_editEmailButton]').prop('disabled', disabled);
    opener.mQuery('[id$=_previewEmailButton]').prop('disabled', disabled);
};

Autoborna.initEmailDynamicContent = function() {
    if (mQuery('#dynamic-content-container').length) {
        mQuery('#emailFilters .remove-selected').each( function (index, el) {
            mQuery(el).on('click', function () {
                mQuery(this).closest('.panel').animate(
                    {'opacity': 0},
                    'fast',
                    function () {
                        mQuery(this).remove();
                    }
                );

                if (!mQuery('#emailFilters li:not(.placeholder)').length) {
                    mQuery('#emailFilters li.placeholder').removeClass('hide');
                } else {
                    mQuery('#emailFilters li.placeholder').addClass('hide');
                }
            });
        });

        mQuery('#addNewDynamicContent').on('click', function (e) {
            e.preventDefault();

            Autoborna.createNewDynamicContentItem();
        });

        Autoborna.initDynamicContentItem();
    }
};

Autoborna.createNewDynamicContentItem = function(jQueryVariant) {
    // To support the parent.mQuery from the builder
    var mQuery = (typeof jQueryVariant != 'undefined') ? jQueryVariant : window.mQuery;

    var tabHolder               = mQuery('#dynamicContentTabs');
    var filterHolder            = mQuery('#dynamicContentContainer');
    var dynamicContentPrototype = mQuery('#dynamicContentPrototype').data('prototype');
    var dynamicContentIndex     = tabHolder.find('li').length - 1;
    while (mQuery('#emailform_dynamicContent_' + dynamicContentIndex).length > 0) {
        dynamicContentIndex++; // prevent duplicate ids
    }
    var tabId                   = '#emailform_dynamicContent_' + dynamicContentIndex;
    var tokenName               = 'Dynamic Content ' + (dynamicContentIndex + 1);
    var newForm                 = dynamicContentPrototype.replace(/__name__/g, dynamicContentIndex);
    var newTab                  = mQuery('<li><a role="tab" data-toggle="tab" href="' + tabId + '">' + tokenName + '</a></li>');

    tabHolder.append(newTab);
    filterHolder.append(newForm);

    var itemContainer = mQuery(tabId);
    var textarea      = itemContainer.find('.editor');
    var firstInput    = itemContainer.find('input[type="text"]').first();

    if (textarea.hasClass('legacy-builder')) {
        textarea.froalaEditor(mQuery.extend({}, Autoborna.basicFroalaOptions, {
            // Set custom buttons with separator between them.
            toolbarSticky: false,
            toolbarButtons: ['undo', 'redo', '|', 'bold', 'italic', 'underline', 'paragraphFormat', 'fontFamily', 'fontSize', 'color', 'align', 'formatOL', 'formatUL', 'quote', 'clearFormatting', 'token', 'insertLink', 'insertImage', 'insertTable', 'html', 'fullscreen'],
            heightMin: 100
        }));
    }

    if (Autoborna.internalDynamicContentItemCreateListeners) {
        Autoborna.internalDynamicContentItemCreateListeners.forEach(function(callback) {
            callback(textarea);
        });
    }

    tabHolder.find('i').first().removeClass('fa-spinner fa-spin').addClass('fa-plus text-success');
    newTab.find('a').tab('show');

    firstInput.focus();

    Autoborna.updateDynamicContentDropdown();

    Autoborna.initDynamicContentItem(tabId, mQuery, tokenName);

    return tabId;
};

Autoborna.dynamicContentAddNewItemListener = function(callback) {
    Autoborna.internalDynamicContentItemCreateListeners.push(callback);
}

Autoborna.createNewDynamicContentFilter = function(el, jQueryVariant) {
    // To support the parent.mQuery from the builder
    var mQuery = (typeof jQueryVariant != 'undefined') ? jQueryVariant : window.mQuery;

    var $this                = mQuery(el);
    var parentElement        = $this.parents('.panel');
    var tabHolder            = parentElement.find('.nav');
    var filterHolder         = parentElement.find('.tab-content');
    var filterBlockPrototype = mQuery('#filterBlockPrototype');
    var filterIndex          = filterHolder.find('.tab-pane').length - 1;
    var dynamicContentIndex  = $this.parents('.tab-pane').attr('id').match(/\d+$/)[0];

    var filterPrototype   = filterBlockPrototype.data('prototype');
    var filterContainerId = '#emailform_dynamicContent_' + dynamicContentIndex + '_filters_' + filterIndex ;
    // prevent duplicate ids
    while (mQuery(filterContainerId).length > 0) {
        filterIndex++;
        filterContainerId = '#emailform_dynamicContent_' + dynamicContentIndex + '_filters_' + filterIndex ;
    }
    var newTab            = mQuery('<li><a role="tab" data-toggle="tab" href="' + filterContainerId + '">Variation ' + (filterIndex + 1) + '</a></li>');
    var newForm           = filterPrototype.replace(/__name__/g, filterIndex)
        .replace(/dynamicContent_0_filters/g, 'dynamicContent_' + dynamicContentIndex + '_filters')
        .replace(/dynamicContent]\[0]\[filters/g, 'dynamicContent][' + dynamicContentIndex + '][filters');

    tabHolder.append(newTab);
    filterHolder.append(newForm);

    var filterContainer  = mQuery(filterContainerId);
    var availableFilters = filterContainer.find('select[data-autoborna="available_filters"]');
    var altTextarea      = filterContainer.find('.editor');
    var removeButton     = filterContainer.find('.remove-item');

    Autoborna.activateChosenSelect(availableFilters, false, mQuery);

    availableFilters.on('change', function() {
        var $this = mQuery(this);

        if ($this.val()) {
            Autoborna.addDynamicContentFilter($this.val(), mQuery);
            $this.val('');
            $this.trigger('chosen:updated');
        }
    });

    if (altTextarea.hasClass('legacy-builder')) {
        altTextarea.froalaEditor(mQuery.extend({}, Autoborna.basicFroalaOptions, {
            // Set custom buttons with separator between them.
            toolbarSticky: false,
            toolbarButtons: ['undo', 'redo', '|', 'bold', 'italic', 'underline', 'paragraphFormat', 'fontFamily', 'fontSize', 'color', 'align', 'formatOL', 'formatUL', 'quote', 'clearFormatting', 'token', 'insertLink', 'insertImage', 'insertTable', 'html', 'fullscreen'],
            heightMin: 100
        }));
    }

    if (Autoborna.internalDynamicContentFilterCreateListeners) {
        Autoborna.internalDynamicContentFilterCreateListeners.forEach(function(callback) {
            callback(altTextarea);
        });
    }

    Autoborna.initRemoveEvents(removeButton, mQuery);

    newTab.find('a').tab('show');

    return filterContainerId;
};

Autoborna.dynamicContentAddNewFilterListener = function(callback) {
    Autoborna.internalDynamicContentFilterCreateListeners.push(callback);
}

Autoborna.initDynamicContentItem = function (tabId, jQueryVariant, tokenName) {
    // To support the parent.mQuery from the builder
    var mQuery = (typeof jQueryVariant != 'undefined') ? jQueryVariant : window.mQuery;

    var $el = mQuery('#dynamic-content-container');
    if ($el.length === 0){
        mQuery = parent.mQuery;
        $el = mQuery('#dynamic-content-container');
    }

    if (tabId || typeof tabId != "undefined") {
        $el = mQuery(tabId);
    }

    // add a click event listener for adding a new dynamic content variant
    $el.find('.addNewDynamicContentFilter').on('click', function (e) {
        e.preventDefault();

        Autoborna.createNewDynamicContentFilter(this);
    });

    if (typeof tokenName != 'undefined') {
        $el.find('.dynamic-content-token-name').val(tokenName);
    }

    if ($el.find('.dynamic-content-token-name').val() === '') {
        var dynamicContent = $el.attr('id').match(/\d+$/);
        if (dynamicContent) {
            var dynamicContentIndex  = dynamicContent[0];
            $el.find('.dynamic-content-token-name').val('Dynamic Content ' + dynamicContentIndex);
        }
    }

    $el.find('a.remove-selected').on('click', function() {
        mQuery(this).closest('.panel').animate(
            {'opacity': 0},
            'fast',
            function () {
                mQuery(this).remove();
            }
        );
    });

    $el.find('select[data-autoborna="available_filters"]').on('change', function() {
        var $this = mQuery(this);

        if ($this.val()) {
            Autoborna.addDynamicContentFilter($this.val(), mQuery);
            $this.val('');
            $this.trigger('chosen:updated');
        }
    });

    Autoborna.initRemoveEvents($el.find('.remove-item'), mQuery);
};

Autoborna.updateDynamicContentDropdown = function () {
    var options = [];

    mQuery('#dynamicContentTabs').find('a[data-toggle="tab"]').each(function () {
        var prototype       = '<li><a class="fr-command" data-cmd="dynamicContent" data-param1="__tokenName__">__tokenName__</a></li>';
        var newOption       = prototype.replace(/__tokenName__/g, mQuery(this).text());

        options.push(newOption);
    });

    mQuery('button[data-cmd="dynamicContent"]').next().find('ul').html(options.join(''));
};

Autoborna.initRemoveEvents = function (elements, jQueryVariant) {
    var mQuery = (typeof jQueryVariant != 'undefined') ? jQueryVariant : window.mQuery;
    if (elements.hasClass('remove-selected')) {
        elements.on('click', function() {
            mQuery(this).closest('.panel').animate(
                {'opacity': 0},
                'fast',
                function () {
                    mQuery(this).remove();
                }
            );
        });
    } else {
        elements.on('click', function (e) {
            e.preventDefault();
            var $this         = mQuery(this);
            var parentElement = $this.parents('.tab-pane.dynamic-content');

            if ($this.hasClass('remove-filter')) {
                parentElement = $this.parents('.tab-pane.dynamic-content-filter');
            }

            var tabLink      = mQuery('a[href="#' + parentElement.attr('id') + '"]').parent();
            var tabContainer = tabLink.parent();

            parentElement.remove();
            tabLink.remove();
            // if tabContainer is for variants, show the first one, if it is the DEC vertical list, show the second one
            if (tabContainer.hasClass('tabs-left') || $this.hasClass('remove-filter')) {
                tabContainer.find('li').first().next().find('a').tab('show');
            } else {
                tabContainer.find('li').first().find('a').tab('show');
            }

            Autoborna.updateDynamicContentDropdown();
        });
    }
};

Autoborna.addDynamicContentFilter = function (selectedFilter, jQueryVariant) {
    var mQuery = (typeof jQueryVariant != 'undefined') ? jQueryVariant : window.mQuery;

    var dynamicContentItems  = mQuery('.tab-pane.dynamic-content');
    var activeDynamicContent = dynamicContentItems.filter(':visible');
    var dynamicContentIndex  = activeDynamicContent.attr('id').match(/\d+$/)[0]; //dynamicContentItems.index(activeDynamicContent);

    var dynamicContentFilterContainers      = activeDynamicContent.find('div[data-filter-container]');
    var activeDynamicContentFilterContainer = dynamicContentFilterContainers.filter(':visible');
    var dynamicContentFilterIndex           = dynamicContentFilterContainers.index(activeDynamicContentFilterContainer);

    var selectedOption  = mQuery('option[data-autoborna="available_' + selectedFilter + '"]').first();
    var label           = selectedOption.text();

    // create a new filter
    var filterNum   = activeDynamicContentFilterContainer.children('.panel').length;
    var prototype   = mQuery('#filterSelectPrototype').data('prototype');
    var fieldObject = selectedOption.data('field-object');
    var fieldType   = selectedOption.data('field-type');
    var isSpecial   = (mQuery.inArray(fieldType, ['leadlist', 'assets', 'lead_email_received', 'tags', 'multiselect', 'boolean', 'select', 'country', 'timezone', 'region', 'stage', 'locale']) != -1);

    // Update the prototype settings
    prototype = prototype.replace(/__name__/g, filterNum)
        .replace(/__label__/g, label)
        .replace(/dynamicContent_0_filters/g, 'dynamicContent_' + dynamicContentIndex + '_filters')
        .replace(/dynamicContent]\[0]\[filters/g, 'dynamicContent][' + dynamicContentIndex + '][filters')
        .replace(/filters_0_filters/g, 'filters_' + dynamicContentFilterIndex + '_filters')
        .replace(/filters]\[0]\[filters/g, 'filters][' + dynamicContentFilterIndex + '][filters');

    if (filterNum === 0) {
        prototype = prototype.replace(/in-group/g, '');
    }

    // Convert to DOM
    prototype = mQuery(prototype);

    if (fieldObject == 'company') {
        prototype.find('.object-icon').removeClass('fa-user').addClass('fa-building');
    } else {
        prototype.find('.object-icon').removeClass('fa-building').addClass('fa-user');
    }

    var filterBase  = "emailform[dynamicContent][" + dynamicContentIndex + "][filters][" + dynamicContentFilterIndex + "][filters][" + filterNum + "]";
    var filterIdBase = "emailform_dynamicContent_" + dynamicContentIndex + "_filters_" + dynamicContentFilterIndex + "_filters_" + filterNum;

    if (isSpecial) {
        var templateField = fieldType;
        if (fieldType == 'boolean' || fieldType == 'multiselect') {
            templateField = 'select';
        }
        var template = mQuery('#templates .' + templateField + '-template').clone();
        var $template = mQuery(template);
        var templateNameAttr = $template.attr('name').replace(/__name__/g, filterNum)
            .replace(/__dynamicContentIndex__/g, dynamicContentIndex)
            .replace(/__dynamicContentFilterIndex__/g, dynamicContentFilterIndex);
        var templateIdAttr = $template.attr('id').replace(/__name__/g, filterNum)
            .replace(/__dynamicContentIndex__/g, dynamicContentIndex)
            .replace(/__dynamicContentFilterIndex__/g, dynamicContentFilterIndex);

        $template.attr('name', templateNameAttr);
        $template.attr('id', templateIdAttr);

        prototype.find('input[name="' + filterBase + '[filter]"]').replaceWith(template);
    }

    if (activeDynamicContentFilterContainer.find('.panel').length == 0) {
        // First filter so hide the glue footer
        prototype.find(".panel-footer").addClass('hide');
    }

    prototype.find("input[name='" + filterBase + "[field]']").val(selectedFilter);
    prototype.find("input[name='" + filterBase + "[type]']").val(fieldType);
    prototype.find("input[name='" + filterBase + "[object]']").val(fieldObject);

    var filterEl = (isSpecial) ? "select[name='" + filterBase + "[filter]']" : "input[name='" + filterBase + "[filter]']";

    activeDynamicContentFilterContainer.append(prototype);

    Autoborna.initRemoveEvents(activeDynamicContentFilterContainer.find("a.remove-selected"), mQuery);

    var filter = '#' + filterIdBase + '_filter';

    var fieldOptions = fieldCallback = '';
    //activate fields
    if (isSpecial) {
        if (fieldType == 'select' || fieldType == 'boolean' || fieldType == 'multiselect') {
            // Generate the options
            fieldOptions = selectedOption.data("field-list");

            mQuery.each(fieldOptions, function(index, val) {
                mQuery('<option>').val(index).text(val).appendTo(filterEl);
            });
        }
    } else if (fieldType == 'lookup') {
        fieldCallback = selectedOption.data("field-callback");
        if (fieldCallback && typeof Autoborna[fieldCallback] == 'function') {
            fieldOptions = selectedOption.data("field-list");
            Autoborna[fieldCallback](filterIdBase + '_filter', selectedFilter, fieldOptions);
        }
    } else if (fieldType == 'datetime') {
        mQuery(filter).datetimepicker({
            format: 'Y-m-d H:i',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollMonth: false,
            scrollInput: false
        });
    } else if (fieldType == 'date') {
        mQuery(filter).datetimepicker({
            timepicker: false,
            format: 'Y-m-d',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollMonth: false,
            scrollInput: false,
            closeOnDateSelect: true
        });
    } else if (fieldType == 'time') {
        mQuery(filter).datetimepicker({
            datepicker: false,
            format: 'H:i',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollMonth: false,
            scrollInput: false
        });
    } else if (fieldType == 'lookup_id') {
        //switch the filter and display elements
        var oldFilter = mQuery(filterEl);
        var newDisplay = mQuery(oldFilter).clone();
        mQuery(newDisplay).attr('name', filterBase + '[display]')
            .attr('id', filterIdBase + '_display');

        var oldDisplay = mQuery(prototype).find("input[name='" + filterBase + "[display]']");
        var newFilter = mQuery(oldDisplay).clone();
        mQuery(newFilter).attr('name', filterBase + '[filter]')
            .attr('id', filterIdBase + '_filter');

        mQuery(oldFilter).replaceWith(newFilter);
        mQuery(oldDisplay).replaceWith(newDisplay);

        var fieldCallback = selectedOption.data("field-callback");
        if (fieldCallback && typeof Autoborna[fieldCallback] == 'function') {
            fieldOptions = selectedOption.data("field-list");
            Autoborna[fieldCallback](filterIdBase + '_display', selectedFilter, fieldOptions, mQuery);
        }
    } else {
        mQuery(filter).attr('type', fieldType);
    }

    var operators = mQuery(selectedOption).data('field-operators');
    mQuery('#' + filterIdBase + '_operator').html('');
    mQuery.each(operators, function (label, value) {
        var newOption = mQuery('<option/>').val(value).text(label);
        newOption.appendTo(mQuery('#' + filterIdBase + '_operator'));
    });

    // Convert based on first option in list
    Autoborna.convertDynamicContentFilterInput('#' + filterIdBase + '_operator', mQuery);
};

Autoborna.convertDynamicContentFilterInput = function(el, jQueryVariant) {
    var mQuery = (typeof jQueryVariant != 'undefined') ? jQueryVariant : window.mQuery;
    var operator = mQuery(el).val();
    // Extract the filter number
    var regExp    = /emailform_dynamicContent_(\d+)_filters_(\d+)_filters_(\d+)_operator/;
    var matches   = regExp.exec(mQuery(el).attr('id'));

    var dynamicContentIndex       = matches[1];
    var dynamicContentFilterIndex = matches[2];
    var filterNum                 = matches[3];

    var filterId       = '#emailform_dynamicContent_' + dynamicContentIndex + '_filters_' + dynamicContentFilterIndex + '_filters_' + filterNum + '_filter';
    var filterEl       = mQuery(filterId);
    var filterElParent = filterEl.parent();

    // Reset has-error
    if (filterElParent.hasClass('has-error')) {
        filterElParent.find('div.help-block').hide();
        filterElParent.removeClass('has-error');
    }

    var disabled = (operator == 'empty' || operator == '!empty');
    filterEl.prop('disabled', disabled);

    if (disabled) {
        filterEl.val('');
    }

    var newName = '';
    var lastPos;

    if (filterEl.is('select')) {
        var isMultiple  = filterEl.attr('multiple');
        var multiple    = (operator == 'in' || operator == '!in');
        var placeholder = filterEl.attr('data-placeholder');

        if (multiple && !isMultiple) {
            filterEl.attr('multiple', 'multiple');

            // Update the name
            newName =  filterEl.attr('name') + '[]';
            filterEl.attr('name', newName);

            placeholder = autobornaLang['chosenChooseMore'];
        } else if (!multiple && isMultiple) {
            filterEl.removeAttr('multiple');

            // Update the name
            newName = filterEl.attr('name');
            lastPos = newName.lastIndexOf('[]');
            newName = newName.substring(0, lastPos);

            filterEl.attr('name', newName);

            placeholder = autobornaLang['chosenChooseOne'];
        }

        if (multiple) {
            // Remove empty option
            filterEl.find('option[value=""]').remove();

            // Make sure none are selected
            filterEl.find('option:selected').removeAttr('selected');
        } else {
            // Add empty option
            filterEl.prepend("<option value='' selected></option>");
        }

        // Destroy the chosen and recreate
        Autoborna.destroyChosen(filterEl);

        filterEl.attr('data-placeholder', placeholder);

        Autoborna.activateChosenSelect(filterEl, false, mQuery);
    }
};
