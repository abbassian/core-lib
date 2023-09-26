<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!isset($alertType)) {
    $alertType = 'growl';
}

?>
<div id="flashes"<?php echo ('growl' == $alertType) ? ' class="alert-growl-container"' : ''; ?>>
    <?php echo $view->render('AutobornaCoreBundle:Notification:flash_messages.html.php', [
        'dismissible' => (empty($notdismissible)) ? ' alert-dismissible' : '',
        'alertType'   => $alertType,
    ]); ?>
</div>