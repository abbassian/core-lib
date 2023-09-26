<?php

namespace Autoborna\ChannelBundle\Controller;

use Autoborna\ChannelBundle\Model\ChannelActionModel;
use Autoborna\ChannelBundle\Model\FrequencyActionModel;
use Autoborna\CoreBundle\Controller\AbstractFormController;
use Autoborna\LeadBundle\Form\Type\ContactChannelsType;
use Autoborna\LeadBundle\Model\LeadModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class BatchContactController extends AbstractFormController
{
    /**
     * @var ChannelActionModel
     */
    private $channelActionModel;

    /**
     * @var FrequencyActionModel
     */
    private $frequencyActionModel;

    /**
     * @var LeadModel
     */
    private $contactModel;

    /**
     * Initialize object props here to simulate constructor
     * and make the future controller refactoring easier.
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->channelActionModel   = $this->container->get('autoborna.channel.model.channel.action');
        $this->frequencyActionModel = $this->container->get('autoborna.channel.model.frequency.action');
        $this->contactModel         = $this->container->get('autoborna.lead.model.lead');
    }

    /**
     * Execute the batch action.
     *
     * @return JsonResponse
     */
    public function setAction()
    {
        $params = $this->request->get('contact_channels', []);
        $ids    = empty($params['ids']) ? [] : json_decode($params['ids']);

        if ($ids && is_array($ids)) {
            $subscribedChannels = isset($params['subscribed_channels']) ? $params['subscribed_channels'] : [];
            $preferredChannel   = isset($params['preferred_channel']) ? $params['preferred_channel'] : null;

            $this->channelActionModel->update($ids, $subscribedChannels);
            $this->frequencyActionModel->update($ids, $params, $preferredChannel);

            $this->addFlash('autoborna.lead.batch_leads_affected', [
                '%count%'     => count($ids),
            ]);
        } else {
            $this->addFlash('autoborna.core.error.ids.missing');
        }

        return new JsonResponse([
            'closeModal' => true,
            'flashes'    => $this->getFlashContent(),
        ]);
    }

    /**
     * View for batch action.
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $route = $this->generateUrl('autoborna_channel_batch_contact_set');

        return $this->delegateView([
            'viewParameters' => [
                'form'         => $this->createForm(ContactChannelsType::class, [], [
                    'action'        => $route,
                    'channels'      => $this->contactModel->getPreferenceChannels(),
                    'public_view'   => false,
                    'save_button'   => true,
                ])->createView(),
            ],
            'contentTemplate' => 'AutobornaLeadBundle:Batch:channel.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_contact_index',
                'autobornaContent' => 'leadBatch',
                'route'         => $route,
            ],
        ]);
    }
}
