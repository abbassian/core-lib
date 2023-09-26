<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaUserBundle:Security:base.html.php');
$view['slots']->set('header', $view['translator']->trans('autoborna.api.oauth.header'));
?>

<form class="form-group login-form" name="login" data-toggle="ajax" role="form" action="<?php echo $view['router']->path($route); ?>" method="post">
    <div class="input-group mb-md">

        <span class="input-group-addon"><i class="fa fa-user"></i></span>
        <label for="username" class="sr-only"><?php echo $view['translator']->trans('autoborna.user.auth.form.loginusername'); ?></label>
        <input type="text" id="username" name="_username"
               class="form-control input-lg" value="<?php echo $view->escape($last_username); ?>" required autofocus
               placeholder="<?php echo $view['translator']->trans('autoborna.user.auth.form.loginusername'); ?>" />
    </div>
    <div class="input-group mb-md">
        <span class="input-group-addon"><i class="fa fa-key"></i></span>
        <label for="password" class="sr-only"><?php echo $view['translator']->trans('autoborna.core.password'); ?>:</label>
        <input type="password" id="password" name="_password"
               class="form-control input-lg" required
               placeholder="<?php echo $view['translator']->trans('autoborna.core.password'); ?>" />
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo $view['translator']->trans('autoborna.user.auth.form.loginbtn'); ?></button>
</form>
