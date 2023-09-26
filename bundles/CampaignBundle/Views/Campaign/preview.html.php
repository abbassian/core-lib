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
<div class="builder campaign-builder preview">
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
            /** @var \Autoborna\CampaignBundle\Entity\Campaign $campaign */
            if (count($campaign->getForms())) {
                $sources = [
                    'sourceType' => 'forms',
                    'campaignId' => $campaignId,
                    'names'      => implode(
                        ', ',
                        array_map(
                            function (Autoborna\FormBundle\Entity\Form $f) {
                                return $f->getName();
                            },
                            $campaign->getForms()
                                     ->toArray()
                        )
                    ),
                ];
                echo $view->render('AutobornaCampaignBundle:Source:index.html.php', $sources);
            }

            if (count($campaign->getLists())) {
                $sources = [
                    'sourceType' => 'lists',
                    'campaignId' => $campaignId,
                    'names'      => implode(
                        ', ',
                        array_map(
                            function (Autoborna\LeadBundle\Entity\LeadList $f) {
                                return $f->getName();
                            },
                            $campaign->getLists()
                                     ->toArray()
                        )
                    ),
                ];
                echo $view->render('AutobornaCampaignBundle:Source:index.html.php', $sources);
            }

            foreach ($campaignEvents as $event):
                echo $view->render('AutobornaCampaignBundle:Event:preview.html.php', ['event' => $event, 'campaignId' => $campaignId]);
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
    Autoborna.campaignBuilderCanvasSettings =
        <?php echo json_encode((object) $canvasSettings, JSON_PRETTY_PRINT); ?>;
    Autoborna.campaignBuilderCanvasSources =
        <?php echo json_encode((object) $campaignSources, JSON_PRETTY_PRINT); ?>;
    Autoborna.campaignBuilderCanvasEvents =
        <?php echo json_encode((object) $campaignEvents, JSON_PRETTY_PRINT); ?>;

    Autoborna.campaignBuilderConnectionRestrictions =
        <?php echo json_encode((object) $eventSettings['connectionRestrictions'], JSON_PRETTY_PRINT); ?>;
</script>
