//CalendarBundle
Autoborna.calendarOnLoad = function (container) {
    Autoborna.loadCalendarEvents(container);
};

Autoborna.calendarModalOnLoad = function (container, response) {
    mQuery('#calendar').fullCalendar( 'refetchEvents' );
    mQuery(container + " a[data-toggle='ajax']").off('click.ajax');
    mQuery(container + " a[data-toggle='ajax']").on('click.ajax', function (event) {
        event.preventDefault();
        mQuery('.modal').modal('hide');
        return Autoborna.ajaxifyLink(this, event);
    });
};

Autoborna.initializeCalendarModals = function (container) {
    mQuery(container + " *[data-toggle='ajaxmodal']").off('click.ajaxmodal');
    mQuery(container + " *[data-toggle='ajaxmodal']").on('click.ajaxmodal', function (event) {
        event.preventDefault();
        Autoborna.ajaxifyModal(this, event);
    });
}

Autoborna.loadCalendarEvents = function (container) {
    mQuery('#calendar').fullCalendar({
        events: autobornaAjaxUrl + "?action=calendar:generateData",
        lang: autobornaLocale,
        eventLimit: true,
        eventLimitText: "more",
        eventRender: function(event, element) {
            element = mQuery(element);
            if (event.iconClass) {
                element.find('.fc-title').before(mQuery('<i />').addClass(event.iconClass));
            }
            if (event.attr) {
                element.attr(event.attr);
            }
            if (event.description) {
                var checkDay = new Date(event.start._d);
                if (checkDay.getDay() == 0) {
                    element.tooltip({'title': event.description, placement: 'right'});
                } else {
                    element.tooltip({'title': event.description, placement: 'left'});
                }
            }
        },
        loading: function(bool) {
            // if calendar events are loaded
            if (!bool) {
                //initialize ajax'd modals
                Autoborna.initializeCalendarModals(container);
            }
        },
        eventDrop: function(event, delta, revertFunc) {
            mQuery.ajax({
                url: autobornaAjaxUrl + "?action=calendar:updateEvent",
                data: 'entityId=' + event.entityId + '&entityType=' + event.entityType + '&setter=' + event.setter + '&startDate=' + event.start.format(),
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (!response.success) {
                        revertFunc();
                    }
                    Autoborna.initializeCalendarModals(container);
                    if (response.flashes) {
                        Autoborna.setFlashes(response.flashes);
                        Autoborna.hideFlashes();
                    }
                },
                error: function (response, textStatus, errorThrown) {
                    revertFunc();
                    Autoborna.processAjaxError(response, textStatus, errorThrown, true);
                }
            });
        }
    });
}
