<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\CustomTemplateEvent;
use Autoborna\IntegrationsBundle\Entity\ObjectMappingRepository;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\LeadBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UIContactIntegrationsTabSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectMappingRepository
     */
    private $objectMappingRepository;

    public function __construct(ObjectMappingRepository $objectMappingRepository)
    {
        $this->objectMappingRepository = $objectMappingRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_TEMPLATE => ['onTemplateRender', 0],
        ];
    }

    public function onTemplateRender(CustomTemplateEvent $event): void
    {
        if ('AutobornaLeadBundle:Lead:lead.html.php' === $event->getTemplate()) {
            $vars         = $event->getVars();
            $integrations = $vars['integrations'];

            /** @var Lead $contact */
            $contact = $vars['lead'];

            $objectMappings = $this->objectMappingRepository->getIntegrationMappingsForInternalObject(
                Contact::NAME,
                (int) $contact->getId()
            );

            foreach ($objectMappings as $objectMapping) {
                $integrations[] = [
                    'integration'           => $objectMapping->getIntegration(),
                    'integration_entity'    => $objectMapping->getIntegrationObjectName(),
                    'integration_entity_id' => $objectMapping->getIntegrationObjectId(),
                    'date_added'            => $objectMapping->getDateCreated(),
                    'last_sync_date'        => $objectMapping->getLastSyncDate(),
                ];
            }

            $vars['integrations'] = $integrations;

            $event->setVars($vars);
        }
    }
}
