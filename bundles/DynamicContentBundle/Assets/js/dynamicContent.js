/** DynamicContentBundle **/
Autoborna.toggleDwcFilters = function () {
    mQuery("#dwcFiltersTab, #slotNameDiv").toggleClass("hide");
    if (mQuery("#dwcFiltersTab").hasClass('hide')) {
        mQuery('.nav-tabs a[href="#details"]').click();
    } else {
        Autoborna.dynamicContentOnLoad();
    }
};

Autoborna.dynamicContentOnLoad = function (container, response) {
    if (typeof container !== 'object') {
        if (mQuery(container + ' #list-search').length) {
            Autoborna.activateSearchAutocomplete('list-search', 'dynamicContent');
        }
    }

    var availableFilters = mQuery('div.dwc-filter').find('select[data-autoborna="available_filters"]');
    Autoborna.activateChosenSelect(availableFilters, false);

    Autoborna.dynamicFiltersOnLoad('div.dwc-filter');
};

Autoborna.dynamicFiltersOnLoad = function(container, response) {

    mQuery('#campaign-share-tab').hover(function () {
        if (Autoborna.shareTableLoaded != true) {
            Autoborna.loadAjaxColumn('campaign-share-stat', 'lead:getCampaignShareStats', 'afterStatsLoad');
            Autoborna.shareTableLoaded = true;
        }
    })

    Autoborna.afterStatsLoad = function () {
        Autoborna.sortTableByColumn('#campaign-share-table', '.campaign-share-stat', true)
    }


    if (mQuery(container + ' #list-search').length) {
        Autoborna.activateSearchAutocomplete('list-search', 'lead.list');
    }

    var prefix = 'leadlist';
    var parent = mQuery('.dynamic-content-filter, .dwc-filter');
    if (parent.length) {
        prefix = parent.attr('id');
    }

    if (mQuery('#' + prefix + '_filters').length) {
        mQuery('#available_filters').on('change', function() {
            if (mQuery(this).val()) {
                Autoborna.addDwcFilter(mQuery(this).val(),mQuery('option:selected',this).data('field-object'));
                mQuery(this).val('');
                mQuery(this).trigger('chosen:updated');
            }
        });

        mQuery('#' + prefix + '_filters .remove-selected').each( function (index, el) {
            mQuery(el).on('click', function () {
                mQuery(this).closest('.panel').animate(
                    {'opacity': 0},
                    'fast',
                    function () {
                        mQuery(this).remove();
                        Autoborna.reorderSegmentFilters();
                    }
                );

                if (!mQuery('#' + prefix + '_filters li:not(.placeholder)').length) {
                    mQuery('#' + prefix + '_filters li.placeholder').removeClass('hide');
                } else {
                    mQuery('#' + prefix + '_filters li.placeholder').addClass('hide');
                }
            });
        });

        var bodyOverflow = {};
        mQuery('#' + prefix + '_filters').sortable({
            items: '.panel',
            helper: function(e, ui) {
                ui.children().each(function() {
                    if (mQuery(this).is(":visible")) {
                        mQuery(this).width(mQuery(this).width());
                    }
                });

                // Fix body overflow that messes sortable up
                bodyOverflow.overflowX = mQuery('body').css('overflow-x');
                bodyOverflow.overflowY = mQuery('body').css('overflow-y');
                mQuery('body').css({
                    overflowX: 'visible',
                    overflowY: 'visible'
                });

                return ui;
            },
            scroll: true,
            axis: 'y',
            stop: function(e, ui) {
                // Restore original overflow
                mQuery('body').css(bodyOverflow);

                // First in the list should be an "and"
                ui.item.find('select.glue-select').first().val('and');

                Autoborna.reorderSegmentFilters();
            }
        });

    }

    // segment contact filters
    var segmentContactForm = mQuery('#segment-contact-filters');

    if (segmentContactForm.length) {
        segmentContactForm.on('change', function() {
            segmentContactForm.submit();
        }).on('keyup', function() {
            segmentContactForm.delay(200).submit();
        }).on('submit', function(e) {
            e.preventDefault();
            Autoborna.refreshSegmentContacts(segmentContactForm);
        });
    }
};

Autoborna.addDwcFilter = function (elId, elObj) {
    var filterId = '#available_' + elObj + '_' + elId;
    var filterOption = mQuery(filterId);
    var label = filterOption.text();
    var alias = filterOption.val();

    // Create a new filter

    var filterNum = parseInt(mQuery('.available-filters').data('index'));
    mQuery('.available-filters').data('index', filterNum + 1);

    var prototypeStr = mQuery('.available-filters').data('prototype');
    var fieldType = filterOption.data('field-type');
    var fieldObject = filterOption.data('field-object');
    var isSpecial = (mQuery.inArray(fieldType, ['leadlist', 'campaign', 'device_type',  'device_brand', 'device_os', 'lead_email_received', 'lead_email_sent', 'tags', 'multiselect', 'boolean', 'select', 'country', 'timezone', 'region', 'stage', 'locale', 'globalcategory']) != -1);

    prototypeStr = prototypeStr.replace(/__name__/g, filterNum);
    prototypeStr = prototypeStr.replace(/__label__/g, label);

    // Convert to DOM
    prototype = mQuery(prototypeStr);

    var prefix = 'leadlist';
    var parent = mQuery(filterId).parents('.dynamic-content-filter, .dwc-filter');
    if (parent.length) {
        prefix = parent.attr('id');
    }

    var filterBase  = prefix + "[filters][" + filterNum + "]";
    var filterIdBase = prefix + "_filters_" + filterNum + "_";

    if (isSpecial) {
        var templateField = fieldType;
        if (fieldType == 'boolean' || fieldType == 'multiselect') {
            templateField = 'select';
        }

        var template = mQuery('#templates .' + templateField + '-template').clone();
        template.attr('name', mQuery(template).attr('name').replace(/__name__/g, filterNum));
        template.attr('id', mQuery(template).attr('id').replace(/__name__/g, filterNum));
        prototype.find('input[name="' + filterBase + '[filter]"]').replaceWith(template);
    }

    if (mQuery('#' + prefix + '_filters div.panel').length == 0) {
        // First filter so hide the glue footer
        prototype.find(".panel-heading").addClass('hide');
    }

    if (fieldObject == 'company') {
        prototype.find(".object-icon").removeClass('fa-user').addClass('fa-building');
    } else {
        prototype.find(".object-icon").removeClass('fa-building').addClass('fa-user');
    }
    prototype.find(".inline-spacer").append(fieldObject);

    prototype.find("a.remove-selected").on('click', function() {
        mQuery(this).closest('.panel').animate(
            {'opacity': 0},
            'fast',
            function () {
                mQuery(this).remove();
                Autoborna.reorderSegmentFilters();
            }
        );
    });

    prototype.find("input[name='" + filterBase + "[field]']").val(elId);
    prototype.find("input[name='" + filterBase + "[type]']").val(fieldType);
    prototype.find("input[name='" + filterBase + "[object]']").val(fieldObject);

    var filterEl = (isSpecial) ? "select[name='" + filterBase + "[filter]']" : "input[name='" + filterBase + "[filter]']";

    prototype.appendTo('#' + prefix + '_filters');

    var filter = mQuery('#' + filterIdBase + 'filter');

    //activate fields
    if (isSpecial) {
        if (fieldType == 'select' || fieldType == 'multiselect' || fieldType == 'boolean') {
            // Generate the options
            var fieldOptions = filterOption.data("field-list");
            mQuery.each(fieldOptions, function(index, val) {
                if (mQuery.isPlainObject(val)) {
                    var optGroup = index;
                    mQuery.each(val, function(index, value) {
                        mQuery('<option class="' + optGroup + '">').val(index).text(value).appendTo(filterEl);
                    });
                    mQuery('.' + index).wrapAll("<optgroup label='"+index+"' />");
                } else {
                    mQuery('<option>').val(index).text(val).appendTo(filterEl);
                }
            });
        }
    } else if (fieldType == 'lookup') {
        var fieldCallback = filterOption.data("field-callback");
        if (fieldCallback && typeof Autoborna[fieldCallback] == 'function') {
            var fieldOptions = filterOption.data("field-list");
            Autoborna[fieldCallback](filterIdBase + 'filter', elId, fieldOptions);
        } else {
            filter.attr('data-target', alias);
            Autoborna.activateLookupTypeahead(filter.parent());
        }
    } else if (fieldType == 'datetime') {
        filter.datetimepicker({
            format: 'Y-m-d H:i',
            lazyInit: true,
            validateOnBlur: false,
            allowBlank: true,
            scrollMonth: false,
            scrollInput: false
        });
    } else if (fieldType == 'date') {
        filter.datetimepicker({
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
        filter.datetimepicker({
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
        var newDisplay = oldFilter.clone();
        newDisplay.attr('name', filterBase + '[display]')
            .attr('id', filterIdBase + 'display');

        var oldDisplay = prototype.find("input[name='" + filterBase + "[display]']");
        var newFilter = mQuery(oldDisplay).clone();
        newFilter.attr('name', filterBase + '[filter]');
        newFilter.attr('id', filterIdBase + 'filter');

        oldFilter.replaceWith(newFilter);
        oldDisplay.replaceWith(newDisplay);

        var fieldCallback = filterOption.data("field-callback");
        if (fieldCallback && typeof Autoborna[fieldCallback] == 'function') {
            var fieldOptions = filterOption.data("field-list");
            Autoborna[fieldCallback](filterIdBase + 'display', elId, fieldOptions);
        }
    } else {
        filter.attr('type', fieldType);
    }

    var operators = filterOption.data('field-operators');
    mQuery('#' + filterIdBase + 'operator').html('');
    mQuery.each(operators, function (label, value) {
        var newOption = mQuery('<option/>').val(value).text(label);
        newOption.appendTo(mQuery('#' + filterIdBase + 'operator'));
    });

    // Convert based on first option in list
    Autoborna.convertDwcFilterInput('#' + filterIdBase + 'operator');

    // Reposition if applicable
    Autoborna.updateFilterPositioning(mQuery('#' + filterIdBase + 'glue'));
};

Autoborna.convertDwcFilterInput = function(el) {
    var prefix = 'leadlist';

    var parent = mQuery(el).parents('.dynamic-content-filter, .dwc-filter');
    if (parent.length) {
        prefix = parent.attr('id');
    }

    var operator = mQuery(el).val();

    // Extract the filter number
    var regExp    = /_filters_(\d+)_operator/;
    var matches   = regExp.exec(mQuery(el).attr('id'));
    var filterNum = matches[1];
    var filterId  = '#' + prefix + '_filters_' + filterNum + '_filter';

    // Reset has-error
    if (mQuery(filterId).parent().hasClass('has-error')) {
        mQuery(filterId).parent().find('div.help-block').hide();
        mQuery(filterId).parent().removeClass('has-error');
    }

    var disabled = (operator == 'empty' || operator == '!empty');
    mQuery(filterId+', #' + prefix + '_filters_' + filterNum + '_display').prop('disabled', disabled);

    if (disabled) {
        mQuery(filterId).val('');
    }

    var newName = '';
    var lastPos;

    if (mQuery(filterId).is('select')) {
        var isMultiple  = mQuery(filterId).attr('multiple');
        var multiple    = (operator == 'in' || operator == '!in');
        var placeholder = mQuery(filterId).attr('data-placeholder');

        if (multiple && !isMultiple) {
            mQuery(filterId).attr('multiple', 'multiple');

            // Update the name
            newName =  mQuery(filterId).attr('name') + '[]';
            mQuery(filterId).attr('name', newName);

            placeholder = autobornaLang['chosenChooseMore'];
        } else if (!multiple && isMultiple) {
            mQuery(filterId).removeAttr('multiple');

            // Update the name
            newName = mQuery(filterId).attr('name');
            lastPos = newName.lastIndexOf('[]');
            newName = newName.substring(0, lastPos);

            mQuery(filterId).attr('name', newName);

            placeholder = autobornaLang['chosenChooseOne'];
        }

        if (multiple) {
            // Remove empty option
            mQuery(filterId).find('option[value=""]').remove();

            // Make sure none are selected
            mQuery(filterId + ' option:selected').removeAttr('selected');
        } else {
            // Add empty option
            mQuery(filterId).prepend("<option value='' selected></option>");
        }

        // Destroy the chosen and recreate
        Autoborna.destroyChosen(mQuery(filterId));

        mQuery(filterId).attr('data-placeholder', placeholder);

        Autoborna.activateChosenSelect(mQuery(filterId));
    }
};

Autoborna.standardDynamicContentUrl = function(options) {
    if (!options) {
        return;
    }

    var url = options.windowUrl;
    if (url) {
        var editDynamicContentKey = '/dwc/edit/dynamicContentId';
        var previewDynamicContentKey = '/dwc/preview/dynamicContentId';
        if (url.indexOf(editDynamicContentKey) > -1 ||
            url.indexOf(previewDynamicContentKey) > -1) {
            options.windowUrl = url.replace('dynamicContentId', mQuery('#campaignevent_properties_dynamicContent').val());
        }
    }

    return options;
};

Autoborna.disabledDynamicContentAction = function(opener) {
    if (typeof opener == 'undefined') {
        opener = window;
    }

    var dynamicContent = opener.mQuery('#campaignevent_properties_dynamicContent').val();

    var disabled = dynamicContent === '' || dynamicContent === null;

    opener.mQuery('#campaignevent_properties_editDynamicContentButton').prop('disabled', disabled);
};

if (typeof AutobornaIsDwcReady === 'undefined') {
    var AutobornaIsDwcReady = true;

    if (
        document.readyState === "complete" ||
        !(document.readyState === "loading" || document.documentElement.doScroll)
    ) {
        Autoborna.dynamicContentOnLoad();
    } else {
        document.addEventListener("DOMContentLoaded", Autoborna.dynamicContentOnLoad);
    }
}
