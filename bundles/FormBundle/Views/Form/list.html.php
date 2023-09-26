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
    $view->extend('AutobornaFormBundle:Form:index.html.php');
}

?>
<?php if (count($items)): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered" id="formTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#formTable',
                        'routeBase'       => 'form',
                        'templateButtons' => [
                            'delete' => $permissions['form:forms:deleteown'] || $permissions['form:forms:deleteother'],
                        ],
                        'customButtons' => [
                            [
                                'confirm' => [
                                    'message'       => $view['translator']->trans('autoborna.form.confirm_batch_rebuild'),
                                    'confirmText'   => $view['translator']->trans('autoborna.form.rebuild'),
                                    'confirmAction' => $view['router']->path(
                                        'autoborna_form_action',
                                        ['objectAction' => 'batchRebuildHtml']
                                    ),
                                    'iconClass'       => 'fa fa-fw fa-refresh',
                                    'btnText'         => $view['translator']->trans('autoborna.form.rebuild'),
                                    'precheck'        => 'batchActionPrecheck',
                                    'confirmCallback' => 'executeBatchAction',
                                ],
                                'primary' => true,
                            ],
                        ],
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'f.name',
                        'text'       => 'autoborna.core.name',
                        'class'      => 'col-form-name',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'c.title',
                        'text'       => 'autoborna.core.category',
                        'class'      => 'visible-md visible-lg col-form-category',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'submission_count',
                        'text'       => 'autoborna.form.form.results',
                        'class'      => 'visible-md visible-lg col-form-submissions',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'f.dateAdded',
                        'text'       => 'autoborna.lead.import.label.dateAdded',
                        'class'      => 'visible-md visible-lg col-form-dateAdded',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'f.dateModified',
                        'text'       => 'autoborna.lead.import.label.dateModified',
                        'class'      => 'visible-md visible-lg col-form-dateModified',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'f.createdByUser',
                        'text'       => 'autoborna.core.createdby',
                        'class'      => 'visible-md visible-lg col-form-createdby',
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'form',
                        'orderBy'    => 'f.id',
                        'text'       => 'autoborna.core.id',
                        'class'      => 'visible-md visible-lg col-form-id',
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $i): ?>
                <?php $item = $i[0]; ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'AutobornaCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'edit' => $security->hasEntityAccess(
                                        $permissions['form:forms:editown'],
                                        $permissions['form:forms:editother'],
                                        $item->getCreatedBy()
                                    ),
                                    'clone'  => $permissions['form:forms:create'],
                                    'delete' => $security->hasEntityAccess(
                                        $permissions['form:forms:deleteown'],
                                        $permissions['form:forms:deleteother'],
                                        $item->getCreatedBy()
                                    ),
                                ],
                                'routeBase'     => 'form',
                                'customButtons' => [
                                    [
                                        'attr' => [
                                            'data-toggle' => '',
                                            'target'      => '_blank',
                                            'href'        => $view['router']->path(
                                                'autoborna_form_action',
                                                ['objectAction' => 'preview', 'objectId' => $item->getId()]
                                            ),
                                        ],
                                        'iconClass' => 'fa fa-camera',
                                        'btnText'   => 'autoborna.form.form.preview',
                                    ],
                                    [
                                        'attr' => [
                                            'data-toggle' => 'ajax',
                                            'href'        => $view['router']->path(
                                                'autoborna_form_action',
                                                ['objectAction' => 'results', 'objectId' => $item->getId()]
                                            ),
                                        ],
                                        'iconClass' => 'fa fa-database',
                                        'btnText'   => 'autoborna.form.form.results',
                                    ],
                                ],
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>
                            <?php echo $view->render(
                                'AutobornaCoreBundle:Helper:publishstatus_icon.html.php',
                                ['item' => $item, 'model' => 'form.form']
                            ); ?>
                            <a href="<?php echo $view['router']->path(
                                'autoborna_form_action',
                                ['objectAction' => 'view', 'objectId' => $item->getId()]
                            ); ?>" data-toggle="ajax" data-menu-link="autoborna_form_index">
                                <?php echo $item->getName(); ?>
                                <?php if ('campaign' == $item->getFormType()): ?>
                                    <span data-toggle="tooltip" title="<?php echo $view['translator']->trans(
                                        'autoborna.form.icon_tooltip.campaign_form'
                                    ); ?>"><i class="fa fa-fw fa-cube"></i></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <?php if ($description = $item->getDescription()): ?>
                            <div class="text-muted mt-4">
                                <small><?php echo $description; ?></small>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="visible-md visible-lg">
                        <?php $category = $item->getCategory(); ?>
                        <?php $catName  = ($category) ? $category->getTitle() : $view['translator']->trans('autoborna.core.form.uncategorized'); ?>
                        <?php $color    = ($category) ? '#'.$category->getColor() : 'inherit'; ?>
                        <span style="white-space: nowrap;"><span class="label label-default pa-4" style="border: 1px solid #d5d5d5; background: <?php echo $color; ?>;"> </span> <span><?php echo $catName; ?></span></span>
                    </td>
                    <td class="visible-md visible-lg">
                        <a href="<?php echo $view['router']->path(
                            'autoborna_form_action',
                            ['objectAction' => 'results', 'objectId' => $item->getId()]
                        ); ?>" data-toggle="ajax" data-menu-link="autoborna_form_index" class="btn btn-primary btn-xs" <?php echo (0
                            == $i['submission_count']) ? 'disabled=disabled' : ''; ?>>
                            <?php echo $view['translator']->trans(
                                'autoborna.form.form.viewresults',
                                ['%count%' => $i['submission_count']]
                            ); ?>
                        </a>
                    </td>
                    <td class="visible-md visible-lg"><?php echo $item->getDateAdded() ? $view['date']->toFull($item->getDateAdded()) : ''; ?></td>
                    <td class="visible-md visible-lg"><?php echo $item->getDateModified() ? $view['date']->toFull($item->getDateModified()) : ''; ?></td>
                    <td class="visible-md visible-lg"><?php echo $item->getCreatedByUser(); ?></td>
                    <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="panel-footer">
            <?php echo $view->render(
                'AutobornaCoreBundle:Helper:pagination.html.php',
                [
                    'totalItems' => $totalItems,
                    'page'       => $page,
                    'limit'      => $limit,
                    'baseUrl'    => $view['router']->path('autoborna_form_index'),
                    'sessionVar' => 'form',
                ]
            ); ?>
        </div>
    </div>
<?php else: ?>
    <?php echo $view->render('AutobornaCoreBundle:Helper:noresults.html.php', ['tip' => 'autoborna.form.noresults.tip']); ?>
<?php endif; ?>
