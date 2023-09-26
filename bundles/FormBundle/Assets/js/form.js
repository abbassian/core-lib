//FormBundle
Autoborna.formOnLoad = function (container) {

    if (mQuery(container + ' #list-search').length) {
        Autoborna.activateSearchAutocomplete('list-search', 'form.form');
    }

    Autoborna.formBuilderNewComponentInit();
    Autoborna.iniNewConditionalField();

    var bodyOverflow = {};

    if (mQuery('#autobornaforms_fields')) {
        //make the fields sortable
        mQuery('#autobornaforms_fields').sortable({
            items: '.panel',
            cancel: '',
            helper: function(e, ui) {
                ui.children().each(function() {
                    mQuery(this).width(mQuery(this).width());
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
            containment: '#autobornaforms_fields .drop-here',
            stop: function(e, ui) {
                // Restore original overflow
                mQuery('body').css(bodyOverflow);
                mQuery(ui.item).attr('style', '');

                mQuery.ajax({
                    type: "POST",
                    url: autobornaAjaxUrl + "?action=form:reorderFields",
                    data: mQuery('#autobornaforms_fields').sortable("serialize", {attribute: 'data-sortable-id'}) + "&formId=" + mQuery('#autobornaform_sessionId').val()
                });
            }
        });

        Autoborna.initFormFieldButtons();
    }

    if (mQuery('#autobornaforms_actions')) {
        //make the fields sortable
        mQuery('#autobornaforms_actions').sortable({
            items: '.panel',
            cancel: '',
            helper: function(e, ui) {
                ui.children().each(function() {
                    mQuery(this).width(mQuery(this).width());
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
            containment: '#autobornaforms_actions .drop-here',
            stop: function(e, ui) {
                // Restore original overflow
                mQuery('body').css(bodyOverflow);
                mQuery(ui.item).attr('style', '');

                mQuery.ajax({
                    type: "POST",
                    url: autobornaAjaxUrl + "?action=form:reorderActions",
                    data: mQuery('#autobornaforms_actions').sortable("serialize") + "&formId=" + mQuery('#autobornaform_sessionId').val()
                });
            }
        });

        mQuery('#autobornaforms_actions .autobornaform-row').on('dblclick.autobornaformactions', function(event) {
            event.preventDefault();
            mQuery(this).find('.btn-edit').first().click();
        });
    }

    if (mQuery('#autobornaform_formType').length && mQuery('#autobornaform_formType').val() == '') {
        mQuery('body').addClass('noscroll');
    }

    Autoborna.initHideItemButton('#autobornaforms_fields');
    Autoborna.initHideItemButton('#autobornaforms_actions');
};

Autoborna.formBuilderNewComponentInit = function () {
    mQuery('select.form-builder-new-component').change(function (e) {
        mQuery(this).find('option:selected');
        Autoborna.ajaxifyModal(mQuery(this).find('option:selected'));
        // Reset the dropdown
        mQuery(this).val('');
        mQuery(this).trigger('chosen:updated');
    });
}

Autoborna.updateFormFields = function () {
    Autoborna.activateLabelLoadingIndicator('campaignevent_properties_field');

    var formId = mQuery('#campaignevent_properties_form').val();
    Autoborna.ajaxActionRequest('form:updateFormFields', {'formId': formId}, function(response) {
        if (response.fields) {
            var select = mQuery('#campaignevent_properties_field');
            select.find('option').remove();
            var fieldOptions = {};
            mQuery.each(response.fields, function(key, field) {
                var option = mQuery('<option></option>')
                    .attr('value', field.alias)
                    .text(field.label);
                select.append(option);
                fieldOptions[field.alias] = field.options;
            });
            select.attr('data-field-options', JSON.stringify(fieldOptions));
            select.trigger('chosen:updated');
            Autoborna.updateFormFieldValues(select);
        }
        Autoborna.removeLabelLoadingIndicator();
    });
};

Autoborna.updateFormFieldValues = function (field) {
    field = mQuery(field);
    var fieldValue = field.val();
    var options = jQuery.parseJSON(field.attr('data-field-options'));
    var valueField = mQuery('#campaignevent_properties_value');
    var valueFieldAttrs = {
        'class': valueField.attr('class'),
        'id': valueField.attr('id'),
        'name': valueField.attr('name'),
        'autocomplete': valueField.attr('autocomplete'),
        'value': valueField.attr('value')
    };

    if (typeof options[fieldValue] !== 'undefined' && !mQuery.isEmptyObject(options[fieldValue])) {
        var newValueField = mQuery('<select/>')
            .attr('class', valueFieldAttrs['class'])
            .attr('id', valueFieldAttrs['id'])
            .attr('name', valueFieldAttrs['name'])
            .attr('autocomplete', valueFieldAttrs['autocomplete'])
            .attr('value', valueFieldAttrs['value']);
        mQuery.each(options[fieldValue], function(key, optionVal) {
            var option = mQuery("<option></option>")
                .attr('value', key)
                .text(optionVal);
            newValueField.append(option);
        });
        valueField.replaceWith(newValueField);
    } else {
        var newValueField = mQuery('<input/>')
            .attr('type', 'text')
            .attr('class', valueFieldAttrs['class'])
            .attr('id', valueFieldAttrs['id'])
            .attr('name', valueFieldAttrs['name'])
            .attr('autocomplete', valueFieldAttrs['autocomplete'])
            .attr('value', valueFieldAttrs['value']);
        valueField.replaceWith(newValueField);
    }
};

Autoborna.formFieldOnLoad = function (container, response) {
    //new field created so append it to the form
    if (response.fieldHtml) {
        var newHtml = response.fieldHtml;
        var fieldId = '#autobornaform_' + response.fieldId;
        var fieldContainer = mQuery(fieldId).closest('.form-field-wrapper');

        if (mQuery(fieldId).length) {
            //replace content
            mQuery(fieldContainer).replaceWith(newHtml);
            var newField = false;
        } else {
            var parentContainer = mQuery('#autobornaform_'+response.parent);
            if (parentContainer.length) {
                (parentContainer.parents('.panel:first')).append(newHtml);
            }else {
                //append content
                var panel = mQuery('#autobornaforms_fields .autobornaform-button-wrapper').closest('.form-field-wrapper');
                panel.before(newHtml);
            }
            var newField = true;
        }

        // Get the updated element
        var fieldContainer = mQuery(fieldId).closest('.form-field-wrapper');

        //activate new stuff
        mQuery(fieldContainer).find("[data-toggle='ajax']").click(function (event) {
            event.preventDefault();
            return Autoborna.ajaxifyLink(this, event);
        });

        //initialize tooltips
        mQuery(fieldContainer).find("*[data-toggle='tooltip']").tooltip({html: true});

        //initialize ajax'd modals
        mQuery(fieldContainer).find("[data-toggle='ajaxmodal']").on('click.ajaxmodal', function (event) {
            event.preventDefault();
            Autoborna.ajaxifyModal(this, event);
        });

        Autoborna.initFormFieldButtons(fieldContainer);
        Autoborna.initHideItemButton(fieldContainer);

        //show fields panel
        if (!mQuery('#fields-panel').hasClass('in')) {
            mQuery('a[href="#fields-panel"]').trigger('click');
        }

        if (newField) {
            mQuery('.bundle-main-inner-wrapper').scrollTop(mQuery('.bundle-main-inner-wrapper').height());
        }

        if (mQuery('#form-field-placeholder').length) {
            mQuery('#form-field-placeholder').remove();
        }

        Autoborna.activateChosenSelect(mQuery('.form-builder-new-component'));
        Autoborna.formBuilderNewComponentInit();
        Autoborna.iniNewConditionalField();
    }
};

Autoborna.iniNewConditionalField = function(){
    mQuery('.add-new-conditional-field').click(function (e) {
        e.preventDefault();
        mQuery(this).parent().next().show('normal');
    })
    mQuery('.add-new-conditional-field').parent().next().hide();

}

Autoborna.initFormFieldButtons = function (container) {
    if (typeof container == 'undefined') {
        mQuery('#autobornaforms_fields .autobornaform-row').off(".autobornaformfields");
        var container = '#autobornaforms_fields';
    }

    mQuery(container).find('.autobornaform-row').on('dblclick.autobornaformfields', function(event) {
        event.preventDefault();
        mQuery(this).closest('.form-field-wrapper').find('.btn-edit').first().click();
    });
};

Autoborna.formActionOnLoad = function (container, response) {
    //new action created so append it to the form
    if (response.actionHtml) {
        var newHtml = response.actionHtml;
        var actionId = '#autobornaform_action_' + response.actionId;
        if (mQuery(actionId).length) {
            //replace content
            mQuery(actionId).replaceWith(newHtml);
            var newField = false;
        } else {
            //append content
            mQuery(newHtml).appendTo('#autobornaforms_actions');
            var newField = true;
        }
        //activate new stuff
        mQuery(actionId + " [data-toggle='ajax']").click(function (event) {
            event.preventDefault();
            return Autoborna.ajaxifyLink(this, event);
        });
        //initialize tooltips
        mQuery(actionId + " *[data-toggle='tooltip']").tooltip({html: true});

        //initialize ajax'd modals
        mQuery(actionId + " [data-toggle='ajaxmodal']").on('click.ajaxmodal', function (event) {
            event.preventDefault();

            Autoborna.ajaxifyModal(this, event);
        });

        Autoborna.initHideItemButton(actionId);

        mQuery('#autobornaforms_actions .autobornaform-row').off(".autobornaform");
        mQuery('#autobornaforms_actions .autobornaform-row').on('dblclick.autobornaformactions', function(event) {
            event.preventDefault();
            mQuery(this).find('.btn-edit').first().click();
        });

        //show actions panel
        if (!mQuery('#actions-panel').hasClass('in')) {
            mQuery('a[href="#actions-panel"]').trigger('click');
        }

        if (newField) {
            mQuery('.bundle-main-inner-wrapper').scrollTop(mQuery('.bundle-main-inner-wrapper').height());
        }

        if (mQuery('#form-action-placeholder').length) {
            mQuery('#form-action-placeholder').remove();
        }
    }
};

Autoborna.initHideItemButton = function(container) {
    mQuery(container).find('[data-hide-panel]').click(function(e) {
        e.preventDefault();
        mQuery(this).closest('.panel,.panel2').hide('fast');
    });
}

Autoborna.onPostSubmitActionChange = function(value) {
    if (value == 'return') {
        //remove required class
        mQuery('#autobornaform_postActionProperty').prev().removeClass('required');
    } else {
        mQuery('#autobornaform_postActionProperty').prev().addClass('required');
    }

    mQuery('#autobornaform_postActionProperty').next().html('');
    mQuery('#autobornaform_postActionProperty').parent().removeClass('has-error');
};

Autoborna.selectFormType = function(formType) {
    if (formType == 'standalone') {
        mQuery('option.action-standalone-only').removeClass('hide');
        mQuery('.page-header h3').text(autobornaLang.newStandaloneForm);
    } else {
        mQuery('option.action-standalone-only').addClass('hide');
        mQuery('.page-header h3').text(autobornaLang.newCampaignForm);
    }

    mQuery('.available-actions select').trigger('chosen:updated');

    mQuery('#autobornaform_formType').val(formType);

    mQuery('body').removeClass('noscroll');

    mQuery('.form-type-modal').remove();
    mQuery('.form-type-modal-backdrop').remove();
};