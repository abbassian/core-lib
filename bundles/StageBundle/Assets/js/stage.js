Autoborna.getStageActionPropertiesForm = function(actionType) {
    Autoborna.activateLabelLoadingIndicator('stage_type');

    var query = "action=stage:getActionForm&actionType=" + actionType;
    mQuery.ajax({
        url: autobornaAjaxUrl,
        type: "POST",
        data: query,
        dataType: "json",
        success: function (response) {
            if (typeof response.html != 'undefined') {
                mQuery('#stageActionProperties').html(response.html);
                Autoborna.onPageLoad('#stageActionProperties', response);
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