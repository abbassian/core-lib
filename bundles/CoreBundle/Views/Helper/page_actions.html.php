<?php

use Autoborna\CoreBundle\Templating\Helper\ButtonHelper;

if (!isset($item)) {
    $item = null;
}

if (!isset($tooltip)) {
    $tooltip = null;
}

$view['buttons']->reset($app->getRequest(), ButtonHelper::LOCATION_PAGE_ACTIONS, ButtonHelper::TYPE_BUTTON_DROPDOWN, $item);
include 'action_button_helper.php';

foreach ($templateButtons as $action => $enabled) {
    if (!$enabled) {
        continue;
    }

    $path     = false;
    $primary  = false;
    $priority = 0;

    switch ($action) {
        case 'clone':
        case 'abtest':
            $actionQuery = [
                'objectId' => ('abtest' == $action && method_exists($item, 'getVariantParent') && $item->getVariantParent())
                    ? $item->getVariantParent()->getId() : $item->getId(),
            ];
            $icon = ('clone' == $action) ? 'copy' : 'sitemap';
            $path = $view['router']->path($actionRoute, array_merge(['objectAction' => $action], $actionQuery, $query));
            break;
        case 'close':
            $closeParameters = isset($routeVars['close']) ? $routeVars['close'] : [];
            $icon            = 'remove';
            $path            = $view['router']->path($indexRoute, $closeParameters);
            $primary         = true;
            $priority        = 200;
            break;
        case 'new':
        case 'edit':
            $actionQuery = ('edit' == $action) ? ['objectId' => $item->getId()] : [];
            $icon        = ('edit' == $action) ? 'pencil-square-o' : 'plus';
            $path        = $view['router']->path($actionRoute, array_merge(['objectAction' => $action], $actionQuery, $query));
            $primary     = true;
            break;
        case 'delete':
            $view['buttons']->addButton(
                [
                    'confirm' => [
                        'message' => $view['translator']->trans(
                            'autoborna.'.$langVar.'.form.confirmdelete',
                            ['%name%' => $item->$nameGetter().' ('.$item->getId().')']
                        ),
                        'confirmAction' => $view['router']->path(
                            $actionRoute,
                            array_merge(['objectAction' => 'delete', 'objectId' => $item->getId()], $query)
                        ),
                        'template' => 'delete',
                        'btnClass' => false,
                    ],
                    'priority' => -1,
                ]
            );
            break;
    }

    if ($path) {
        $mergeAttr = (!in_array($action, ['edit', 'new'])) ? [] : $editAttr;
        $view['buttons']->addButton(
            [
                'attr' => array_merge(
                    [
                        'class'       => 'btn btn-default',
                        'href'        => $path,
                        'data-toggle' => 'ajax',
                    ],
                    $mergeAttr
                ),
                'iconClass' => 'fa fa-'.$icon,
                'btnText'   => $view['translator']->trans('autoborna.core.form.'.$action),
                'priority'  => $priority,
                'primary'   => $primary,
                'tooltip'   => $tooltip,
            ]
        );
    }
}

echo '<div class="std-toolbar btn-group">';

$dropdownOpenHtml = '<button type="button" class="btn btn-default btn-nospin dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-caret-down"></i></button>'
    ."\n";
$dropdownOpenHtml .= '<ul class="dropdown-menu dropdown-menu-right" role="menu">'."\n";
echo $view['buttons']->renderButtons($dropdownOpenHtml, '</ul>');

echo '</div>';

echo $extraHtml;
