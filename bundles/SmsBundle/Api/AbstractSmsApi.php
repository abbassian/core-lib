<?php

namespace Autoborna\SmsBundle\Api;

use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\PageBundle\Model\TrackableModel;
use Autoborna\SmsBundle\Sms\TransportInterface;

/**
 * Class AbstractSmsApi.
 *
 * @deprecated use TransportInterface instead
 */
abstract class AbstractSmsApi implements TransportInterface
{
    /**
     * @var TrackableModel
     */
    protected $pageTrackableModel;

    /**
     * AbstractSmsApi constructor.
     */
    public function __construct(TrackableModel $pageTrackableModel)
    {
        $this->pageTrackableModel = $pageTrackableModel;
    }

    /**
     * @param string $content
     *
     * @return mixed
     */
    abstract public function sendSms(Lead $lead, $content);

    /**
     * Convert a non-tracked url to a tracked url.
     *
     * @param string $url
     *
     * @return string
     */
    public function convertToTrackedUrl($url, array $clickthrough = [])
    {
        /* @var \Autoborna\PageBundle\Entity\Redirect $redirect */
        $trackable = $this->pageTrackableModel->getTrackableByUrl($url, 'sms', $clickthrough['sms']);

        return $this->pageTrackableModel->generateTrackableUrl($trackable, $clickthrough, true);
    }
}
