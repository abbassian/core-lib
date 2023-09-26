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
        <?php foreach ($items as $item): ?>
            <?php /** @var \Autoborna\LeadBundle\Entity\Lead $item */ ?>
            <?php $fields = $item->getFields(); ?>
            <tr<?php if (!empty($highlight)): echo ' class="warning"'; endif; ?>>
                <td>
                    <?php
                    $hasEditAccess = $security->hasEntityAccess(
                        $permissions['lead:leads:editown'],
                        $permissions['lead:leads:editother'],
                        $item->getPermissionUser()
                    );

                    $custom = [];

                    $custom[] = [
                        'attr'      => [
                            'href'        => $view['router']->path(
                                'autoborna_contact_action',
                                [
                                    'objectAction' => 'view',
                                    'objectId'     => $item->getId(),
                                ]
                            ),
                            'data-toggle' => 'ajax',
                            'data-method' => 'POST',
                        ],
                        'btnText'   => 'autoborna.core.details',
                        'iconClass' => 'fa fa-info-circle',
                    ];

                    if ($hasEditAccess && !empty($currentList)) {
                        //this lead was manually added to a list so give an option to remove them
                        $custom[] = [
                            'attr' => [
                                'href' => $view['router']->path('autoborna_segment_action', [
                                    'objectAction' => 'removeLead',
                                    'objectId'     => $currentList['id'],
                                    'leadId'       => $item->getId(),
                                ]),
                                'data-toggle' => 'ajax',
                                'data-method' => 'POST',
                            ],
                            'btnText'   => 'autoborna.lead.lead.remove.fromlist',
                            'iconClass' => 'fa fa-remove',
                        ];
                    }

                    if (!empty($fields['core']['email']['value'])) {
                        $custom[] = [
                            'attr' => [
                                'data-toggle' => 'ajaxmodal',
                                'data-target' => '#AutobornaSharedModal',
                                'data-header' => $view['translator']->trans('autoborna.lead.email.send_email.header', ['%email%' => $fields['core']['email']['value']]),
                                'href'        => $view['router']->path('autoborna_contact_action', ['objectId' => $item->getId(), 'objectAction' => 'email', 'list' => 1]),
                            ],
                            'btnText'   => 'autoborna.lead.email.send_email',
                            'iconClass' => 'fa fa-send',
                        ];
                    }

                    echo $view->render('AutobornaCoreBundle:Helper:list_actions.html.php', [
                        'item'            => $item,
                        'templateButtons' => [
                            'edit'   => $hasEditAccess,
                            'delete' => $security->hasEntityAccess($permissions['lead:leads:deleteown'], $permissions['lead:leads:deleteother'], $item->getPermissionUser()),
                        ],
                        'routeBase'     => 'contact',
                        'langVar'       => 'lead.lead',
                        'customButtons' => $custom,
                    ]);
                    ?>
                </td>
                <?php
                $columsAliases = array_flip($columns);
                foreach ($columns as $column=>$label) {
                    $template = 'AutobornaLeadBundle:Lead\row:'.$column.'.html.php';
                    if (!$view->exists($template)) {
                        $template = 'AutobornaLeadBundle:Lead\row:default.html.php';
                    }
                    echo $view->render(
                        $template,
                        [
                            'item'          => $item,
                            'fields'        => $fields,
                            'label'         => $label,
                            'column'        => $column,
                            'columns'       => $columns,
                            'noContactList' => $noContactList,
                            'class'         => array_search($column, $columsAliases) > 1 ? 'hidden-xs' : '',
                        ]
                    );
                }
                ?>
            </tr>
        <?php endforeach; ?>
