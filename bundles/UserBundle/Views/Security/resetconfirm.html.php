<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!$app->getRequest()->isXmlHttpRequest()) {
    $view->extend('AutobornaUserBundle:Security:base.html.php');
} else {
    $view->extend('AutobornaUserBundle:Security:ajax.html.php');
}
?>

<div class="alert alert-warning"><?php echo $view['translator']->trans('autoborna.user.user.passwordresetconfirm.info'); ?></div>
<?php
echo $view['form']->start($form);
echo $view['form']->row($form['identifier']);
echo $view['form']->row($form['plainPassword']['password']);
echo $view['form']->row($form['plainPassword']['confirm']);
echo $view['form']->widget($form['submit']);
echo $view['form']->end($form);
?>

<div class="mt-sm">
    <a href="<?php echo $view['router']->path('login'); ?>"><?php echo $view['translator']->trans('autoborna.user.user.passwordreset.back'); ?></a>
</div>
