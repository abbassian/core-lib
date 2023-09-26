<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<div class="hide builder campaign-builder live">
    <div class="btns-builder">
        <button type="button" class="btn btn-primary btn-apply-builder" onclick="Autoborna.saveCampaignFromBuilder();">
            <?php echo $view['translator']->trans('autoborna.core.form.apply'); ?>
        </button>
        <button type="button" class="btn btn-primary btn-close-campaign-builder"
                onclick="Autoborna.closeCampaignBuilder();">
            <?php echo $view['translator']->trans('autoborna.core.close.builder'); ?>
        </button>
    </div>
    <div id="builder-errors" class="alert alert-danger" role="alert" style="display: none;">test</div>
    <div class="builder-content">
        <div id="CampaignCanvas">
            <div id="CampaignEvent_newsource<?php if (!empty($campaignSources)) {
    echo '_hide';
} ?>" class="text-center list-campaign-source list-campaign-leadsource">
                <div class="campaign-event-content">
                    <div>
                        <span class="campaign-event-name ellipsis">
                            <i class="mr-sm fa fa-users"></i> <?php echo $view['translator']->trans('autoborna.campaign.add_new_source'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php
            foreach ($campaignSources as $source):
                echo $view->render('AutobornaCampaignBundle:Source:index.html.php', $source);
            endforeach;

            foreach ($campaignEvents as $event):
                $settings = $eventSettings[$event['eventType']][$event['type']];
                $template = isset($settings['template']) ? $settings['template'] : 'AutobornaCampaignBundle:Event:generic.html.php';

                echo $view->render($template,
                    ['event' => $event, 'campaignId' => $campaignId]);
            endforeach;

            echo $view->render('AutobornaCampaignBundle:Campaign\Builder:index.html.php',
                [
                    'campaignSources' => $campaignSources,
                    'eventSettings'   => $eventSettings,
                    'campaignId'      => $campaignId,
                ]
            );
            ?>
        </div>
        <div id="EventJumpOverlay"></div>
    </div>
</div>
<!-- dropped coordinates -->
<input type="hidden" value="" id="droppedX"/>
<input type="hidden" value="" id="droppedY"/>
<input type="hidden" value="<?php echo $view->escape($campaignId); ?>" id="campaignId"/>

<?php echo $view->render(
    'AutobornaCoreBundle:Helper:modal.html.php',
    [
        'id'            => 'CampaignEventModal',
        'header'        => false,
        'footerButtons' => true,
        'dismissible'   => false,
    ]
);

?>
<script>
    /**
     * We typecast to object here so that an empty value will
     * be encoded to {} instead of []. Adding JSON_FORCE_OBJECT
     * is not an option because it does a deep transform to
     * object, whereas typecasting only does the first level.
     */
    Autoborna.campaignBuilderCanvasSettings =
    <?php echo json_encode((object) $canvasSettings, JSON_PRETTY_PRINT); ?>;
    Autoborna.campaignBuilderCanvasSources =
    <?php echo json_encode((object) $campaignSources, JSON_PRETTY_PRINT); ?>;
    Autoborna.campaignBuilderCanvasEvents =
    <?php echo json_encode((object) $campaignEvents, JSON_PRETTY_PRINT); ?>;

    Autoborna.campaignBuilderConnectionRestrictions =
    <?php echo json_encode((object) $eventSettings['connectionRestrictions'], JSON_PRETTY_PRINT); ?>;
</script>
