//PageBundle
Autoborna.pageOnLoad = function (container, response) {
    if (mQuery(container + ' #list-search').length) {
        Autoborna.activateSearchAutocomplete('list-search', 'page.page');
    }

    if (mQuery(container + ' #page_template').length) {
        Autoborna.toggleBuilderButton(mQuery('#page_template').val() == '');

        //Handle autohide of "Redirect URL" field if "Redirect Type" is none
        if (mQuery(container + ' select[name="page[redirectType]"]').length) {
            //Auto-hide on page loading
            Autoborna.autoHideRedirectUrl(container);

            //Auto-hide on select changing
            mQuery(container + ' select[name="page[redirectType]"]').chosen().change(function(){
                Autoborna.autoHideRedirectUrl(container);
            });
        }

        // Preload tokens for code mode builder
        Autoborna.getTokens(Autoborna.getBuilderTokensMethod(), function(){});
        Autoborna.initSelectTheme(mQuery('#page_template'));
    }

    // Open the builder directly when saved from the builder
    if (response && response.inBuilder) {
        Autoborna.launchBuilder('page');
        Autoborna.processBuilderErrors(response);
    }
};

Autoborna.getPageAbTestWinnerForm = function(abKey) {
    if (abKey && mQuery(abKey).val() && mQuery(abKey).closest('.form-group').hasClass('has-error')) {
        mQuery(abKey).closest('.form-group').removeClass('has-error');
        if (mQuery(abKey).next().hasClass('help-block')) {
            mQuery(abKey).next().remove();
        }
    }

    Autoborna.activateLabelLoadingIndicator('page_variantSettings_winnerCriteria');

    var pageId = mQuery('#page_sessionId').val();
    var query  = "action=page:getAbTestForm&abKey=" + mQuery(abKey).val() + "&pageId=" + pageId;

    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: "POST",
        data: query,
        dataType: "json",
        success: function (response) {
            if (typeof response.html != 'undefined') {
                if (mQuery('#page_variantSettings_properties').length) {
                    mQuery('#page_variantSettings_properties').replaceWith(response.html);
                } else {
                    mQuery('#page_variantSettings').append(response.html);
                }

                if (response.html != '') {
                    Autoborna.onPageLoad('#page_variantSettings_properties', response);
                }
            }

            Autoborna.removeLabelLoadingIndicator();

        },
        error: function (request, textStatus, errorThrown) {
            Autoborna.processAjaxError(request, textStatus, errorThrown);
            spinner.remove();
        },
        complete: function () {
            Autoborna.removeLabelLoadingIndicator();
        }
    });
};

Autoborna.autoHideRedirectUrl = function(container) {
    var select = mQuery(container + ' select[name="page[redirectType]"]');
    var input = mQuery(container + ' input[name="page[redirectUrl]"]');

    //If value is none we autohide the "Redirect URL" field and empty it
    if (select.val() == '') {
        input.closest('.form-group').hide();
        input.val('');
    } else {
        input.closest('.form-group').show();
    }
};