<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Autoborna\CoreBundle\Helper\InputHelper;

$timeOnPage = $view['translator']->trans('autoborna.core.unknown');
if ($event['extra']['hit']['dateLeft']) {
    $timeOnPage = ($event['extra']['hit']['dateLeft']->getTimestamp() - $event['extra']['hit']['dateHit']->getTimestamp());

    // format the time
    if ($timeOnPage > 60) {
        $sec        = $timeOnPage % 60;
        $min        = floor($timeOnPage / 60);
        $timeOnPage = $min.'m '.$sec.'s';
    } else {
        $timeOnPage .= 's';
    }
}

$query = $event['extra']['hit']['query'];
?>

<?php if (isset($event['extra'])) : ?>
<dl class="dl-horizontal">
    <dt><?php echo $view['translator']->trans('autoborna.page.time.on.page'); ?>:</dt>
    <dd><?php echo $timeOnPage; ?></dd>
    <dt><?php echo $view['translator']->trans('autoborna.page.referrer'); ?>:</dt>
    <dd><?php echo $event['extra']['hit']['referer'] ? $view['assets']->makeLinks($event['extra']['hit']['referer']) : $view['translator']->trans('autoborna.core.unknown'); ?></dd>
    <dt><?php echo $view['translator']->trans('autoborna.page.url'); ?>:</dt>
    <dd><?php echo $event['extra']['hit']['url'] ? $view['assets']->makeLinks($event['extra']['hit']['url']) : $view['translator']->trans('autoborna.core.unknown'); ?></dd>

    <?php if (isset($event['extra']['hit']['device']) and !empty($event['extra']['hit']['device'])): ?>
        <dt><?php echo $view['translator']->trans('autoborna.core.timeline.device.name'); ?></dt>
        <dd class="ellipsis">
            <?php echo InputHelper::clean($event['extra']['hit']['device']); ?>
        </dd>
    <?php endif; ?>

    <?php if (!empty($event['extra']['hit']['deviceOsName'])): ?>
        <dt><?php echo $view['translator']->trans('autoborna.core.timeline.device.os'); ?></dt>
        <dd class="ellipsis">
            <?php echo InputHelper::clean($event['extra']['hit']['deviceOsName']); ?>
        </dd>
    <?php endif; ?>

    <?php if (!empty($event['extra']['hit']['deviceBrand'])): ?>
        <dt><?php echo $view['translator']->trans('autoborna.core.timeline.device.brand'); ?></dt>
        <dd class="ellipsis">
            <?php echo InputHelper::clean($event['extra']['hit']['deviceBrand']); ?>
        </dd>
    <?php endif; ?>

    <?php if (!empty($event['extra']['hit']['deviceModel'])): ?>
        <dt><?php echo $view['translator']->trans('autoborna.core.timeline.device.model'); ?></dt>
        <dd class="ellipsis">
            <?php echo InputHelper::clean($event['extra']['hit']['deviceModel']); ?>
        </dd>
    <?php endif; ?>

    <?php if (isset($event['extra']['hit']['sourceName'])): ?>

        <dt><?php echo $view['translator']->trans('autoborna.core.source'); ?>:</dt>
        <dd>
            <?php if (isset($event['extra']['hit']['sourceRoute'])): ?>
            <a href="<?php echo InputHelper::clean($event['extra']['hit']['sourceRoute']); ?>" data-toggle="ajax">
                <?php echo InputHelper::clean($event['extra']['hit']['sourceName']); ?>
            </a>
            <?php else: ?>
            <?php echo InputHelper::clean($event['extra']['hit']['sourceName']); ?>
            <?php endif; ?>
        </dd>

        <?php if (!empty($event['extra']['hit']['clientInfo']) && is_array($event['extra']['hit']['clientInfo'])): ?>
            <dt><?php echo $view['translator']->trans('autoborna.core.timeline.device.client.info'); ?></dt>
            <dd class="ellipsis">
                <?php foreach ($event['extra']['hit']['clientInfo'] as $clientInfo) : ?>
                    <?php echo InputHelper::clean($clientInfo); ?>
                <?php endforeach; ?>
            </dd>
        <?php endif; ?>

    <?php endif; ?>

    <?php
    if (!empty($query)) {
        $counter = 0;
        foreach ($query as $k => $v) {
            if (in_array($v, ['', null, []])) {
                continue;
            }
            if (in_array($k, ['ct', 'page_title', 'page_referrer', 'page_url'])) {
                continue;
            }
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    ++$counter;
                    $k2 = ucwords(str_replace('_', ' ', $k));

                    echo '<dt>'.$k2.':</dt>';
                    echo '<dd class="ellipsis">'.$v2.'</dd>';

                    if (empty($showMore) && $counter > 5) {
                        $showMore = true;

                        echo '<div style="display:none">';
                    }
                }

                continue;
            }

            ++$counter;
            $k = ucwords(str_replace('_', ' ', $k));

            echo '<dt>'.$k.':</dt>';
            echo '<dd class="ellipsis">'.$v.'</dd>';

            if (empty($showMore) && $counter > 5) {
                $showMore = true;

                echo '<div style="display:none">';
            }
        }

        if (!empty($showMore)) {
            echo '</div>';
            echo '<a href="javascript:void(0);" class="text-center small center-block mt-xs" onclick="Autoborna.toggleTimelineMoreVisiblity(mQuery(this).prev());">';
            echo $view['translator']->trans('autoborna.core.more.show');
            echo '</a>';
        }
    }
    ?>
</dl>
<div class="small">
    <?php echo InputHelper::clean($event['extra']['hit']['userAgent']); ?>
</div>
<?php endif; ?>
