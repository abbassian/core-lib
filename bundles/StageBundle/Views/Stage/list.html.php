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
    $view->extend('AutobornaStageBundle:Stage:index.html.php');
}
?>

<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered stage-list" id="stageTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#stageTable',
                        'routeBase'       => 'stage',
                        'templateButtons' => [
                            'delete' => $permissions['stage:stages:delete'],
                        ],
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'stage',
                        'orderBy'    => 's.name',
                        'text'       => 'autoborna.core.name',
                        'class'      => 'col-stage-name',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'stage',
                        'orderBy'    => 'c.title',
                        'text'       => 'autoborna.core.category',
                        'class'      => 'visible-md visible-lg col-stage-category',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'stage',
                        'orderBy'    => 's.id',
                        'text'       => 'autoborna.core.id',
                        'class'      => 'visible-md visible-lg col-stage-id',
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'AutobornaCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'edit'   => $permissions['stage:stages:edit'],
                                    'clone'  => $permissions['stage:stages:create'],
                                    'delete' => $permissions['stage:stages:delete'],
                                ],
                                'routeBase' => 'stage',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>

                            <?php echo $view->render(
                                'AutobornaCoreBundle:Helper:publishstatus_icon.html.php',
                                ['item' => $item, 'model' => 'stage']
                            ); ?>
                            <?php if ($permissions['stage:stages:edit']): ?>
                            <a href="<?php echo $view['router']->url(
                                'autoborna_stage_action',
                                ['objectAction' => 'edit', 'objectId' => $item->getId()]
                            ); ?>" data-toggle="ajax">
                                <?php echo $item->getName(); ?>
                            </a>
                            <?php else: ?>
                                <?php echo $item->getName(); ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($description = $item->getDescription()): ?>
                            <div class="text-muted mt-4">
                                <small><?php echo $description; ?></small>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="visible-md visible-lg">
                        <?php $category = $item->getCategory(); ?>
                        <?php $catName  = ($category)
                            ? $category->getTitle()
                            : $view['translator']->trans(
                                'autoborna.core.form.uncategorized'
                            ); ?>
                        <?php $color = ($category) ? '#'.$category->getColor() : 'inherit'; ?>
                        <span style="white-space: nowrap;"><span class="label label-default pa-4"
                                                                 style="border: 1px solid #d5d5d5; background: <?php echo $color; ?>;"> </span> <span><?php echo $catName; ?></span></span>
                    </td>
                    <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <?php echo $view->render(
            'AutobornaCoreBundle:Helper:pagination.html.php',
            [
                'totalItems' => count($items),
                'page'       => $page,
                'limit'      => $limit,
                'menuLinkId' => 'autoborna_stage_index',
                'baseUrl'    => $view['router']->url('autoborna_stage_index'),
                'sessionVar' => 'stage',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:noresults.html.php',
        ['tip' => 'autoborna.stage.action.noresults.tip']
    ); ?>
<?php endif; ?>
