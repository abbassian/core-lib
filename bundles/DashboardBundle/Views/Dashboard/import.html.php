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
$view['slots']->set('autobornaContent', 'dashboardImport');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.dashboard.import'));
?>

<div class="row">
<?php if ($dashboards) : ?>
    <div class="col-sm-6">
        <div class="ml-sm mt-sm pa-sm">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title"><?php echo $view['translator']->trans('autoborna.dashboard.predefined'); ?></div>
                </div>
                <div class="panel-body">
                    <div class="list-group">
                        <?php foreach ($dashboards as $dashboard => $config) : ?>
                            <div class="list-group-item mt-md <?php echo ($dashboard == $preview) ? 'active' : ''; ?>">
                                <h4 class="list-group-item-heading"><?php echo $view->escape($config['name']); ?></h4>
                                <?php if (!empty($config['description'])):?>
                                <p class="small"><?php echo $view->escape($config['description']); ?></p>
                                <?php endif; ?>
                                <p class="list-group-item-text">
                                    <a href="<?php echo $view['router']->path('autoborna_dashboard_action', ['objectAction' => 'import', 'preview' => $dashboard]); ?>">
                                        <?php echo $view['translator']->trans('autoborna.dashboard.preview'); ?>
                                    </a>&#183;
                                    <a href="<?php echo $view['router']->path('autoborna_dashboard_action', ['objectAction' => 'applyDashboardFile', 'file' => "{$config['type']}.{$dashboard}"]); ?>">
                                        <?php echo $view['translator']->trans('autoborna.core.form.apply'); ?>
                                    </a><?php if ('user' == $config['type']): ?>&#183;
                                    <a href="<?php echo $view['router']->path('autoborna_dashboard_action', ['objectAction' => 'deleteDashboardFile', 'file' => "{$config['type']}.{$dashboard}"]); ?>" data-toggle="confirmation" data-message="<?php echo $view['translator']->trans('autoborna.dashboard.delete_layout'); ?>" data-confirm-text="<?php echo $view->escape($view['translator']->trans('autoborna.core.form.delete')); ?>" data-confirm-callback="executeAction" data-cancel-text="<?php echo $view->escape($view['translator']->trans('autoborna.core.form.cancel')); ?>">
                                        <?php echo $view['translator']->trans('autoborna.core.form.delete'); ?>
                                    </a><?php endif; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
    <div class="col-sm-6">
        <div class="mr-sm mt-sm pa-sm">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title"><?php echo $view['translator']->trans('autoborna.dashboard.import.start.instructions'); ?></div>
                </div>
                <div class="panel-body">
                    <?php echo $view['form']->start($form); ?>
                    <div class="input-group well mt-lg">
                        <?php echo $view['form']->widget($form['file']); ?>
                        <span class="input-group-btn">
                            <?php echo $view['form']->widget($form['start']); ?>
                        </span>
                    </div>
                    <?php echo $view['form']->end($form); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($widgets)) : ?>
    <div class="col-md-12">
        <h2><?php echo $view['translator']->trans('autoborna.dashboard.widgets.preview'); ?></h2>
    </div>
    <div id="dashboard-widgets" class="dashboard-widgets cards">
        <?php if ($widgets): ?>
            <?php foreach ($widgets as $widget): ?>
                <div class="card-flex widget" data-widget-id="<?php echo $widget->getId(); ?>" style="width: <?php echo $widget->getHeight() ? $widget->getWidth().'' : '100'; ?>%; height: <?php echo $widget->getHeight() ? $widget->getHeight().'px' : '300px'; ?>">
                    <?php echo $view->render('AutobornaDashboardBundle:Widget:detail.html.php', ['widget' => $widget]); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
