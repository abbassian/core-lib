<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaCoreBundle:Default:content.html.php');
$view['slots']->set('autobornaContent', 'pointTrigger');
$view['slots']->set('headerTitle', $entity->getName());

$view['slots']->set('actions', $view->render('AutobornaCoreBundle:Helper:page_actions.html.php', [
    'item'            => $entity,
    'templateButtons' => [
        'edit'   => $permissions['point:triggers:edit'],
        'delete' => $permissions['point:triggers:delete'],
    ],
    'routeBase' => 'pointtrigger',
    'langVar'   => 'point.trigger',
]));
?>

<div class="scrollable trigger-details">
    <?php //@todo - output trigger details/actions?>
</div>