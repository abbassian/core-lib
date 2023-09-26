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
$view['slots']->set('autobornaContent', 'calendar');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.calendar.menu.index'));
?>

<div class="panel panel-default mnb-5 bdr-t-wdh-0">
	<div class="panel-body">
		<div id="calendar"></div>
	</div>
</div>

<?php echo $view->render('AutobornaCoreBundle:Helper:modal.html.php', [
    'id'            => 'CalendarEditModal',
    'footerButtons' => true,
]);
