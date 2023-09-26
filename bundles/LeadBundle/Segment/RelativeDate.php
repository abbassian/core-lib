<?php

namespace Autoborna\LeadBundle\Segment;

use Symfony\Component\Translation\TranslatorInterface;

class RelativeDate
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getRelativeDateStrings()
    {
        $keys = $this->getRelativeDateTranslationKeys();

        $strings = [];
        foreach ($keys as $key) {
            $strings[$key] = $this->translator->trans($key);
        }

        return $strings;
    }

    /**
     * @return array
     */
    private function getRelativeDateTranslationKeys()
    {
        return [
            'autoborna.lead.list.month_last',
            'autoborna.lead.list.month_next',
            'autoborna.lead.list.month_this',
            'autoborna.lead.list.today',
            'autoborna.lead.list.tomorrow',
            'autoborna.lead.list.yesterday',
            'autoborna.lead.list.week_last',
            'autoborna.lead.list.week_next',
            'autoborna.lead.list.week_this',
            'autoborna.lead.list.year_last',
            'autoborna.lead.list.year_next',
            'autoborna.lead.list.year_this',
            'autoborna.lead.list.anniversary',
        ];
    }
}
