<?php

namespace Autoborna\DynamicContentBundle\Helper;

use Autoborna\CampaignBundle\Executioner\RealTimeExecutioner;
use Autoborna\CoreBundle\Event\TokenReplacementEvent;
use Autoborna\DynamicContentBundle\DynamicContentEvents;
use Autoborna\DynamicContentBundle\Entity\DynamicContent;
use Autoborna\DynamicContentBundle\Event\ContactFiltersEvaluateEvent;
use Autoborna\DynamicContentBundle\Model\DynamicContentModel;
use Autoborna\EmailBundle\EventListener\MatchFilterForLeadTrait;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\Tag;
use Autoborna\LeadBundle\Model\LeadModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DynamicContentHelper
{
    use MatchFilterForLeadTrait;

    protected RealTimeExecutioner $realTimeExecutioner;
    protected EventDispatcherInterface $dispatcher;
    protected DynamicContentModel $dynamicContentModel;
    protected LeadModel $leadModel;

    public function __construct(
        DynamicContentModel $dynamicContentModel,
        RealTimeExecutioner $realTimeExecutioner,
        EventDispatcherInterface $dispatcher,
        LeadModel $leadModel
    ) {
        $this->dynamicContentModel = $dynamicContentModel;
        $this->realTimeExecutioner = $realTimeExecutioner;
        $this->dispatcher          = $dispatcher;
        $this->leadModel           = $leadModel;
    }

    /**
     * @param string     $slot
     * @param Lead|array $lead
     *
     * @return string
     */
    public function getDynamicContentForLead($slot, $lead)
    {
        // Attempt campaign slots first
        $dwcActionResponse = $this->realTimeExecutioner->execute('dwc.decision', $slot, 'dynamicContent')->getActionResponses('dwc.push_content');
        if (!empty($dwcActionResponse)) {
            return array_shift($dwcActionResponse);
        }

        // Attempt stored content second
        $data = $this->dynamicContentModel->getSlotContentForLead($slot, $lead);
        if (!empty($data)) {
            $content = $data['content'];
            $dwc     = $this->dynamicContentModel->getEntity($data['id']);
            if ($dwc instanceof DynamicContent) {
                $content = $this->getRealDynamicContent($slot, $lead, $dwc);
            }

            return $content;
        }

        // Finally attempt standalone DWC
        return $this->getDynamicContentSlotForLead($slot, $lead);
    }

    /**
     * @param string     $slotName
     * @param Lead|array $lead
     *
     * @return string
     */
    public function getDynamicContentSlotForLead($slotName, $lead)
    {
        $leadArray = [];
        if ($lead instanceof Lead) {
            $leadArray = $this->convertLeadToArray($lead);
        }

        $dwcs = $this->getDwcsBySlotName($slotName, true);
        /** @var DynamicContent $dwc */
        foreach ($dwcs as $dwc) {
            if ($dwc->getIsCampaignBased()) {
                continue;
            }
            if ($lead && $this->filtersMatchContact($dwc->getFilters(), $leadArray)) {
                return $lead ? $this->getRealDynamicContent($dwc->getSlotName(), $lead, $dwc) : '';
            }
        }

        return '';
    }

    /**
     * @param string     $content
     * @param Lead|array $lead
     *
     * @return string Content with the {content} tokens replaced with dynamic content
     */
    public function replaceTokensInContent($content, $lead)
    {
        // Find all dynamic content tags
        preg_match_all('/{(dynamiccontent)=(\w+)(?:\/}|}(?:([^{]*(?:{(?!\/\1})[^{]*)*){\/\1})?)/is', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $slot           = $match[2];
            $defaultContent = $match[3];

            $dwcContent = $this->getDynamicContentForLead($slot, $lead);

            if (!$dwcContent) {
                $dwcContent = $defaultContent;
            }

            $content = str_replace($matches[0], $dwcContent, $content);
        }

        return $content;
    }

    /**
     * @param string    $content
     * @param Lead|null $lead
     *
     * @return array
     */
    public function findDwcTokens($content, $lead)
    {
        preg_match_all('/{dwc=(.*?)}/', $content, $matches);

        $tokens = [];
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key => $slotName) {
                $token = $matches[0][$key];
                if (!empty($tokens[$token])) {
                    continue;
                }

                $dwcs = $this->getDwcsBySlotName($slotName);

                /** @var DynamicContent $dwc */
                foreach ($dwcs as $dwc) {
                    if ($dwc->getIsCampaignBased()) {
                        continue;
                    }
                    $content                   = $lead ? $this->getRealDynamicContent($dwc->getSlotName(), $lead, $dwc) : '';
                    $tokens[$token]['content'] = $content;
                    $tokens[$token]['filters'] = $dwc->getFilters();
                }
            }

            unset($matches);
        }

        return $tokens;
    }

    /**
     * @param string       $slot
     * @param Lead|mixed[] $lead
     *
     * @return string
     */
    public function getRealDynamicContent($slot, $lead, DynamicContent $dwc)
    {
        $content = $dwc->getContent();
        // Determine a translation based on contact's preferred locale
        /** @var DynamicContent $translation */
        list($ignore, $translation) = $this->dynamicContentModel->getTranslatedEntity($dwc, $lead);
        if ($translation !== $dwc) {
            // Use translated version of content
            $dwc     = $translation;
            $content = $dwc->getContent();
        }
        $this->dynamicContentModel->createStatEntry($dwc, $lead, $slot);

        $tokenEvent = new TokenReplacementEvent($content, $lead, ['slot' => $slot, 'dynamic_content_id' => $dwc->getId()]);
        $this->dispatcher->dispatch(DynamicContentEvents::TOKEN_REPLACEMENT, $tokenEvent);

        return $tokenEvent->getContent();
    }

    /**
     * @param string $slotName
     * @param bool   $publishedOnly
     *
     * @return array|\Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getDwcsBySlotName($slotName, $publishedOnly = false)
    {
        $filter = [
            'where' => [
                [
                    'col'  => 'e.slotName',
                    'expr' => 'eq',
                    'val'  => $slotName,
                ],
            ],
        ];

        if ($publishedOnly) {
            $filter['where'][] = [
                'col'  => 'e.isPublished',
                'expr' => 'eq',
                'val'  => 1,
            ];
        }

        return $this->dynamicContentModel->getEntities(
            [
                'filter'           => $filter,
                'ignore_paginator' => true,
            ]
        );
    }

    /**
     * @param Lead $lead
     *
     * @return array
     */
    public function convertLeadToArray($lead)
    {
        return array_merge(
            $lead->getProfileFields(),
            [
                'tags' => array_map(
                    function (Tag $v) {
                        return $v->getId();
                    },
                    $lead->getTags()->toArray()
                ),
            ]
        );
    }

    /**
     * @param mixed[] $filters
     * @param mixed[] $contactArray
     */
    private function filtersMatchContact(array $filters, array $contactArray): bool
    {
        if (empty($contactArray['id'])) {
            return false;
        }

        //  We attempt even listeners first
        if ($this->dispatcher->hasListeners(DynamicContentEvents::ON_CONTACTS_FILTER_EVALUATE)) {
            /** @var Lead $contact */
            $contact = $this->leadModel->getEntity($contactArray['id']);

            $event = new ContactFiltersEvaluateEvent($filters, $contact);
            $this->dispatcher->dispatch(DynamicContentEvents::ON_CONTACTS_FILTER_EVALUATE, $event);
            if ($event->isMatch()) {
                return true;
            }
        }

        return $this->matchFilterForLead($filters, $contactArray);
    }
}
