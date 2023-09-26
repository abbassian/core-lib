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
$view['slots']->set('autobornaContent', 'integration');

$header = $view['translator']->trans('autoborna.plugin.manage.plugins');
if ($pluginFilter) {
    $filterValue = $pluginFilter['id'];
    $header .= ' - '.$pluginFilter['name'];
} else {
    $filterValue = '';
}
$view['slots']->set('headerTitle', $header);

$view['slots']->set('actions', $view->render('AutobornaCoreBundle:Helper:page_actions.html.php', [
    'customButtons' => [
        [
            'attr' => [
                'data-toggle' => 'ajax',
                'href'        => $view['router']->path('autoborna_plugin_reload'),
            ],
            'btnText'   => $view['translator']->trans('autoborna.plugin.reload.plugins'),
            'iconClass' => 'fa fa-cubes',
            'tooltip'   => 'autoborna.plugin.reload.plugins.tooltip',
        ],
    ],
]));
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <div class="panel-body">
        <div class="box-layout">
            <div class="row">
                <div class="col-xs-3 va-m">
                    <select id="integrationFilter" onchange="Autoborna.filterIntegrations(true);" class="form-control" data-placeholder="<?php echo $view['translator']->trans('autoborna.integration.filter.all'); ?>">
                        <option value=""></option>
                        <?php foreach ($plugins as $a): ?>
                        <option<?php echo ($filterValue === $a['id']) ? ' selected' : ''; ?> value="<?php echo $view->escape($a['id']); ?>">
                            <?php echo $a['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
