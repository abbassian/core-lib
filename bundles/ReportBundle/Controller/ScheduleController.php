<?php

namespace Autoborna\ReportBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Service\FlashBag;
use Autoborna\ReportBundle\Scheduler\Date\DateBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends CommonAjaxController
{
    public function indexAction($isScheduled, $scheduleUnit, $scheduleDay, $scheduleMonthFrequency)
    {
        /** @var DateBuilder $dateBuilder */
        $dateBuilder = $this->container->get('autoborna.report.model.scheduler_date_builder');
        $dates       = $dateBuilder->getPreviewDays($isScheduled, $scheduleUnit, $scheduleDay, $scheduleMonthFrequency);

        $html = $this->render(
            'AutobornaReportBundle:Schedule:index.html.php',
            [
                'dates' => $dates,
            ]
        )->getContent();

        return $this->sendJsonResponse(
            [
                'html' => $html,
            ]
        );
    }

    /**
     * Sets report to schedule NOW if possible.
     *
     * @param int $reportId
     *
     * @return JsonResponse
     */
    public function nowAction($reportId)
    {
        /** @var \Autoborna\ReportBundle\Model\ReportModel $model */
        $model = $this->getModel('report');

        /** @var \Autoborna\ReportBundle\Entity\Report $report */
        $report = $model->getEntity($reportId);

        /** @var \Autoborna\CoreBundle\Security\Permissions\CorePermissions $security */
        $security = $this->container->get('autoborna.security');

        if (empty($report)) {
            $this->addFlash('autoborna.report.notfound', ['%id%' => $reportId], FlashBag::LEVEL_ERROR, 'messages');

            return $this->flushFlash(Response::HTTP_NOT_FOUND);
        }

        if (!$security->hasEntityAccess('report:reports:viewown', 'report:reports:viewother', $report->getCreatedBy())) {
            $this->addFlash('autoborna.core.error.accessdenied', [], FlashBag::LEVEL_ERROR);

            return $this->flushFlash(Response::HTTP_FORBIDDEN);
        }

        if ($report->isScheduled()) {
            $this->addFlash('autoborna.report.scheduled.already', ['%id%' => $reportId], FlashBag::LEVEL_ERROR);

            return $this->flushFlash(Response::HTTP_PROCESSING);
        }

        $report->setAsScheduledNow($this->user->getEmail());
        $model->saveEntity($report);

        $this->addFlash(
            'autoborna.report.scheduled.to.now',
            ['%id%' => $reportId, '%email%' => $this->user->getEmail()]
        );

        return $this->flushFlash(Response::HTTP_OK);
    }

    /**
     * @param string $status
     *
     * @return JsonResponse
     */
    private function flushFlash($status)
    {
        return new JsonResponse(['flashes' => $this->getFlashContent()]);
    }
}
