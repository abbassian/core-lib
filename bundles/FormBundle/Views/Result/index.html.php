<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$view->extend('AutobornaCoreBundle:Default:content.html.php');
$view['slots']->set('autobornaContent', 'formresult');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.form.result.header.index', [
    '%name%' => $form->getName(),
]));

$buttons = [];

$buttons[] = [
    'attr' => [
        'target'      => '_new',
        'data-toggle' => '',
        'class'       => 'btn btn-default btn-nospin',
        'href'        => $view['router']->path('autoborna_form_export', ['objectId' => $form->getId(), 'format' => 'html']),
    ],
    'btnText'   => $view['translator']->trans('autoborna.form.result.export.html'),
    'iconClass' => 'fa fa-file-code-o',
    'primary'   => true,
];

$buttons[] = [
    'attr' => [
        'data-toggle' => '',
        'class'       => 'btn btn-default btn-nospin',
        'href'        => $view['router']->path('autoborna_form_export', ['objectId' => $form->getId(), 'format' => 'csv']),
    ],
    'btnText'   => $view['translator']->trans('autoborna.form.result.export.csv'),
    'iconClass' => 'fa fa-file-text-o',
    'primary'   => true,
];

if (class_exists(Spreadsheet::class)) {
    $buttons[] = [
        'attr' => [
            'data-toggle' => '',
            'class'       => 'btn btn-default btn-nospin',
            'href'        => $view['router']->path('autoborna_form_export', ['objectId' => $form->getId(), 'format' => 'xlsx']),
        ],
        'btnText'   => $view['translator']->trans('autoborna.form.result.export.xlsx'),
        'iconClass' => 'fa fa-file-excel-o',
        'primary'   => true,
    ];
}

$buttons[] =
    [
        'attr' => [
                'class'       => 'btn btn-default',
                'href'        => $view['router']->path('autoborna_form_action', ['objectAction' => 'view', 'objectId'=> $form->getId()]),
                'data-toggle' => 'ajax',
            ],
        'iconClass' => 'fa fa-remove',
        'btnText'   => $view['translator']->trans('autoborna.core.form.close'),
    ];

$view['slots']->set('actions', $view->render('AutobornaCoreBundle:Helper:page_actions.html.php', ['customButtons' => $buttons]));
?>

<div class="page-list">
    <?php $view['slots']->output('_content'); ?>
</div>
