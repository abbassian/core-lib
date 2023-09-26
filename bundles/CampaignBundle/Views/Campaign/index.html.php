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
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.campaign.campaigns'));

$view['slots']->set(
    'actions',
    $view->render(
        'AutobornaCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => $permissions['campaign:campaigns:create'],
            ],
            'routeBase' => 'campaign',
        ]
    )
);
?>

<div class="panel panel-default bdr-t-wdh-0">
	<?php echo $view->render('AutobornaCoreBundle:Helper:list_toolbar.html.php', [
        'searchValue' => $searchValue,
        'searchHelp'  => 'autoborna.core.help.searchcommands',
        'action'      => $currentRoute,
        'filters'     => $filters,
    ]); ?>

    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>