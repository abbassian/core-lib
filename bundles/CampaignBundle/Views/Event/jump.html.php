<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (empty($route)) {
    $route = 'autoborna_campaignevent_action';
}

?>
<?php if (empty($update)): ?>
<div id="CampaignEvent_<?php echo $event['id']; ?>" data-type="<?php echo $event['eventType']; ?>" class="draggable list-campaign-event list-campaign-<?php echo $event['eventType']; ?>" data-event="<?php echo $event['type']; ?>" data-event-id="<?php echo $event['id']; ?>">
<?php endif; ?>
    <div class="campaign-event-content">
        <div><span class="campaign-event-name ellipsis"><?php if ('dev' === MAUTIC_ENV):?><small><?php echo $event['id']; ?></small> <?php endif; ?><?php echo $event['name']; ?></span></div>
        <a class="btn btn-primary btn-nospin btn-xs highlight-jump-target" href="javascript:void(0)" onClick="Autoborna.highlightJumpTarget(event, this)" data-jump-target="<?php echo $event['properties']['jumpToEvent']; ?>">
            <i class="fa fa-refresh" style="margin-top:4px;"></i>
        </a>
    </div>
<?php if (empty($update)): ?>
    <div class="campaign-event-buttons hide">
        <a data-toggle="ajaxmodal" data-prevent-dismiss="true" data-target="#CampaignEventModal" href="<?php echo $view['router']->path($route, ['objectAction' => 'edit', 'objectId' => $event['id'], 'campaignId' => $campaignId]); ?>" class="btn btn-primary btn-xs btn-edit">
            <i class="fa fa-pencil"></i>
        </a>
        <a data-toggle="ajax" data-target="CampaignEvent_<?php echo $event['id']; ?>" data-ignore-formexit="true" data-method="POST" data-hide-loadingbar="true" href="<?php echo $view['router']->path($route, ['objectAction' => 'delete', 'objectId' => $event['id'], 'campaignId' => $campaignId]); ?>"  class="btn btn-delete btn-danger btn-xs">
            <i class="fa fa-times"></i>
        </a>
    </div>
</div>
<?php endif; ?>
