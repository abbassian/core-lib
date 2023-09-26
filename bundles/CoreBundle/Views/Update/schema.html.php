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
$view['slots']->set('autobornaContent', 'update');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.core.update.index'));

if ($failed) {
    $message = $view['translator']->trans('autoborna.core.update.error_performing_migration');
    $class   = 'danger';
} elseif ($noMigrations) {
    $message = $view['translator']->trans('autoborna.core.update.schema_uptodate');
    $class   = 'autoborna';
} else {
    $message = $view['translator']->trans('autoborna.core.update.schema_updated');
    $class   = 'success';
}
?>

<div class="panel panel-default mnb-5 bdr-t-wdh-0">
    <div id="update-panel" class="panel-body">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="alert alert-<?php echo $class; ?> mb-sm">
                <?php echo $view['translator']->trans($message); ?>
            </div>
            <?php if (!$failed): ?>
                <div class="text-center">
                    <a href="<?php echo $view['router']->path('autoborna_dashboard_index'); ?>" data-toggle="ajax"><?php echo $view['translator']->trans('autoborna.core.go_to_dashboard'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
