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
if (isset($data['failed'])) {
    return;
}
?>

<dl class="dl-horizontal">
    <dt><?php echo $view['translator']->trans('autoborna.notification.timeline.status'); ?></dt>
    <dd><?php echo $view['translator']->trans($data['status']); ?></dd>
    <dt><?php echo $view['translator']->trans('autoborna.notification.timeline.type'); ?></dt>
    <dd><?php echo $view['translator']->trans($data['type']); ?></dd>
</dl>
<div class="small">
    <hr />
    <strong><?php echo $data['heading']; ?></strong>
    <br />
    <?php echo $data['content']; ?>
</div>
