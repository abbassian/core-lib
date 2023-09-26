<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<!DOCTYPE html>
<html>
    <?php echo $view->render('AutobornaCoreBundle:Default:head.html.php'); ?>
    <body>
        <?php $view['assets']->outputScripts('bodyOpen'); ?>
        <section id="app-content" class="container content-only">
            <?php echo $view->render('AutobornaCoreBundle:Notification:flashes.html.php', ['alertType' => 'standard']); ?>
            <?php $view['slots']->output('_content'); ?>
        </section>
        <?php echo $view->render('AutobornaCoreBundle:Helper:modal.html.php', [
            'id'            => 'AutobornaSharedModal',
            'footerButtons' => true,
        ]); ?>
        <?php $view['assets']->outputScripts('bodyClose'); ?>
        <script>
            Autoborna.onPageLoad('body');
        </script>
    </body>
</html>
