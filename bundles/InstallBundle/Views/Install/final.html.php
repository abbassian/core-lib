<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('AutobornaInstallBundle:Install:content.html.php');
}
?>

<div class="panel-heading">
    <h2 class="panel-title">
        <?php echo $view['translator']->trans('autoborna.install.heading.final'); ?>
    </h2>
</div>
<div class="panel-body text-center">
    <div><i class="fa fa-check fa-5x mb-20 text-success"></i></div>
    <h4 class="mb-3"><?php echo $view['translator']->trans('autoborna.install.heading.finished'); ?></h4>
    <h5><?php echo $view['translator']->trans('autoborna.install.heading.configured'); ?></h5>
    <?php if ($welcome_url) : ?>
        <a href="<?php echo $welcome_url; ?>" role="button" class="btn btn-primary mt-20">
            <?php echo $view['translator']->trans('autoborna.install.sentence.proceed.to.autoborna'); ?>
        </a>
    <?php endif; ?>
</div>
