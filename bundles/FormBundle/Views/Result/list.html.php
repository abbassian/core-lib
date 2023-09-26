<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl):
    $view->extend('AutobornaFormBundle:Result:index.html.php');
endif;

$formId = $form->getId();

?>
<div class="table-responsive table-responsive-force">
    <table class="table table-hover table-striped table-bordered formresult-list" id="formResultTable">
        <thead>
            <tr>
                <?php
                if ($canDelete):
                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'checkall'        => 'true',
                    'target'          => '#formResultTable',
                    'routeBase'       => 'form_results',
                    'query'           => ['formId' => $formId],
                    'templateButtons' => [
                        'delete' => $canDelete,
                    ],
                ]);
                endif;

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'formresult.'.$formId,
                    'orderBy'    => 's.id',
                    'text'       => 'autoborna.form.report.submission.id',
                    'class'      => 'col-formresult-id',
                    'filterBy'   => 's.id',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'formresult.'.$formId,
                    'orderBy'    => 's.lead_id',
                    'text'       => 'autoborna.lead.report.contact_id',
                    'class'      => 'col-formresult-lead-id',
                    'filterBy'   => 's.lead_id',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'formresult.'.$formId,
                    'orderBy'    => 's.date_submitted',
                    'text'       => 'autoborna.form.result.thead.date',
                    'class'      => 'col-formresult-date',
                    'default'    => true,
                    'filterBy'   => 's.date_submitted',
                    'dataToggle' => 'date',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'formresult.'.$formId,
                    'orderBy'    => 'i.ip_address',
                    'text'       => 'autoborna.core.ipaddress',
                    'class'      => 'col-formresult-ip',
                    'filterBy'   => 'i.ip_address',
                ]);

                $fields     = $form->getFields();
                $fieldCount = ($canDelete) ? 4 : 3;
                foreach ($fields as $f):
                    if (in_array($f->getType(), $viewOnlyFields) || false === $f->getSaveResult()) {
                        continue;
                    }
                    echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                        'sessionVar' => 'formresult.'.$formId,
                        'orderBy'    => 'r.'.$f->getAlias(),
                        'text'       => $f->getLabel(),
                        'class'      => 'col-formresult-field col-formresult-field'.$f->getId(),
                        'filterBy'   => 'r.'.$f->getAlias(),
                    ]);
                    ++$fieldCount;
                endforeach;
                ?>
            </tr>
        </thead>
        <tbody>
        <?php if (count($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php $item['name'] = $view['translator']->trans('autoborna.form.form.results.name', ['%id%' => $item['id']]); ?>
            <tr>
                <?php if ($canDelete): ?>
                <td>
                    <?php
                    echo $view->render('AutobornaCoreBundle:Helper:list_actions.html.php', [
                        'item'            => $item,
                        'templateButtons' => [
                            'delete' => $canDelete,
                        ],
                        'route'   => 'autoborna_form_results_action',
                        'langVar' => 'form.results',
                        'query'   => [
                            'formId'       => $formId,
                            'objectAction' => 'delete',
                        ],
                    ]);
                    ?>
                </td>
                <?php endif; ?>

                <td><?php echo $view->escape($item['id']); ?></td>
                <td>
                    <?php if (!empty($item['leadId'])): ?>
                    <a href="<?php echo $view['router']->path('autoborna_contact_action', ['objectAction' => 'view', 'objectId' => $item['leadId']]); ?>" data-toggle="ajax">
                        <?php echo $view->escape($item['leadId']); ?>
                    </a>
                    <?php endif; ?>
                </td>
                <td><?php echo $view['date']->toFull($item['dateSubmitted'], 'UTC'); ?></td>
                <td><?php echo $view->escape($item['ipAddress']); ?></td>
                <?php foreach ($item['results'] as $key => $r): ?>
                    <?php $isTextarea = 'textarea' === $r['type']; ?>
                    <td <?php echo $isTextarea ? 'class="long-text"' : ''; ?>>
                        <?php if ($isTextarea) : ?>
                            <?php echo $view->escape(nl2br($r['value'])); ?>
                        <?php elseif ('file' === $r['type']) : ?>
                            <a href="<?php echo $view['router']->path('autoborna_form_file_download', ['submissionId' => $item['id'], 'field' => $key]); ?>">
                                <?php echo $view->escape($r['value']); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $view->escape($r['value']); ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?php echo $fieldCount; ?>">
                    <?php echo $view->render('AutobornaCoreBundle:Helper:noresults.html.php'); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="panel-footer">
    <?php echo $view->render('AutobornaCoreBundle:Helper:pagination.html.php', [
        'totalItems' => $totalCount,
        'page'       => $page,
        'limit'      => $limit,
        'baseUrl'    => $view['router']->path('autoborna_form_results', ['objectId' => $form->getId()]),
        'sessionVar' => 'formresult.'.$formId,
    ]); ?>
</div>
