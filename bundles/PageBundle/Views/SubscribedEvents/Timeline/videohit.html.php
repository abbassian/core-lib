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

$viewTime = $duration = $percentage = $unknown = $view['translator']->trans('autoborna.core.unknown');

if ($event['extra']['hit']['time_watched']) {
    $viewTimeActual = $viewTime = $event['extra']['hit']['time_watched'];

    // format the time
    if ($viewTime > 60) {
        $sec      = $viewTime % 60;
        $min      = floor($viewTime / 60);
        $viewTime = $min.'m '.$sec.'s';
    } else {
        $viewTime .= 's';
    }
}

if ($event['extra']['hit']['duration']) {
    $durationActual = $duration = $event['extra']['hit']['duration'];

    // format the time
    if ($duration > 60) {
        $sec      = $duration % 60;
        $min      = floor($duration / 60);
        $duration = $min.'m '.$sec.'s';
    } else {
        $duration .= 's';
    }
}

if ($viewTime !== $unknown && $duration !== $unknown) {
    $percentage = round(($viewTimeActual / $durationActual) * 100);
}

$icon = (isset($event['icon'])) ? $event['icon'] : '';

?>
<dl class="dl-horizontal">
    <dt><?php echo $view['translator']->trans('autoborna.page.time.on.video'); ?>:</dt>
    <dd class="ellipsis"><?php echo $view['translator']->trans('autoborna.page.time.on.video.value', ['%time_watched%' => $viewTime, '%duration%' => $duration, '%percentage%' => $percentage]); ?></dd>
    <dt><?php echo $view['translator']->trans('autoborna.page.referrer'); ?>:</dt>
    <dd class="ellipsis"><?php echo $event['extra']['hit']['referer'] ? $view['assets']->makeLinks($event['extra']['hit']['referer']) : $view['translator']->trans('autoborna.core.unknown'); ?></dd>
    <dt><?php echo $view['translator']->trans('autoborna.video.url'); ?>:</dt>
    <dd class="ellipsis"><?php echo $event['extra']['hit']['url'] ? $view['assets']->makeLinks($event['extra']['hit']['url']) : $view['translator']->trans('autoborna.core.unknown'); ?></dd>
</dl>
<div class="small">
    <?php echo InputHelper::clean($event['extra']['hit']['user_agent']); ?>
</div>
