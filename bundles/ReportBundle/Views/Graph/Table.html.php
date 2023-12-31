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

<div class="col-md-4">
    <div class="panel panel-default report-list">
        <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $view['translator']->trans($graph['name']); ?>
                    <div class="pull-right">
                        <span class="fa <?php echo isset($graph['iconClass']) ? $graph['iconClass'] : ''; ?>"></span>
                    </div>
                </h3>
        </div>
        <?php if (count($graph['data']) > 0) : ?>
            <div class="table-responsive panel-collapse pull out">
                <?php // We need to dynamically create the table headers based on the result set?>
                <table class="table table-hover table-striped table-bordered report-list" id="reportTable">
                    <thead>
                        <tr>
                            <?php foreach ($graph['data'][0] as $key => $value) : ?>
                                <?php if ('id' != $key) : ?>
                                    <th class="col-report-count"><?php echo ucfirst($key); ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($graph['data'] as $rowKey => $row) : ?>
                        <tr>
                            <?php foreach ($row as $cellName => $cell) : ?>
                                <?php if (array_key_exists('id', $graph['data'][0]) && 'title' == $cellName && isset($graph['link'])) : ?>
                                    <td>
                                        <a href="<?php echo $view['router']->path($graph['link'], ['objectAction' => 'view', 'objectId' => $row['id']]); ?>" data-toggle="ajax">
                                            <?php echo $cell; ?>
                                        </a>
                                    </td>
                                <?php elseif ('id' != $cellName) : ?>
                                    <td>
                                        <?php if (is_numeric($cell)): ?>
                                            <?php echo $cell; ?>
                                        <?php else: ?>
                                            <?php echo $view['assets']->makeLinks($cell); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="panel-body">
                <p class="text-muted"><?php echo $view['translator']->trans('autoborna.report.table.noresults'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>