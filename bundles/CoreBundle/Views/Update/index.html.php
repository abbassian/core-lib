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

/** @var bool $isComposerEnabled */
$isComposerEnabled = $isComposerEnabled;
?>

<div class="panel panel-default mnb-5 bdr-t-wdh-0">
    <div id="update-panel" class="panel-body">
        <div class="col-sm-offset-2 col-sm-8">
            <?php if ($updateData['error'] || 'autoborna.core.updater.running.latest.version' == $updateData['message']) : ?>
                <div class="alert alert-autoborna">
                    <?php echo $view['translator']->trans($updateData['message']); ?>
                </div>
            <?php else : ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">
                        <?php echo $view['translator']->trans('autoborna.core.update.available'); ?>
                    </h2>
                    <div class="panel-body">
                        <table class="table table-hover table-striped table-bordered addon-list" id="updateTable">
                            <tbody>
                            <tr>
                                <td><?php echo $view['translator']->trans('autoborna.core.update.current.version'); ?></td>
                                <td><?php echo $currentVersion; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $view['translator']->trans('autoborna.core.update.upgrade.version'); ?></td>
                                <td><?php echo $updateData['version']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $view['translator']->trans('autoborna.core.update.announcement'); ?></td>
                                <td class="break-word"><a href="<?php echo $updateData['announcement']; ?>" target="_blank"><?php echo $updateData['announcement']; ?></a></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center text-danger"><?php echo $view['translator']->trans('autoborna.core.update.backup_warning'); ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <?php if ($isComposerEnabled): ?>
                        <div class="alert alert-warning text-center">
                            <strong><?php echo $view['translator']->trans('autoborna.core.update.composer'); ?></strong>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <strong><?php echo $view['translator']->trans('autoborna.core.update.ui.deprecated'); ?></strong>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" onclick="Autoborna.processUpdate('update-panel', 1, '<?php echo base64_encode(json_encode([])); ?>');"><?php echo $view['translator']->trans('autoborna.core.update.now'); ?></button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
