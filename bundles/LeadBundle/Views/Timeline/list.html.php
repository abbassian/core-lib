<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (isset($tmpl) && 'index' == $tmpl) {
    $view->extend('AutobornaLeadBundle:Timeline:index.html.php');
}

$baseUrl = $view['router']->path(
    'autoborna_contacttimeline_action',
    [
        'leadId' => $lead->getId(),
    ]
);
?>

<!-- timeline -->
<div class="table-responsive">
    <table class="table table-hover table-bordered" id="contact-timeline">
        <thead>
        <tr>
            <th class="timeline-icon">
                <a class="btn btn-sm btn-nospin btn-default" data-activate-details="all" data-toggle="tooltip" title="<?php echo $view['translator']->trans(
                    'autoborna.lead.timeline.toggle_all_details'
                ); ?>">
                    <span class="fa fa-fw fa-level-down"></span>
                </a>
            </th>
            <?php
            echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                'orderBy'    => 'eventLabel',
                'text'       => 'autoborna.lead.timeline.event_name',
                'class'      => 'timeline-name',
                'sessionVar' => 'lead.'.$lead->getId().'.timeline',
                'baseUrl'    => $baseUrl,
                'target'     => '#timeline-table',
            ]);

            echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                'orderBy'    => 'eventType',
                'text'       => 'autoborna.lead.timeline.event_type',
                'class'      => 'visible-md visible-lg timeline-type',
                'sessionVar' => 'lead.'.$lead->getId().'.timeline',
                'baseUrl'    => $baseUrl,
                'target'     => '#timeline-table',
            ]);

            echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                'orderBy'    => 'timestamp',
                'text'       => 'autoborna.lead.timeline.event_timestamp',
                'class'      => 'visible-md visible-lg timeline-timestamp',
                'sessionVar' => 'lead.'.$lead->getId().'.timeline',
                'baseUrl'    => $baseUrl,
                'target'     => '#timeline-table',
            ]);
            ?>
        </tr>
        <tbody>
        <?php foreach ($events['events'] as $counter => $event): ?>
            <?php
            ++$counter; // prevent 0
            $icon       = (isset($event['icon'])) ? $event['icon'] : 'fa-history';
            $eventLabel = (isset($event['eventLabel'])) ? $event['eventLabel'] : $event['eventType'];
            if (is_array($eventLabel)):
                $linkType   = empty($eventLabel['isExternal']) ? 'data-toggle="ajax"' : 'target="_new"';
                $eventLabel = "<a href=\"{$eventLabel['href']}\" $linkType>{$eventLabel['label']}</a>";
            endif;

            $details = '';
            if (isset($event['contentTemplate']) && $view->exists($event['contentTemplate'])):
                $details = trim($view->render($event['contentTemplate'], ['event' => $event, 'lead' => $lead]));
            endif;

            $rowStripe = (0 === $counter % 2) ? ' timeline-row-highlighted' : '';
            ?>
            <tr class="timeline-row<?php echo $rowStripe; ?><?php if (!empty($event['featured'])) {
                echo ' timeline-featured';
            } ?>">
                <td class="timeline-icon">
                    <a href="javascript:void(0);" data-activate-details="<?php echo $counter; ?>" class="btn btn-sm btn-nospin btn-default<?php if (empty($details)) {
                echo ' disabled';
            } ?>" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.lead.timeline.toggle_details'); ?>">
                        <span class="fa fa-fw <?php echo $icon; ?>"></span>
                    </a>
                </td>
                <td class="timeline-name"><?php echo $eventLabel; ?></td>
                <td class="timeline-type"><?php if (isset($event['eventType'])) {
                echo $event['eventType'];
            } ?></td>
                <td class="timeline-timestamp"><?php echo $view['date']->toText($event['timestamp'], 'local', 'Y-m-d H:i:s', true); ?></td>
            </tr>
            <?php if (!empty($details)): ?>
                <tr class="timeline-row<?php echo $rowStripe; ?> timeline-details hide" id="timeline-details-<?php echo $counter; ?>">
                    <td colspan="4">
                        <?php echo $details; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php echo $view->render(
    'AutobornaCoreBundle:Helper:pagination.html.php',
    [
        'page'       => $events['page'],
        'fixedPages' => $events['maxPages'],
        'fixedLimit' => true,
        'baseUrl'    => $baseUrl,
        'target'     => '#timeline-table',
        'totalItems' => $events['total'],
    ]
); ?>

<!--/ timeline -->