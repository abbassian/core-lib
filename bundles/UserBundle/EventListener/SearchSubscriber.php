<?php

namespace Autoborna\UserBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\UserBundle\Model\RoleModel;
use Autoborna\UserBundle\Model\UserModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var RoleModel
     */
    private $userRoleModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var TemplatingHelper
     */
    private $templating;

    public function __construct(
        UserModel $userModel,
        RoleModel $roleModel,
        CorePermissions $security,
        TemplatingHelper $templating
    ) {
        $this->userModel     = $userModel;
        $this->userRoleModel = $roleModel;
        $this->security      = $security;
        $this->templating    = $templating;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::GLOBAL_SEARCH      => ['onGlobalSearch', 0],
            CoreEvents::BUILD_COMMAND_LIST => ['onBuildCommandList', 0],
        ];
    }

    public function onGlobalSearch(AutobornaEvents\GlobalSearchEvent $event)
    {
        $str = $event->getSearchString();
        if (empty($str)) {
            return;
        }

        if ($this->security->isGranted('user:users:view')) {
            $users = $this->userModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $str,
                ]);

            if (count($users) > 0) {
                $userResults = [];
                $canEdit     = $this->security->isGranted('user:users:edit');
                foreach ($users as $user) {
                    $userResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaUserBundle:SubscribedEvents\Search:global_user.html.php',
                        [
                            'user'    => $user,
                            'canEdit' => $canEdit,
                        ]
                    )->getContent();
                }
                if (count($users) > 5) {
                    $userResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaUserBundle:SubscribedEvents\Search:global_user.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($users) - 5),
                        ]
                    )->getContent();
                }
                $userResults['count'] = count($users);
                $event->addResults('autoborna.user.users', $userResults);
            }
        }

        if ($this->security->isGranted('user:roles:view')) {
            $roles = $this->userRoleModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $str,
                ]);
            if (count($roles)) {
                $roleResults = [];
                $canEdit     = $this->security->isGranted('user:roles:edit');

                foreach ($roles as $role) {
                    $roleResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaUserBundle:SubscribedEvents\Search:global_role.html.php',
                        [
                            'role'    => $role,
                            'canEdit' => $canEdit,
                        ]
                    )->getContent();
                }
                if (count($roles) > 5) {
                    $roleResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaUserBundle:SubscribedEvents\Search:global_role.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($roles) - 5),
                        ]
                    )->getContent();
                }
                $roleResults['count'] = count($roles);
                $event->addResults('autoborna.user.roles', $roleResults);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        if ($this->security->isGranted('user:users:view')) {
            $event->addCommands(
                'autoborna.user.users',
                $this->userModel->getCommandList()
            );
        }
        if ($this->security->isGranted('user:roles:view')) {
            $event->addCommands(
                'autoborna.user.roles',
                $this->userRoleModel->getCommandList()
            );
        }
    }
}
