<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$data = $event['extra']['log']['metadata'];

if (isset($data['failed']) || !isset($data['timeline'])) {
    return;
}
?>

<dl class="dl-horizontal">
    <dt><?php echo $view['translator']->trans('autoborna.dynamicContent.timeline.content'); ?></dt>
    <dd><?php echo $view['translator']->trans($data['timeline']); ?></dd>
</dl>
