Autoborna.sendHookTest = function() {

    var url = mQuery('#webhook_webhookUrl').val();
    var secret = mQuery('#webhook_secret').val();
    var eventTypes = mQuery("#event-types input[type='checkbox']");
    var selectedTypes = [];

    eventTypes.each(function() {
        var item = mQuery(this);
        if (item.is(':checked')) {
            selectedTypes.push(item.val());
        }
    });

    var data = {
        action: 'webhook:sendHookTest',
        url: url,
        secret: secret,
        types: selectedTypes
    };

    var spinner = mQuery('#spinner');

    // show the spinner
    spinner.removeClass('hide');

    mQuery.ajax({
        url: autobornaAjaxUrl,
        data: data,
        type: 'POST',
        dataType: "json",
        success: function(response) {
            if (response.success) {
                mQuery('#tester').html(response.html);
            }
        },
        error: function (request, textStatus, errorThrown) {
            Autoborna.processAjaxError(request, textStatus, errorThrown);
        },
        complete: function(response) {
            spinner.addClass('hide');
        }
    })
};