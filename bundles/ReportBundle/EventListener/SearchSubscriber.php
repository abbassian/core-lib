<?php

namespace Autoborna\ReportBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\ReportBundle\Model\ReportModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var ReportModel
     */
    private $reportModel;

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
        ReportModel $reportModel,
        CorePermissions $security,
        TemplatingHelper $templating
    ) {
        $this->userHelper  = $userHelper;
        $this->reportModel = $reportModel;
        $this->security    = $security;
        $this->templating  = $templating;
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

        $filter = ['string' => $str, 'force' => []];

        $permissions = $this->security->isGranted(
            ['report:reports:viewown', 'report:reports:viewother'],
            'RETURN_ARRAY'
        );
        if ($permissions['report:reports:viewown'] || $permissions['report:reports:viewother']) {
            if (!$permissions['report:reports:viewother']) {
                $filter['force'][] = [
                    'column' => 'IDENTITY(r.createdBy)',
                    'expr'   => 'eq',
                    'value'  => $this->userHelper->getUser()->getId(),
                ];
            }

            $items = $this->reportModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $filter,
                ]);

            $count = count($items);
            if ($count > 0) {
                $results = [];

                foreach ($items as $item) {
                    $results[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaReportBundle:SubscribedEvents\Search:global.html.php',
                        ['item' => $item]
                    )->getContent();
                }
                if ($count > 5) {
                    $results[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaReportBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => ($count - 5),
                        ]
                    )->getContent();
                }
                $results['count'] = $count;
                $event->addResults('autoborna.report.reports', $results);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        if ($this->security->isGranted(['report:reports:viewown', 'report:reports:viewother'], 'MATCH_ONE')) {
            $event->addCommands(
                'autoborna.report.reports',
                $this->reportModel->getCommandList()
            );
        }
    }
}
