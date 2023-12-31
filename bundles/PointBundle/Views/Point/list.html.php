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
    $view->extend('AutobornaPointBundle:Point:index.html.php');
}
?>

<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered point-list" id="pointTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#pointTable',
                        'routeBase'       => 'point',
                        'templateButtons' => [
                            'delete' => $permissions['point:points:delete'],
                        ],
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'point',
                        'orderBy'    => 'p.name',
                        'text'       => 'autoborna.core.name',
                        'class'      => 'col-point-name',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'point',
                        'orderBy'    => 'cat.title',
                        'text'       => 'autoborna.core.category',
                        'class'      => 'visible-md visible-lg col-point-category',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'point',
                        'orderBy'    => 'p.delta',
                        'text'       => 'autoborna.point.thead.delta',
                        'class'      => 'visible-md visible-lg col-point-delta',
                    ]
                );

                echo '<th class="col-point-action">'.$view['translator']->trans('autoborna.point.thead.action').'</th>';

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'point',
                        'orderBy'    => 'p.id',
                        'text'       => 'autoborna.core.id',
                        'class'      => 'visible-md visible-lg col-point-id',
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
                                    'edit'   => $permissions['point:points:edit'],
                                    'clone'  => $permissions['point:points:create'],
                                    'delete' => $permissions['point:points:delete'],
                                ],
                                'routeBase' => 'point',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>

                            <?php echo $view->render(
                                'AutobornaCoreBundle:Helper:publishstatus_icon.html.php',
                                ['item' => $item, 'model' => 'point']
                            ); ?>
                            <?php if ($permissions['point:points:edit']): ?>
                                <a href="<?php echo $view['router']->path(
                                    'autoborna_point_action',
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
                    <td class="visible-md visible-lg"><?php echo $item->getDelta(); ?></td>
                    <?php
                    $type   = $item->getType();
                    $action = (isset($actions[$type])) ? $actions[$type]['label'] : '';
                    ?>
                    <td><?php echo $view['translator']->trans($action); ?></td>
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
                'menuLinkId' => 'autoborna_point_index',
                'baseUrl'    => $view['router']->path('autoborna_point_index'),
                'sessionVar' => 'point',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:noresults.html.php',
        ['tip' => 'autoborna.point.action.noresults.tip']
    ); ?>
<?php endif; ?>
