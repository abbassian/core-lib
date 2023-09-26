<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Doctrine\GeneratedColumn\GeneratedColumn;
use Autoborna\CoreBundle\Event\GeneratedColumnsEvent;
use Autoborna\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Model\ListModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class GeneratedColumnSubscriber implements EventSubscriberInterface
{
    private ListModel $segmentModel;
    private TranslatorInterface $translator;

    public function __construct(ListModel $segmentModel, TranslatorInterface $translator)
    {
        $this->segmentModel = $segmentModel;
        $this->translator   = $translator;
    }

    /**
     * @return array<string,array<int|string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::ON_GENERATED_COLUMNS_BUILD       => ['onGeneratedColumnsBuild', 0],
            LeadEvents::LIST_FILTERS_CHOICES_ON_GENERATE => ['onGenerateSegmentFilters', 0],
        ];
    }

    public function onGeneratedColumnsBuild(GeneratedColumnsEvent $event): void
    {
        $emailDomain = new GeneratedColumn(
            'leads',
            'generated_email_domain',
            'VARCHAR(255)',
            'SUBSTRING(email, LOCATE("@", email) + 1)'
        );

        $event->addGeneratedColumn($emailDomain);
    }

    public function onGenerateSegmentFilters(LeadListFiltersChoicesEvent $event): void
    {
        $event->addChoice('lead', 'generated_email_domain', [
            'label'      => $this->translator->trans('autoborna.email.segment.choice.generated_email_domain'),
            'properties' => ['type' => 'text'],
            'operators'  => $this->segmentModel->getOperatorsForFieldType(
                [
                    'include' => [
                        '=',
                        '!=',
                        'empty',
                        '!empty',
                        'like',
                        '!like',
                        'regexp',
                        '!regexp',
                        'startsWith',
                        'endsWith',
                        'contains',
                    ],
                ]
            ),
            'object' => 'lead',
        ]);
    }
}
