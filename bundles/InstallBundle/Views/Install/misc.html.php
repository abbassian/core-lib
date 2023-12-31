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
        <?php echo $view['translator']->trans('autoborna.install.heading.misc.configuration'); ?>
    </h2>
</div>
<div class="panel-body">
    <?php echo $view['form']->start($form); ?>
    <h4><?php echo $view['translator']->trans('autoborna.install.misc.header.url'); ?></h4>
    <h6><?php echo $view['translator']->trans('autoborna.install.misc.subheader.url'); ?></h6>
    <div class="row">
        <div class="col-sm-12">
            <?php echo $view['form']->row($form['site_url']); ?>
        </div>
    </div>
    <h4><?php echo $view['translator']->trans('autoborna.install.misc.header.paths'); ?></h4>
    <h6><?php echo $view['translator']->trans('autoborna.install.misc.subheader.paths'); ?></h6>
    <div class="row">
        <div class="col-sm-6">
            <?php echo $view['form']->row($form['log_path']); ?>
        </div>
        <div class="col-sm-6">
            <?php echo $view['form']->row($form['cache_path']); ?>
        </div>
    </div>
    <?php if (\AppKernel::EXTRA_VERSION) : ?>
    <h4><?php echo $view['translator']->trans('autoborna.install.misc.header.stability'); ?></h4>
    <h6><?php echo $view['translator']->trans('autoborna.install.misc.subheader.stability'); ?></h6>
    <div class="row">
        <div class="col-sm-12">
            <?php echo $view['form']->row($form['update_stability']); ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="row mt-20">
        <div class="col-sm-9">
            <div class="hide" id="waitMessage">
                <div class="alert alert-info">
                    <strong><?php echo $view['translator']->trans('autoborna.install.finalizing'); ?></strong>
                </div>
            </div>
            <?php echo $view->render('AutobornaInstallBundle:Install:navbar.html.php', ['step' => $index, 'count' => $count, 'completedSteps' => $completedSteps]); ?>
        </div>
        <div class="col-sm-3">
            <?php echo $view['form']->row($form['buttons']); ?>
        </div>
    </div>
    <?php echo $view['form']->end($form); ?>
</div>
