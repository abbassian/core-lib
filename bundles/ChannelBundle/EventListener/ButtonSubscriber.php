<?php

namespace Autoborna\ChannelBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\CustomButtonEvent;
use Autoborna\CoreBundle\Templating\Helper\ButtonHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ButtonSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router     = $router;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_BUTTONS => ['injectContactBulkButtons', 0],
        ];
    }

    public function injectContactBulkButtons(CustomButtonEvent $event)
    {
        if (0 === strpos($event->getRoute(), 'autoborna_contact_')) {
            $event->addButton(
                [
                    'attr' => [
                        'class'       => 'btn btn-default btn-sm btn-nospin',
                        'data-toggle' => 'ajaxmodal',
                        'data-target' => '#AutobornaSharedModal',
                        'href'        => $this->router->generate('autoborna_channel_batch_contact_view'),
                        'data-header' => $this->translator->trans('autoborna.lead.batch.channels'),
                    ],
                    'btnText'   => $this->translator->trans('autoborna.lead.batch.channels'),
                    'iconClass' => 'fa fa-rss',
                ],
                ButtonHelper::LOCATION_BULK_ACTIONS
            );
        }
    }
}
