Autoborna.overflowNavOptions = {
    "parent": ".nav-overflow-tabs",
    "more": Autoborna.translate('autoborna.core.tabs.more')
};

/**
 * Toggle a tab based on published status
 *
 * @param el
 */
Autoborna.toggleTabPublished = function(el) {
    if (mQuery(el).val() === "1" && mQuery(el).prop('checked')) {
        Autoborna.publishTab(el);
    } else {
        Autoborna.unpublishTab(el);
    }
}

/**
 * Publish a tab
 *
 * @param tab
 */
Autoborna.publishTab = function(tab) {
    mQuery('a[href="#'+Autoborna.getTabId(tab)+'"]').find('.fa').removeClass('text-muted').addClass('text-success');
};

/**
 * Unpublish a tab
 *
 * @param tab
 */
Autoborna.unpublishTab = function(tab) {
    mQuery('a[href="#'+Autoborna.getTabId(tab)+'"]').find('.fa').removeClass('text-success').addClass('text-muted');
};

/**
 * Get the tab ID from the given element
 *
 * @param tab
 * @returns {*}
 */
Autoborna.getTabId = function(tab) {
    if (!mQuery(tab).hasClass('tab-pane')) {
        tab = mQuery(tab).closest('.tab-pane');
    }

    return mQuery(tab).attr('id');
};

/**
 *
 * @param tabs
 * @param options
 */
Autoborna.activateOverflowTabs = function(tabs, options) {
    if (!options) {
        options = {};
    }

    var localOptions = Autoborna.overflowNavOptions;

    mQuery.extend(localOptions, options);
    mQuery(tabs).overflowNavs(localOptions);

    var resizeMe = function(tabs, options) {
        mQuery(window).on('resize', {tabs: tabs, options: options},
            function (event) {
                mQuery(event.data.tabs).overflowNavs(event.data.options);
            }
        );
    };

    resizeMe(tabs, localOptions);
};

/**
 * Activate sortable tabs
 * @param tabs
 */
Autoborna.activateSortableTabs = function(tabs) {
    mQuery(tabs).sortable(
        {
            container: 'ul.nav',
            axis: mQuery(tabs).hasClass('tabs-right') || mQuery(tabs).hasClass('tabs-left') ? 'y' : 'x',
            stop: function (e, ui) {
                var action = mQuery(tabs).attr('data-sort-action');
                mQuery.ajax({
                    type: "POST",
                    url: action,
                    data: mQuery(tabs).sortable("serialize", {attribute: 'data-tab-id'})
                });
            }
        }
    );
};

/**
 * Activate hover delete buttons
 *
 * @param container
 */
Autoborna.activateTabDeleteButtons = function(container) {
    mQuery(container + " .nav.nav-deletable>li a").each(
        function() {
            Autoborna.activateTabDeleteButton(this);
        }
    );
};

/**
 * Activate hover and click for tab deletes
 *
 * @param tab
 */
Autoborna.activateTabDeleteButton = function(tab) {
    var btn = mQuery('<span class="btn btn-danger btn-xs btn-delete pull-right hide"><i class="fa fa-times"></i></span>')
        .on('click',
            function() {
                return Autoborna.deleteTab(btn)
            }
        ).appendTo(tab);

    mQuery(tab).hover(
        function() {
            mQuery(btn).removeClass('hide');
        },
        function () {
            mQuery(btn).addClass('hide');
        }
    );
};

/**
 * Delete a tab
 *
 * @param tab
 */
Autoborna.deleteTab = function(deleteBtn) {
    var tab = mQuery(deleteBtn).closest('li');
    var tabContent = mQuery(deleteBtn).closest('a').attr('href');

    var parent = mQuery(tab).closest('ul');
    var wasActive = (mQuery(tab.hasClass('active')));

    var action = mQuery(parent).attr('data-delete-action');
    if (action) {
        var success = false;
        mQuery.ajax({
            url: action,
            type: 'POST',
            dataType: "json",
            data: {tab: tabContent},
            success: function (response) {
                if (response && response.success) {
                    mQuery(tab).remove();
                    mQuery(tabContent).remove();

                    if (wasActive) {
                        mQuery(parent).find('li:first a').click();
                    }

                    if (!mQuery(parent).find('li').length) {
                        mQuery('.tab-content .placeholder').removeClass('hide');
                    }
                } else {
                    Autoborna.stopIconSpinPostEvent();
                }
            }
        });
    } else {
        mQuery(tab).remove();
        mQuery(tabContent).remove();

        if (wasActive) {
            mQuery(parent).find('li:first a').click();
        }

        if (!mQuery(parent).find('li').length) {
            mQuery('.tab-content .placeholder').removeClass('hide');
        }
    }

    return false;
};