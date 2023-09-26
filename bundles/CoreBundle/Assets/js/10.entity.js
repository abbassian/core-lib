//live search vars
AutobornaVars.liveCache            = new Array();
AutobornaVars.lastSearchStr        = "";
AutobornaVars.globalLivecache      = new Array();
AutobornaVars.lastGlobalSearchStr  = "";

/**
 * Check if the the entity ID is temporary (for new entities)
 *
 * @param string idInputSelector
 */
Autoborna.isNewEntity = function(idInputSelector) {
    id = mQuery(idInputSelector);
    if (id.length) {
        return id.val().match("^new_");
    }
    return null;
};

/**
 * Get entity ID of pages that have an input with id of entityId
 *
 * @returns {*}
 */
Autoborna.getEntityId = function() {
    return (mQuery('input#entityId').length) ? mQuery('input#entityId').val() : 0;
};

/**
 * Reorder table data
 * @param name
 * @param orderby
 * @param tmpl
 * @param target
 */
Autoborna.reorderTableData = function (name, orderby, tmpl, target, baseUrl) {
    if (typeof baseUrl == 'undefined') {
        baseUrl = window.location.pathname;
    }

    if (baseUrl.indexOf('tmpl') == -1) {
        baseUrl = baseUrl + "?tmpl=" + tmpl
    }

    var route = baseUrl + "&name=" + name + "&orderby=" + encodeURIComponent(orderby);
    Autoborna.loadContent(route, '', 'POST', target);
};

/**
 *
 * @param name
 * @param filterby
 * @param filterValue
 * @param tmpl
 * @param target
 */
Autoborna.filterTableData = function (name, filterby, filterValue, tmpl, target, baseUrl) {
    if (typeof baseUrl == 'undefined') {
        baseUrl = window.location.pathname;
    }

    if (baseUrl.indexOf('tmpl') == -1) {
        baseUrl = baseUrl + "?tmpl=" + tmpl
    }

    var route = baseUrl + "&name=" + name + "&filterby=" + encodeURIComponent(filterby) + "&value=" + encodeURIComponent(filterValue)
    Autoborna.loadContent(route, '', 'POST', target);
};

/**
 *
 * @param name
 * @param limit
 * @param tmpl
 * @param target
 */
Autoborna.limitTableData = function (name, limit, tmpl, target, baseUrl) {
    if (typeof baseUrl == 'undefined') {
        baseUrl = window.location.pathname;
    }

    if (baseUrl.indexOf('tmpl') == -1) {
        baseUrl = baseUrl + "?tmpl=" + tmpl
    }

    var route = baseUrl + "&name=" + name + "&limit=" + limit;
    Autoborna.loadContent(route, '', 'POST', target);
};


/**
 * Filters list based on search contents
 */
Autoborna.filterList = function (e, elId, route, target, liveCacheVar, action, overlayEnabled, overlayTarget) {
    if (typeof liveCacheVar == 'undefined') {
        liveCacheVar = "liveCache";
    }

    var el = mQuery('#' + elId);
    //only submit if the element exists, its a livesearch, or on button click

    if (el.length && (e.data.livesearch || mQuery(e.target).prop('tagName') == 'BUTTON' || mQuery(e.target).parent().prop('tagName') == 'BUTTON')) {
        var value = el.val().trim();
        //should the content be cleared?
        if (!value) {
            //force action since we have no content
            action = 'clear';
        } else if (action == 'clear') {
            el.val('');
            el.typeahead('val', '');
            value = '';
        }

        //make the request
        //@TODO reevaluate search caching as it seems to cause issues
        if (false && value && value in AutobornaVars[liveCacheVar]) {
            var response = {"newContent": AutobornaVars[liveCacheVar][value]};
            response.target = target;
            response.overlayEnabled = overlayEnabled;
            response.overlayTarget = overlayTarget;

            Autoborna.processPageContent(response);
        } else {
            var searchName = el.attr('name');
            if (searchName == 'undefined') {
                searchName = 'search';
            }

            if (typeof Autoborna.liveSearchXhr !== 'undefined') {
                //ensure current search request is aborted
                Autoborna['liveSearchXhr'].abort();
            }

            var btn = "button[data-livesearch-parent='" + elId + "']";
            if (mQuery(btn).length && !mQuery(btn).hasClass('btn-nospin') && !Autoborna.filterButtonClicked) {
                Autoborna.startIconSpinOnEvent(btn);
            }

            var tmpl = mQuery('#' + elId).data('tmpl');
            if (!tmpl) {
                tmpl = 'list';
            }

            var tmplParam = (route.indexOf('tmpl') == -1) ? '&tmpl=' + tmpl : '';

            // In a modal?
            var checkInModalTarget = (overlayTarget) ? overlayTarget : target;
            var modalParent        = mQuery(checkInModalTarget).closest('.modal');
            var inModal            = mQuery(modalParent).length > 0;

            if (inModal) {
                var modalTarget = '#' + mQuery(modalParent).attr('id');
                Autoborna.startModalLoadingBar(modalTarget);
            }
            var showLoading = (inModal) ? false : true;

            Autoborna.liveSearchXhr = mQuery.ajax({
                showLoadingBar: showLoading,
                url: route,
                type: "GET",
                data: searchName + "=" + encodeURIComponent(value) + tmplParam,
                dataType: "json",
                success: function (response) {
                    //cache the response
                    if (response.newContent) {
                        AutobornaVars[liveCacheVar][value] = response.newContent;
                    }
                    //note the target to be updated
                    response.target = target;
                    response.overlayEnabled = overlayEnabled;
                    response.overlayTarget = overlayTarget;

                    //update the buttons class and action
                    if (mQuery(btn).length) {
                        if (action == 'clear') {
                            mQuery(btn).attr('data-livesearch-action', 'search');
                            mQuery(btn).children('i').first().removeClass('fa-eraser').addClass('fa-search');
                        } else {
                            mQuery(btn).attr('data-livesearch-action', 'clear');
                            mQuery(btn).children('i').first().removeClass('fa-search').addClass('fa-eraser');
                        }
                    }

                    if (inModal) {
                        Autoborna.processModalContent(response);
                        Autoborna.stopModalLoadingBar(modalTarget);
                    } else {
                        Autoborna.processPageContent(response);
                        Autoborna.stopPageLoadingBar();
                    }
                },
                error: function (request, textStatus, errorThrown) {
                    Autoborna.processAjaxError(request, textStatus, errorThrown);

                    //update the buttons class and action
                    if (mQuery(btn).length) {
                        if (action == 'clear') {
                            mQuery(btn).attr('data-livesearch-action', 'search');
                            mQuery(btn).children('i').first().removeClass('fa-eraser').addClass('fa-search');
                        } else {
                            mQuery(btn).attr('data-livesearch-action', 'clear');
                            mQuery(btn).children('i').first().removeClass('fa-search').addClass('fa-eraser');
                        }
                    }
                },
                complete: function() {
                    delete Autoborna.liveSearchXhr;
                    delete Autoborna.filterButtonClicked;
                }
            });
        }
    }
};

/**
 * Apply filter
 * @param list
 */
Autoborna.setSearchFilter = function (el, searchId, string) {
    if (typeof searchId == 'undefined')
        searchId = '#list-search';
    else
        searchId = '#' + searchId;

    if (string || string === '') {
        var current = string;
    } else {
        var filter  = mQuery(el).val();
        var current = mQuery('#list-search').typeahead('val') + " " + filter;
    }

    //append the filter
    mQuery(searchId).typeahead('val', current);

    //submit search
    var e = mQuery.Event("keypress", {which: 13});
    e.data = {};
    e.data.livesearch = true;
    Autoborna.filterList(
        e,
        'list-search',
        mQuery(searchId).attr('data-action'),
        mQuery(searchId).attr('data-target'),
        'liveCache'
    );
};

/**
 * Unlock an entity
 *
 * @param model
 * @param id
 */
Autoborna.unlockEntity = function (model, id, parameter) {
    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: "POST",
        data: "action=unlockEntity&model=" + model + "&id=" + id + "&parameter=" + parameter,
        dataType: "json"
    });
};

/**
 * Toggles published status of an entity
 *
 * @param el
 * @param model
 * @param id
 */
Autoborna.togglePublishStatus = function (event, el, model, id, extra, backdrop) {
    event.preventDefault();

    var wasPublished = mQuery(el).hasClass('fa-toggle-on');
    var element = mQuery(el);

    element.removeClass('fa-toggle-on fa-toggle-off').addClass('fa-spin fa-spinner');

    //destroy tooltips so it can be regenerated
    element.tooltip('destroy');
    //clear the lookup cache
    AutobornaVars.liveCache = new Array();

    if (backdrop) {
        Autoborna.activateBackdrop();
    }

    if (extra) {
        extra = '&' + extra;
    }
    element.tooltip('destroy');
    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: "POST",
        data: "action=togglePublishStatus&model=" + model + '&id=' + id + extra,
        dataType: "json",
        success: function (response) {
            if (response.reload) {
                Autoborna.redirectWithBackdrop(window.location);
            } else if (response.statusHtml) {
                element.replaceWith(response.statusHtml);
                element.tooltip({html: true, container: 'body'});
            }
        },
        error: function (request, textStatus, errorThrown) {
            var addClass = (wasPublished) ? 'fa-toggle-on' : 'fa-toggle-off';
            element.removeClass('fa-spin fa-spinner').addClass(addClass);

            Autoborna.processAjaxError(request, textStatus, errorThrown);
        }
    });
};

/**
 * Executes a batch action
 *
 * @param action
 */
Autoborna.executeBatchAction = function (action, el) {
    if (typeof Autoborna.activeActions == 'undefined') {
        Autoborna.activeActions = {};
    } else if (typeof Autoborna.activeActions[action] != 'undefined') {
        // Action is currently being executed
        return;
    }

    var items = Autoborna.getCheckedListIds(el, true);

    var queryGlue = action.indexOf('?') >= 0 ? '&' : '?';

    // Append the items to the action to send with the POST
    var action = action + queryGlue + 'ids=' + items;

    // Hand over processing to the executeAction method
    Autoborna.executeAction(action);
};

/**
 * Checks that items are checked before showing confirmation
 *
 * @param container
 * @returns int
 */
Autoborna.batchActionPrecheck = function(container) {
    if (typeof container == 'undefined') {
        container = '';
    }

    return mQuery(container + ' input[class=list-checkbox]:checked').length;
};

/**
 * Retrieves the IDs of the items checked in a list
 *
 * @param el
 * @param stringify
 * @returns {*}
 */
Autoborna.getCheckedListIds = function(el, stringify) {
    var checkboxes = 'input[class=list-checkbox]:checked';

    // Check for a target
    if (typeof el != 'undefined' && el) {
        var target = mQuery(el).data('target');
        if (target) {
            checkboxes = target + ' ' + checkboxes;
        }
    }

    // Retrieve all of the selected items
    var items = mQuery(checkboxes).map(function () {
        return mQuery(this).val();
    }).get();

    if (stringify) {
        items = JSON.stringify(items);
    }

    return items;
};
