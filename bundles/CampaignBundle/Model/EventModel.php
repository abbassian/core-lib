<?php

namespace Autoborna\CampaignBundle\Model;

use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Entity\LeadEventLogRepository;
use Autoborna\CoreBundle\Helper\Chart\ChartQuery;
use Autoborna\CoreBundle\Helper\Chart\LineChart;
use Autoborna\CoreBundle\Model\FormModel;

class EventModel extends FormModel
{
    /**
     * @return \Autoborna\CampaignBundle\Entity\EventRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Event::class);
    }

    /**
     * @return \Autoborna\CampaignBundle\Entity\CampaignRepository
     */
    public function getCampaignRepository()
    {
        return $this->em->getRepository(Campaign::class);
    }

    /**
     * @return LeadEventLogRepository
     */
    public function getLeadEventLogRepository()
    {
        return $this->em->getRepository(LeadEventLog::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getPermissionBase()
    {
        return 'campaign:campaigns';
    }

    /**
     * Get a specific entity or generate a new one if id is empty.
     *
     * @param $id
     *
     * @return object|null
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new Event();
        }

        return parent::getEntity($id);
    }

    /**
     * @param $currentEvents
     * @param $deletedEvents
     */
    public function deleteEvents($currentEvents, $deletedEvents)
    {
        $deletedKeys = [];
        foreach ($deletedEvents as $k => $deleteMe) {
            if ($deleteMe instanceof Event) {
                $deleteMe = $deleteMe->getId();
            }

            if (0 === strpos($deleteMe, 'new')) {
                unset($deletedEvents[$k]);
            }

            if (isset($currentEvents[$deleteMe])) {
                unset($deletedEvents[$k]);
            }

            if (isset($deletedEvents[$k])) {
                $deletedKeys[] = $deleteMe;
            }
        }

        if (count($deletedEvents)) {
            // wipe out any references to these events to prevent restraint violations
            $this->getRepository()->nullEventRelationships($deletedKeys);

            foreach ($deletedEvents as $eventToDelete) {
                // delete the events
                $this->getLeadEventLogRepository()->removeEventLogs($eventToDelete);
                $this->deleteEntities([$eventToDelete]);
            }
        }
    }

    /**
     * Get line chart data of campaign events.
     *
     * @param string $unit          {@link php.net/manual/en/function.date.php#refsect1-function.date-parameters}
     * @param string $dateFormat
     * @param array  $filter
     * @param bool   $canViewOthers
     *
     * @return array
     */
    public function getEventLineChartData($unit, \DateTime $dateFrom, \DateTime $dateTo, $dateFormat = null, $filter = [], $canViewOthers = true)
    {
        $chart = new LineChart($unit, $dateFrom, $dateTo, $dateFormat);
        $query = new ChartQuery($this->em->getConnection(), $dateFrom, $dateTo);
        $q     = $query->prepareTimeDataQuery('campaign_lead_event_log', 'date_triggered', $filter);

        if (!$canViewOthers) {
            $q->join('t', MAUTIC_TABLE_PREFIX.'campaigns', 'c', 'c.id = t.campaign_id')
                ->andWhere('c.created_by = :userId')
                ->setParameter('userId', $this->userHelper->getUser()->getId());
        }

        $data = $query->loadAndBuildTimeData($q);
        $chart->setDataset($this->translator->trans('autoborna.campaign.triggered.events'), $data);

        return $chart->render();
    }
}
