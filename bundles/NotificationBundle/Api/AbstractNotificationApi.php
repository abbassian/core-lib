<?php

namespace Autoborna\NotificationBundle\Api;

use GuzzleHttp\Client;
use Autoborna\NotificationBundle\Entity\Notification;
use Autoborna\PageBundle\Model\TrackableModel;
use Autoborna\PluginBundle\Helper\IntegrationHelper;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractNotificationApi
{
    protected Client $http;
    protected TrackableModel $trackableModel;
    protected IntegrationHelper $integrationHelper;

    /**
     * AbstractNotificationApi constructor.
     */
    public function __construct(Client $http, TrackableModel $trackableModel, IntegrationHelper $integrationHelper)
    {
        $this->http              = $http;
        $this->trackableModel    = $trackableModel;
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * @param string $endpoint One of "apps", "players", or "notifications"
     * @param array  $data     Array of data to send
     */
    abstract public function send(string $endpoint, array $data): ResponseInterface;

    /**
     * @param $id
     *
     * @return mixed
     */
    abstract public function sendNotification($id, Notification $notification);

    /**
     * Convert a non-tracked url to a tracked url.
     *
     * @param string $url
     *
     * @return string
     */
    public function convertToTrackedUrl($url, array $clickthrough, Notification $notification)
    {
        /* @var \Autoborna\PageBundle\Entity\Redirect $redirect */
        $trackable = $this->trackableModel->getTrackableByUrl($url, 'notification', $clickthrough['notification']);

        return $this->trackableModel->generateTrackableUrl($trackable, $clickthrough, [], $notification->getUtmTags());
    }
}
