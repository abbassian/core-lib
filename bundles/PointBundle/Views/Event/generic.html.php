<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$containerClass = (!empty($deleted)) ? ' bg-danger' : '';
?>

<div class="trigger-event-row <?php echo $containerClass; ?>" id="triggerEvent_<?php echo $id; ?>">
    <?php echo $view->render('AutobornaPointBundle:Event:actions.html.php', [
        'deleted'   => (!empty($deleted)) ? $deleted : false,
        'id'        => $id,
        'route'     => 'autoborna_pointtriggerevent_action',
        'sessionId' => $sessionId,
    ]); ?>
    <span class="trigger-event-label"><?php echo $event['name']; ?></span>
    <?php if (!empty($event['description'])): ?>
    <span class="trigger-event-descr"><?php echo $event['description']; ?></span>
    <?php endif; ?>
</div>