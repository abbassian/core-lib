/* PluginBundle */
Autoborna.matchedFields = function (index, object, integration) {
    var compoundAutobornaFields = ['autobornaContactId','autobornaContactTimelineLink'];

    if (mQuery('#integration_details_featureSettings_updateDncByDate_0').is(':checked')) {
        compoundAutobornaFields.push('autobornaContactIsContactableByEmail');
    }
    var integrationField = mQuery('#integration_details_featureSettings_'+object+'Fields_i_' + index).attr('data-value');
    var autobornaField = mQuery('#integration_details_featureSettings_'+object+'Fields_m_' + index + ' option:selected').val();

    if(mQuery('.btn-arrow' + index).parent().attr('data-force-direction') != 1) {
        if (mQuery.inArray(autobornaField, compoundAutobornaFields) >= 0) {
            mQuery('.btn-arrow' + index).removeClass('active');
            mQuery('#integration_details_featureSettings_' + object + 'Fields_update_autoborna' + index + '_0').attr('checked', 'checked');
            mQuery('input[name="integration_details[featureSettings][' + object + 'Fields][update_autoborna' + index + ']"]').prop('disabled', true).trigger("chosen:updated");
            mQuery('.btn-arrow' + index).addClass('disabled');
        }
        else {
            mQuery('input[name="integration_details[featureSettings][' + object + 'Fields][update_autoborna' + index + ']"]').prop('disabled', false).trigger("chosen:updated");
            mQuery('.btn-arrow' + index).removeClass('disabled');
        }
    }

    if (object == 'lead') {
        var updateAutobornaField = mQuery('input[name="integration_details[featureSettings]['+object+'Fields][update_autoborna' + index + ']"]:checked').val();
    } else {
        var updateAutobornaField = mQuery('input[name="integration_details[featureSettings]['+object+'Fields][update_autoborna_company' + index + ']"]:checked').val();
    }
    Autoborna.ajaxActionRequest('plugin:matchFields', {object: object, integration: integration, integrationField : integrationField, autobornaField: autobornaField, updateAutoborna : updateAutobornaField}, function(response) {
        var theMessage = (response.success) ? '<i class="fa fa-check-circle text-success"></i>' : '';
        mQuery('#matched-' + index + "-" + object).html(theMessage);
    });
};
Autoborna.initiateIntegrationAuthorization = function() {
    mQuery('#integration_details_in_auth').val(1);

    Autoborna.postForm(mQuery('form[name="integration_details"]'), 'loadIntegrationAuthWindow');
};

Autoborna.loadIntegrationAuthWindow = function(response) {
    if (response.newContent) {
        Autoborna.processModalContent(response, '#IntegrationEditModal');
    } else {
        Autoborna.stopPageLoadingBar();
        Autoborna.stopIconSpinPostEvent();
        mQuery('#integration_details_in_auth').val(0);

        if (response.authUrl) {
            var generator = window.open(response.authUrl, 'integrationauth', 'height=500,width=500');

            if (!generator || generator.closed || typeof generator.closed == 'undefined') {
                alert(autobornaLang.popupBlockerMessage);
            }
        }
    }
};

Autoborna.refreshIntegrationForm = function() {
    var opener = window.opener;
    if(opener) {
            var form = opener.mQuery('form[name="integration_details"]');
            if (form.length) {
                var action = form.attr('action');
                if (action) {
                    opener.Autoborna.startModalLoadingBar('#IntegrationEditModal');
                    opener.Autoborna.loadAjaxModal('#IntegrationEditModal', action);
                }
            }
    }

    window.close()
};

Autoborna.integrationOnLoad = function(container, response) {
    if (response && response.name) {
        var integration = '.integration-' + response.name;
        if (response.enabled) {
            mQuery(integration).removeClass('integration-disabled');
        } else {
            mQuery(integration).addClass('integration-disabled');
        }
    } else {
        Autoborna.filterIntegrations();
    }
    mQuery('[data-toggle="tooltip"]').tooltip();
};

Autoborna.integrationConfigOnLoad = function(container) {
    if (mQuery('.fields-container select.integration-field').length) {
        var selects = mQuery('.fields-container select.integration-field');
        selects.on('change', function() {
            var select   = mQuery(this),
                newValue = select.val(),
                previousValue = select.attr('data-value');
            select.attr('data-value', newValue);

            var groupSelects = mQuery(this).closest('.fields-container').find('select.integration-field').not(select);

            // Enable old value
            if (previousValue) {
                mQuery('option[value="' + previousValue + '"]', groupSelects).each(function() {
                    if (!mQuery(this).closest('select').prop('disabled')) {
                        mQuery(this).prop('disabled', false);
                        mQuery(this).removeAttr('disabled');
                    }
                });
            }

            if (newValue) {
                mQuery('option[value="' + newValue + '"]', groupSelects).each(function() {
                    if (!mQuery(this).closest('select').prop('disabled')) {
                        mQuery(this).prop('disabled', true);
                        mQuery(this).attr('disabled', 'disabled');
                    }
                });
            }

            groupSelects.each(function() {
                mQuery(this).trigger('chosen:updated');
            });
        });

        selects.each(function() {
            if (!mQuery(this).closest('.field-container').hasClass('hide')) {
                mQuery(this).trigger('change');
            }
        });
    }
};

Autoborna.filterIntegrations = function(update) {
    var filter = mQuery('#integrationFilter').val();

    if (update) {
        mQuery.ajax({
            url: autobornaAjaxUrl,
            type: "POST",
            data: "action=plugin:setIntegrationFilter&plugin=" + filter
        });
    }

    //activate shuffles
    if (mQuery('.native-integrations').length) {
        //give a slight delay in order for images to load so that shuffle starts out with correct dimensions
        setTimeout(function () {
            var Shuffle = window.Shuffle,
                element = document.querySelector('.native-integrations'),
                shuffleOptions = {
                    itemSelector: '.shuffle-item'
                };

            // Using global variable to make it available outside of the scope of this function
            window.nativeIntegrationsShuffleInstance = new Shuffle(element, shuffleOptions);

            window.nativeIntegrationsShuffleInstance.filter(function($el) {
                if (filter) {
                    return mQuery($el).hasClass('plugin' + filter);
                } else {
                    // Shuffle.js has a bug. It hides the first item when we reset the filter.
                    // This fixes it.
                    mQuery(shuffleOptions.itemSelector).first().css('transform', '');
                    return true;
                }
            });

            // Update shuffle on sidebar minimize/maximize
            mQuery("html")
                .on("fa.sidebar.minimize", function() {
                    setTimeout(function() {
                        window.nativeIntegrationsShuffleInstance.update();
                    }, 1000);
                })
                .on("fa.sidebar.maximize", function() {
                    setTimeout(function() {
                        window.nativeIntegrationsShuffleInstance.update();
                    }, 1000);
                });

            // This delay is needed so that the tab has time to render and the sizes are correctly calculated
            mQuery('#plugin-nav-tabs a').click(function () {
                setTimeout(function() {
                    window.nativeIntegrationsShuffleInstance.update();
                }, 500);
            });
        }, 500);
    }
};

Autoborna.getIntegrationLeadFields = function (integration, el, settings) {

    if (typeof settings == 'undefined') {
        settings = {};
    }
    settings.integration = integration;
    settings.object      = 'lead';

    Autoborna.getIntegrationFields(settings, 1, el);
};

Autoborna.getIntegrationCompanyFields = function (integration, el, settings) {
    if (typeof settings == 'undefined') {
        settings = {};
    }
    settings.integration = integration;
    settings.object      = 'company';

    Autoborna.getIntegrationFields(settings, 1, el);
};

Autoborna.getIntegrationFields = function(settings, page, el) {
    var object    = settings.object ? settings.object : 'lead';
    var fieldsTab = ('lead' === object) ? '#fields-tab' : '#'+object+'-fields-container';

    if (el && mQuery(el).is('input')) {
        Autoborna.activateLabelLoadingIndicator(mQuery(el).attr('id'));

        var namePrefix = mQuery(el).attr('name').split('[')[0];
        if ('integration_details' !== namePrefix) {
            var nameParts = mQuery(el).attr('name').match(/\[.*?\]+/g);
            nameParts = nameParts.slice(0, -1);
            settings.prefix = namePrefix + nameParts.join('') + "[" + object + "Fields]";
        }
    }
    var fieldsContainer = '#'+object+'FieldsContainer';

    var inModal = mQuery(fieldsContainer).closest('.modal');
    if (inModal) {
        var modalId = '#'+mQuery(fieldsContainer).closest('.modal').attr('id');
        Autoborna.startModalLoadingBar(modalId);
    }

    Autoborna.ajaxActionRequest('plugin:getIntegrationFields',
        {
            page: page,
            integration: (settings.integration) ? settings.integration : null,
            settings: settings
        },
        function(response) {
            if (response.success) {
                mQuery(fieldsContainer).replaceWith(response.html);
                Autoborna.onPageLoad(fieldsContainer);
                Autoborna.integrationConfigOnLoad(fieldsContainer);
                if (mQuery(fieldsTab).length) {
                    mQuery(fieldsTab).removeClass('hide');
                }
            } else {
                if (mQuery(fieldsTab).length) {
                    mQuery(fieldsTab).addClass('hide');
                }
            }

            if (el) {
                Autoborna.removeLabelLoadingIndicator();
            }

            if (inModal) {
                Autoborna.stopModalLoadingBar(modalId);
            }
        }
    );
};

Autoborna.getIntegrationConfig = function (el, settings) {
    Autoborna.activateLabelLoadingIndicator(mQuery(el).attr('id'));

    if (typeof settings == 'undefined') {
        settings = {};
    }

    settings.name = mQuery(el).attr('name');
    var data = {integration: mQuery(el).val(), settings: settings};
    mQuery('.integration-campaigns-status').html('');
    mQuery('.integration-config-container').html('');

    Autoborna.ajaxActionRequest('plugin:getIntegrationConfig', data,
        function (response) {
            if (response.success) {
                mQuery('.integration-config-container').html(response.html);
                Autoborna.onPageLoad('.integration-config-container', response);
            }

            Autoborna.integrationConfigOnLoad('.integration-config-container');
            Autoborna.removeLabelLoadingIndicator();
        }
    );


};

Autoborna.getIntegrationCampaignStatus = function (el, settings) {
    Autoborna.activateLabelLoadingIndicator(mQuery(el).attr('id'));
    if (typeof settings == 'undefined') {
        settings = {};
    }

    // Extract the name and ID prefixes
    var prefix = mQuery(el).attr('name').split("[")[0];

    settings.name = mQuery('#'+prefix+'_properties_integration').attr('name');
    var data = {integration:mQuery('#'+prefix+'_properties_integration').val(),campaign: mQuery(el).val(), settings: settings};

    mQuery('.integration-campaigns-status').html('');
    mQuery('.integration-campaigns-status').removeClass('hide');
    Autoborna.ajaxActionRequest('plugin:getIntegrationCampaignStatus', data,
        function (response) {

            if (response.success) {
                mQuery('.integration-campaigns-status').append(response.html);
                Autoborna.onPageLoad('.integration-campaigns-status', response);
            }

            Autoborna.integrationConfigOnLoad('.integration-campaigns-status');
            Autoborna.removeLabelLoadingIndicator();
        }
    );
};

Autoborna.getIntegrationCampaigns = function (el, settings) {
    Autoborna.activateLabelLoadingIndicator(mQuery(el).attr('id'));

    var data = {integration: mQuery(el).val()};

    mQuery('.integration-campaigns').html('');

    Autoborna.ajaxActionRequest('plugin:getIntegrationCampaigns', data,
        function (response) {
            if (response.success) {
                mQuery('.integration-campaigns').html(response.html);
                Autoborna.onPageLoad('.integration-campaigns', response);
            }

            Autoborna.integrationConfigOnLoad('.integration-campaigns');
            Autoborna.removeLabelLoadingIndicator();
        }
    );
};