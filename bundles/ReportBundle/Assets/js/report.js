//ReportBundle
Autoborna.reportOnLoad = function (container) {
    // Activate search if the container exists
    if (mQuery(container + ' #list-search').length) {
        Autoborna.activateSearchAutocomplete('list-search', 'report');
    }

    // Append an index of the number of filters on the edit form
    if (mQuery('div[id=report_filters]').length) {
        mQuery('div[id=report_filters]').attr('data-index', Autoborna.getHighestIndex('report_filters'));
        mQuery('div[id=report_tableOrder]').attr('data-index', Autoborna.getHighestIndex('report_tableOrder'));
        mQuery('div[id=report_aggregators]').attr('data-index', Autoborna.getHighestIndex('report_aggregators'));

        if (mQuery('.filter-columns').length) {
            mQuery('.filter-columns').each(function () {
                Autoborna.updateReportFilterValueInput(this, true);
                mQuery(this).on('change', function () {
                    Autoborna.updateReportFilterValueInput(this);
                });
            });
        }
    } else {
        mQuery('#report-shelves .collapse').on('show.bs.collapse', function (e) {
            var actives = mQuery('#report-shelves').find('.in, .collapsing');
            actives.each(function (index, element) {
                mQuery(element).collapse('hide');
                var id = mQuery(element).attr('id');
                mQuery('a[aria-controls="' + id + '"]').addClass('collapsed');
            })
        })
    }
    Autoborna.updateReportGlueTriggers();
    Autoborna.checkSelectedGroupBy();
    Autoborna.initDateRangePicker();

    var $isScheduled = mQuery('[data-report-schedule="isScheduled"]');
    var $unitTypeId = mQuery('[data-report-schedule="scheduleUnit"]');
    var $scheduleDay = mQuery('[data-report-schedule="scheduleDay"]');
    var $scheduleMonthFrequency = mQuery('[data-report-schedule="scheduleMonthFrequency"]');

    mQuery($isScheduled).change(function () {
        Autoborna.scheduleDisplay($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency);
    });
    mQuery($unitTypeId).change(function () {
        Autoborna.scheduleDisplay($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency);
    });
    mQuery($scheduleDay).change(function () {
        Autoborna.schedulePreview($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency);
    });
    mQuery($scheduleMonthFrequency).change(function () {
        Autoborna.schedulePreview($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency);
    });
    Autoborna.scheduleDisplay($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency);

    jQuery(document).ajaxComplete(function(){
        Autoborna.ajaxifyForm('daterange');
    });
};

Autoborna.scheduleDisplay = function ($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency) {
    Autoborna.checkIsScheduled($isScheduled);

    var unitVal = mQuery($unitTypeId).val();
    mQuery('#scheduleDay, #scheduleDay label, #scheduleMonthFrequency').hide();
    if (unitVal === 'WEEKLY' || unitVal === 'MONTHLY') {
        mQuery('#scheduleDay').show();
    }
    if (unitVal === 'MONTHLY') {
        mQuery('#scheduleMonthFrequency').show();
        mQuery('#scheduleDay label').hide();
    } else {
        mQuery('#scheduleDay label').show();
    }
    if($isScheduled.length) {
        Autoborna.schedulePreview($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency);
    }
};

Autoborna.schedulePreview = function ($isScheduled, $unitTypeId, $scheduleDay, $scheduleMonthFrequency) {
    var previewUrl = mQuery('#schedule_preview_url').data('url');
    var $schedulePreviewData = mQuery('#schedule_preview_data');

    var isScheduledVal = 0;
    if (!mQuery($isScheduled).prop("checked")) { //$isScheduled.val() does not work
        isScheduledVal = 1;
    }

    if (!isScheduledVal) {
        $schedulePreviewData.hide();

        return;
    }
    var unitVal = mQuery($unitTypeId).val();
    var scheduleDayVal = mQuery($scheduleDay).val();
    var scheduleMonthFrequencyVal = mQuery($scheduleMonthFrequency).val();

    mQuery.get(
        previewUrl + '/' + isScheduledVal + '/' + unitVal + '/' + scheduleDayVal + '/' + scheduleMonthFrequencyVal,
        function( data ) {
            if (!data.html) {
                return;
            }

            mQuery("#schedule_preview_data_content").html(data.html);
            $schedulePreviewData.show();
        }
    );
};

Autoborna.checkIsScheduled = function ($isScheduled) {
    var $scheduleForm = mQuery('#schedule-container .schedule_form');
    if (!mQuery($isScheduled).prop("checked")) {
        $scheduleForm.show();
        return;
    }
    $scheduleForm.hide();
};

/**
 * Written with inspiration from http://symfony.com/doc/current/cookbook/form/form_collections.html#allowing-new-tags-with-the-prototype
 */
Autoborna.addReportRow = function (elId) {
    // Container with the prototype markup
    var prototypeHolder = mQuery('div[id="' + elId + '"]');

    // Fetch the index
    var index = parseInt(prototypeHolder.attr('data-index'));
    if (!index) {
        index = 0;
    }

    index++;

    // Fetch the prototype markup
    var prototype = prototypeHolder.data('prototype');

    // Replace the placeholder with our index
    var output = prototype.replace(/__name__/g, index);

    // Increase the index for the next row
    prototypeHolder.attr('data-index', index);

    // Render the new row
    prototypeHolder.append(output);

    var newColumnId = '#' + elId + '_' + index + '_column';
    if (elId == 'report_filters') {
        if (typeof Autoborna.reportPrototypeFilterOptions != 'undefined') {
            // Update the column options if applicable
            mQuery(newColumnId).html(Autoborna.reportPrototypeFilterOptions);
        }

        // Add `in-group` class by default
        mQuery('#report_filters_' + index + '_container').addClass('in-group');

        mQuery(newColumnId).on('change', function () {
            Autoborna.updateReportFilterValueInput(this);
        });
        Autoborna.updateReportFilterValueInput(newColumnId);
        Autoborna.updateReportGlueTriggers();
    } else if (typeof Autoborna.reportPrototypeColumnOptions != 'undefined') {
        // Update the column options if applicable
        mQuery(newColumnId).html(Autoborna.reportPrototypeColumnOptions);
    }

    Autoborna.activateChosenSelect(mQuery('#' + elId + '_' + index + '_column'));
    mQuery("#" + elId + " *[data-toggle='tooltip']").tooltip({html: true, container: 'body'});

};

Autoborna.updateReportGlueTriggers = function () {
    var filterContainer = mQuery('#report_filters');
    var glueEl = filterContainer.find('.filter-glue');

    glueEl.off('change');
    glueEl.on('change', function () {
        var $this = mQuery(this);

        if ($this.val() === 'and') {
            $this.parents('.panel').addClass('in-group');
        } else {
            $this.parents('.panel').removeClass('in-group');
        }
    });
};

Autoborna.updateReportFilterValueInput = function (filterColumn, setup) {
    var definitions = (typeof Autoborna.reportPrototypeFilterDefinitions != 'undefined') ? Autoborna.reportPrototypeFilterDefinitions : mQuery('#report_filters').data('filter-definitions');
    var operators = (typeof Autoborna.reportPrototypeFilterOperators != 'undefined') ? Autoborna.reportPrototypeFilterOperators : mQuery('#report_filters').data('filter-operators');

    var newValue = mQuery(filterColumn).val();
    if (!newValue) {

        return;
    }

    var filterId = mQuery(filterColumn).attr('id');
    var filterType = definitions[newValue].type;

    // Get the value element
    var valueEl = mQuery(filterColumn).parent().parent().find('.filter-value');
    var valueVal = valueEl.val();

    var idParts = filterId.split("_");
    var valueId = 'report_filters_' + idParts[2] + '_value';
    var valueName = 'report[filters][' + idParts[2] + '][value]';

    // Replace the condition list with operators
    var currentOperator = mQuery('#report_filters_' + idParts[2] + '_condition').val();
    mQuery('#report_filters_' + idParts[2] + '_condition').html(operators[newValue]);
    if (mQuery('#report_filters_' + idParts[2] + '_condition option[value="' + currentOperator + '"]').length > 0) {
        mQuery('#report_filters_' + idParts[2] + '_condition').val(currentOperator);
    }

    // Replace the value field appropriately
    Autoborna.destroyChosen(mQuery('#' + valueId));

    if (filterType == 'bool' || filterType == 'boolean') {
        if (mQuery(valueEl).attr('type') != 'radio') {
            var template = mQuery('#filterValueYesNoTemplate .btn-group').clone(true);
            mQuery(template).find('input[type="radio"]').each(function () {
                mQuery(this).attr('name', valueName);
                var radioVal = mQuery(this).val();
                mQuery(this).attr('id', valueId + '_' + radioVal);
            });
            mQuery(valueEl).replaceWith(template);
        }

        if (setup) {
            mQuery('#' + valueId + '_' + valueVal).click();
        }
    } else if (mQuery(valueEl).attr('type') != 'text') {
        var newValueEl = mQuery('<input type="text" />').attr({
            id: valueId,
            name: valueName,
            'class': "form-control filter-value"
        });

        var replaceMe = (mQuery(valueEl).attr('type') == 'radio') ? mQuery(valueEl).parent().parent() : mQuery(valueEl);
        replaceMe.replaceWith(newValueEl);
    }

    if ((filterType == 'multiselect' || filterType == 'select') && typeof definitions[newValue].list != 'undefined') {
        // Activate a chosen
        var currentValue = mQuery(valueEl).val();

        var attr = {
            id: valueId,
            name: valueName,
            "class": 'form-control filter-value',
        };

        if (filterType == 'multiselect') {
            attr.multiple = true;
        }

        var newSelect = mQuery('<select />', attr);

        mQuery.each(definitions[newValue].list, function (value, label) {
            var newOption = mQuery('<option />')
                .val(value)
                .html(label);

            if (value == currentValue) {
                newOption.prop('selected', true);
            }

            newOption.appendTo(newSelect);
        });
        mQuery(valueEl).replaceWith(newSelect);

        Autoborna.activateChosenSelect(newSelect);
    }

    // Activate datetime
    if (filterType == 'datetime' || filterType == 'date' || filterType == 'time') {
        Autoborna.activateDateTimeInputs('#' + valueId, filterType);
    } else if (mQuery('#' + valueId).hasClass('calendar-activated')) {
        mQuery('#' + valueId).datetimepicker('destroy');
    }
};

Autoborna.removeReportRow = function (container) {
    mQuery("#" + container + " *[data-toggle='tooltip']").tooltip('destroy');
    mQuery('#' + container).remove();
};

Autoborna.updateReportSourceData = function (context) {
    Autoborna.activateLabelLoadingIndicator('report_source');
    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: 'post',
        data: "action=report:getSourceData&context=" + context,
        success: function (response) {
            mQuery('#report_columns').html(response.columns);
            mQuery('#report_columns').multiSelect('refresh');

            mQuery('#report_groupBy').html(response.columns);
            mQuery('#report_groupBy').multiSelect('refresh');

            // Remove any filters, they're no longer valid with different column lists
            mQuery('#report_filters').find('div').remove().end();

            // Reset index
            mQuery('#report_filters').data('index', 0);

            // Update columns
            Autoborna.reportPrototypeColumnOptions = mQuery(response.columns);

            // Remove order
            mQuery('#report_tableOrder').find('div').remove().end();

            // Reset index
            mQuery('#report_tableOrder').data('index', 0);
            mQuery('#report_aggregators').find('div').remove().end();
            // Reset index
            mQuery('#report_aggregators').data('index', 0);

            // Update filter list
            Autoborna.reportPrototypeFilterDefinitions = response.filterDefinitions;
            Autoborna.reportPrototypeFilterOptions = response.filters;
            Autoborna.reportPrototypeFilterOperators = response.filterOperators;

            mQuery('#report_graphs').html(response.graphs);
            mQuery('#report_graphs').multiSelect('refresh');

            if (!response.graphs) {
                mQuery('#graphs-container').addClass('hide');
                mQuery('#graphs-tab').addClass('hide');
            } else {
                mQuery('#graphs-container').removeClass('hide');
                mQuery('#graphs-tab').removeClass('hide');
            }
        },
        error: function (request, textStatus, errorThrown) {
            Autoborna.processAjaxError(request, textStatus, errorThrown);
        },
        complete: function () {
            Autoborna.removeLabelLoadingIndicator();
        }
    });
};

Autoborna.checkReportCondition = function (selector) {
    var option = mQuery('#' + selector + ' option:selected').val();
    var valueInput = selector.replace('condition', 'value');

    // Disable the value input if the condition is empty or notEmpty
    if (option == 'empty' || option == 'notEmpty') {
        mQuery('#' + valueInput).prop('disabled', true);
    } else {
        mQuery('#' + valueInput).prop('disabled', false);
    }
};

Autoborna.checkSelectedGroupBy = function () {
    var selectedOption = mQuery("select[name='report[groupBy][]'] option:selected").length;
    var existingAggregators = mQuery("select[name*='report[aggregators]']");
    if (selectedOption > 0) {
        mQuery('#aggregators-button').prop('disabled', false);
    } else {
        existingAggregators.each(function() {
            var containerId = mQuery(this).attr('id').replace('_column', '');
            Autoborna.removeReportRow(containerId + '_container');
        });
        mQuery('#aggregators-button').prop('disabled', true);
    }
};

Autoborna.getHighestIndex = function (selector) {
    var highestIndex = 1;
    var selectorChildren = mQuery('#' + selector + ' > div');

    selectorChildren.each(function() {
        var index = parseInt(mQuery(this).attr('id').split('_')[2]);
        highestIndex = (index > highestIndex) ? index : highestIndex;
    });

    return parseInt(highestIndex);
};
