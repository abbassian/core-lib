<?php

namespace Autoborna\FormBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\FormBundle\Model\FormModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var FormModel
     */
    private $formModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var TemplatingHelper
     */
    private $templating;

    public function __construct(
        UserHelper $userHelper,
        FormModel $formModel,
        CorePermissions $security,
        TemplatingHelper $templating
    ) {
        $this->userHelper = $userHelper;
        $this->formModel  = $formModel;
        $this->security   = $security;
        $this->templating = $templating;
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

        $filter = ['string' => $str, 'force' => ''];

        $permissions = $this->security->isGranted(['form:forms:viewown', 'form:forms:viewother'], 'RETURN_ARRAY');
        if ($permissions['form:forms:viewown'] || $permissions['form:forms:viewother']) {
            //only show own forms if the user does not have permission to view others
            if (!$permissions['form:forms:viewother']) {
                $filter['force'] = [
                    ['column' => 'f.createdBy', 'expr' => 'eq', 'value' => $this->userHelper->getUser()->getId()],
                ];
            }

            $forms = $this->formModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $filter,
                ]);

            if (count($forms) > 0) {
                $formResults = [];
                foreach ($forms as $form) {
                    $formResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaFormBundle:SubscribedEvents\Search:global.html.php',
                        ['form' => $form[0]]
                    )->getContent();
                }
                if (count($forms) > 5) {
                    $formResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaFormBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($forms) - 5),
                        ]
                    )->getContent();
                }
                $formResults['count'] = count($forms);
                $event->addResults('autoborna.form.forms', $formResults);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        if ($this->security->isGranted(['form:forms:viewown', 'form:forms:viewother'], 'MATCH_ONE')) {
            $event->addCommands(
                'autoborna.form.forms',
                $this->formModel->getCommandList()
            );
        }
    }
}
