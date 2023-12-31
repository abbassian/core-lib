<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$item   = $event['extra']['log'];
$errors = false;
if (!empty($item['metadata']['errors'])) {
    $errors = (is_array($item['metadata']['errors'])) ? implode('<br />', $item['metadata']['errors']) : $item['metadata']['errors'];
} elseif (!empty($item['metadata']['failed'])) {
    $errors = (!empty($item['metadata']['reason'])) ? $item['metadata']['reason'] : 'autoborna.campaign.event.failed.timeline';
    $errors = $view['translator']->trans($errors);
} elseif (!empty($item['fail_reason'])) {
    $errors = $item['fail_reason'];
}

$cancelled = (empty($item['isScheduled']) && empty($item['dateTriggered']));
$dateSpan  = ($item['triggerDate']) ? '<span class="timeline-campaign-event-date-'.$item['event_id'].'" data-date="'.$item['triggerDate']->format('Y-m-d H:i:s').'">'.$view['date']->toFull($item['triggerDate']).'</span>' : '';

if ($cancelled) {
    // Note is scheduled
    $item['isScheduled'] = true;
}

?>
<div class="mt-10">
<?php if ($item['isScheduled']): ?>
    <p class="mt-0 mb-10 text-info" id="timeline-campaign-event-<?php echo $item['event_id']; ?>">
        <span id="timeline-campaign-event-text-<?php echo $item['event_id']; ?>"<?php if ($cancelled) {
    echo ' class="text-warning"';
} ?>>
            <i class="fa fa-clock-o"></i>
            <span class="timeline-campaign-event-scheduled-<?php echo $item['event_id']; ?><?php if ($cancelled) {
    echo ' hide';
} ?>">
                <?php echo $view['translator']->trans('autoborna.core.timeline.event.scheduled.time', ['%date%' => $dateSpan, '%event%' => $event['eventLabel']]); ?>
            </span>
            <span class="timeline-campaign-event-cancelled-<?php echo $item['event_id']; ?><?php if (!$cancelled) {
    echo ' hide';
} ?>">
                <?php echo $view['translator']->trans('autoborna.campaign.event.cancelled.time', ['%date%' => $dateSpan, '%event%' => $event['eventLabel']]); ?>
            </span>
        </span>
        <?php if ($lead && $view['security']->hasEntityAccess('lead:leads:editown', 'lead:leads:editother', $lead->getPermissionUser())): ?>
        <span class="form-buttons btn-group btn-group-xs mb-3" role="group" aria-label="Field options">
            <button type="button" id="timeline-campaign-event-save-<?php echo $item['event_id']; ?>" class="btn btn-default btn-nospin" onmousedown="return false;" onclick="Autoborna.saveScheduledCampaignEvent(<?php echo $item['event_id']; ?>, <?php echo $lead->getId(); ?>)" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.campaign.event.save'); ?>" style="display:none">
                <i class="fa fa-floppy-o text-primary"></i>
            </button>
            <button type="button" class="btn btn-default btn-nospin btn-reschedule" onclick="Autoborna.updateScheduledCampaignEvent(<?php echo $item['event_id']; ?>, <?php echo $lead->getId(); ?>)" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.campaign.event.reschedule'); ?>">
                <i class="fa fa-clock-o text-primary"></i>
            </button>
            <button type="button" class="btn btn-default btn-nospin"<?php if ($cancelled) {
    echo ' disabled';
} ?> onclick="Autoborna.cancelScheduledCampaignEvent(<?php echo $item['event_id']; ?>, <?php echo $lead->getId(); ?>)" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.campaign.event.cancel'); ?>">
                <i class="fa fa-times text-danger"></i>
            </button>
        </span>
        <?php endif; ?>
    </p>
<?php endif; ?>

<?php if ($errors): ?>
    <?php if ($item['isScheduled']): ?>
    <hr />
    <?php endif; ?>
    <p class="text-danger mt-0 mb-10"><i class="fa fa-warning"></i> <?php echo $view['translator']->trans('autoborna.campaign.event.last_error').': '.$errors; ?></p>
<?php endif; ?>

<?php if (!empty($item['metadata']['timeline']) || $item['campaign_description'] || $item['event_description']): ?>
    <hr />

    <?php if (!empty($item['metadata']['timeline'])): ?>
        <p class="mt-0 mb-10"><?php echo $item['metadata']['timeline']; ?></p>
    <?php endif; ?>

    <?php if ($item['campaign_description']): ?>
        <p class="mt-0 mb-10"><?php echo $view['translator']->trans('autoborna.campaign.campaign.description', ['%description%' => $item['campaign_description']]); ?></p>
    <?php endif; ?>
    <?php if ($item['event_description']): ?>
        <p class="mt-0 mb-10"><?php echo $view['translator']->trans('autoborna.campaign.campaign.description', ['%description%' => $item['event_description']]); ?></p>
    <?php endif; ?>
<?php endif; ?>
</div>
