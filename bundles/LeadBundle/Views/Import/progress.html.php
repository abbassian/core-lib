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

$object     = $app->getRequest()->get('object', 'contacts');
$objectName = $view['translator']->trans($objectName);

$view['slots']->set('autobornaContent', 'leadImport');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.lead.import.leads', ['%object%' => $objectName]));

$percent    = $progress->toPercent();
$id         = ($complete) ? 'leadImportProgressComplete' : 'leadImportProgress';
$header     = ($complete) ? 'autoborna.lead.import.success' : 'autoborna.lead.import.donotleave';
?>

<div class="row ma-lg" id="<?php echo $id; ?>">
    <div class="col-sm-offset-3 col-sm-6 text-center">
        <div class="panel panel-<?php echo ($complete) ? 'success' : 'danger'; ?>">
            <div class="panel-heading">

                <h4 class="panel-title"><?php echo $view['translator']->trans($header, ['object' => $object]); ?></h4>
            </div>
            <div class="panel-body">
                <?php if (!$complete): ?>
                    <h4><?php echo $view['translator']->trans('autoborna.lead.import.inprogress'); ?></h4>
                <?php else: ?>
                    <h4>
                        <?php echo $view['translator']->trans(
                            'autoborna.lead.import.stats',
                            [
                            '%merged%'  => $import->getUpdatedCount(),
                            '%created%' => $import->getInsertedCount(),
                            '%ignored%' => $import->getIgnoredCount(),
                            ]
                        ); ?>
                    </h4>
                <?php endif; ?>
                <div class="progress mt-md" style="height:50px;">
                    <div class="progress-bar-import progress-bar progress-bar-striped<?php if (!$complete) {
                            echo ' active';
                        } ?>" role="progressbar" aria-valuenow="<?php echo $progress->getDone(); ?>" aria-valuemin="0" aria-valuemax="<?php echo $progress->getTotal(); ?>" style="width: <?php echo $percent; ?>%; height: 50px;">
                        <span class="sr-only"><?php echo $percent; ?>%</span>
                    </div>
                </div>
            </div>
            <?php if (!empty($failedRows)): ?>
                <ul class="list-group">
                    <?php foreach ($failedRows as $row): ?>
                        <?php $lineNumber = isset($row['properties']['line']) ? $row['properties']['line'] : 'N/A'; ?>
                        <?php $failure    = isset($row['properties']['error']) ? $row['properties']['error'] : 'N/A'; ?>
                        <li class="list-group-item text-left">
                            <a target="_new" class="text-danger">
                                <?php echo "(#$lineNumber) $failure"; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="panel-footer">
                <p class="small"><span class="imported-count"><?php echo $progress->getDone(); ?></span> / <span class="total-count"><?php echo $progress->getTotal(); ?></span></p>
                <?php if (!$complete): ?>
                    <div>
                        <a class="btn btn-danger" href="<?php echo $view['router']->path(
                            'autoborna_import_action',
                            ['objectAction' => 'cancel', 'object' => $object]
                        ); ?>" data-toggle="ajax">
                            <?php echo $view['translator']->trans('autoborna.core.form.cancel'); ?>
                        </a>
                        <a class="btn btn-primary" href="<?php echo $view['router']->path(
                            'autoborna_import_action',
                            ['objectAction' => 'queue', 'object' => $object]
                        ); ?>" data-toggle="ajax">
                            <?php echo $view['translator']->trans('autoborna.lead.import.queue.btn'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div>
                        <a class="btn btn-success" href="<?php echo $view['router']->path($indexRoute, $indexRouteParams); ?>" data-toggle="ajax">
                            <?php echo $view['translator']->trans('autoborna.lead.list.view', ['%objects%' => $objectName]); ?>
                        </a>
                        <a class="btn btn-success" href="<?php echo $view['router']->path('autoborna_import_index', ['object' => $object]); ?>" data-toggle="ajax">
                            <?php echo $view['translator']->trans('autoborna.lead.view.imports'); ?>
                        </a>
                        <a class="btn btn-success" href="<?php echo $view['router']->path(
                            'autoborna_import_action',
                            ['objectAction' => 'view', 'objectId' => $import->getId(), 'object' => $object]
                        ); ?>" data-toggle="ajax">
                            <?php echo $view['translator']->trans('autoborna.lead.import.result.info', ['%import%' => $import->getName()]); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
