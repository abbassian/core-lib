<?php

namespace Autoborna\PageBundle\EventListener;

use Autoborna\PageBundle\Event as Events;
use Autoborna\PageBundle\Form\Type\PointActionPageHitType;
use Autoborna\PageBundle\Form\Type\PointActionUrlHitType;
use Autoborna\PageBundle\Helper\PointActionHelper;
use Autoborna\PageBundle\PageEvents;
use Autoborna\PointBundle\Event\PointBuilderEvent;
use Autoborna\PointBundle\Model\PointModel;
use Autoborna\PointBundle\PointEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PointSubscriber implements EventSubscriberInterface
{
    /**
     * @var PointModel
     */
    private $pointModel;

    public function __construct(PointModel $pointModel)
    {
        $this->pointModel = $pointModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PointEvents::POINT_ON_BUILD => ['onPointBuild', 0],
            PageEvents::PAGE_ON_HIT     => ['onPageHit', 0],
        ];
    }

    public function onPointBuild(PointBuilderEvent $event)
    {
        $action = [
            'group'       => 'autoborna.page.point.action',
            'label'       => 'autoborna.page.point.action.pagehit',
            'description' => 'autoborna.page.point.action.pagehit_descr',
            'callback'    => [PointActionHelper::class, 'validatePageHit'],
            'formType'    => PointActionPageHitType::class,
        ];

        $event->addAction('page.hit', $action);

        $action = [
            'group'       => 'autoborna.page.point.action',
            'label'       => 'autoborna.page.point.action.urlhit',
            'description' => 'autoborna.page.point.action.urlhit_descr',
            'callback'    => [PointActionHelper::class, 'validateUrlHit'],
            'formType'    => PointActionUrlHitType::class,
            'formTheme'   => 'AutobornaPageBundle:FormTheme\Point',
        ];

        $event->addAction('url.hit', $action);
    }

    /**
     * Trigger point actions for page hits.
     */
    public function onPageHit(Events\PageHitEvent $event)
    {
        if ($event->getPage()) {
            // Autoborna Landing Page was hit
            $this->pointModel->triggerAction('page.hit', $event->getHit());
        } else {
            // Autoborna Tracking Pixel was hit
            $this->pointModel->triggerAction('url.hit', $event->getHit());
        }
    }
}
