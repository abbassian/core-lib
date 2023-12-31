<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna, Inc.
 *
 * @link        https://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$extra            = $event['extra'];
$log              = $extra['log'];
$eventType        = $log['type'];
$eventSettings    = $extra['campaignEventSettings'];
$messageSettings  = $eventSettings['action'][$eventType]['timelineTemplateVars']['messageSettings'];
$getChannelOutput = function ($channel) use ($view, $event, $log, $eventSettings) {
    $log['metadata'] = $log['metadata'][$channel];

    if (!empty($log['metadata']['dnc'])) {
        switch ($log['metadata']['dnc']) {
            case \Autoborna\LeadBundle\Entity\DoNotContact::BOUNCED:
                $msg = 'autoborna.lead.event.donotcontact_bounced';
                break;
            case \Autoborna\LeadBundle\Entity\DoNotContact::UNSUBSCRIBED:
                $msg = 'autoborna.lead.event.donotcontact_unsubscribed';
                break;
            case \Autoborna\LeadBundle\Entity\DoNotContact::MANUAL:
                $msg = 'autoborna.lead.event.donotcontact_manual';
                break;
        }

        return $view['translator']->trans($msg);
    }

    $template                     = 'AutobornaCampaignBundle:SubscribedEvents\Timeline:index.html.php';
    $channelEvent                 = $event;
    $channelEvent['extra']['log'] = $log;
    $vars                         = [
        'event' => $channelEvent,
    ];

    // Successful send through this channel
    if (!empty($messageSettings[$channel]['campaignAction'])) {
        $eventType = $messageSettings[$channel]['campaignAction'];
        if (!empty($eventSettings['action'][$eventType]['timelineTemplate'])) {
            $template = $eventSettings['action'][$eventType]['timelineTemplate'];
        }
        if (!empty($eventSettings['action'][$eventType]['timelineTemplateVars'])) {
            $vars['event']['extra'] = array_merge(
                $vars['event']['extra'],
                $eventSettings['action'][$eventType]['timelineTemplateVars']
            );
        }
    }

    return $view->render($template, $vars);
};
$counter = count($extra['log']['metadata']);
?>

<?php foreach ($extra['log']['metadata'] as $channel => $results): ?>
    <?php if (isset($messageSettings[$channel])): ?>
        <h4><?php echo $messageSettings[$channel]['label']; ?></h4>
        <?php echo $getChannelOutput($channel); ?>
        <?php --$counter; ?>
        <?php if ($counter > 0): ?>
            <hr/>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>