<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Templating\Twig\Extension;

use Autoborna\CoreBundle\Templating\Helper\DateHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateExtension extends AbstractExtension
{
    protected DateHelper $dateHelper;

    public function __construct(DateHelper $dateHelper)
    {
        $this->dateHelper = $dateHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('dateToText', [$this, 'toText'], ['is_safe' => ['all']]),
            new TwigFunction('dateToFull', [$this, 'toFull'], ['is_safe' => ['all']]),
        ];
    }

    /**
     * Returns date/time like Today, 10:00 AM.
     *
     * @param mixed $datetime
     * @param bool  $forceDateForNonText If true, return as full date/time rather than "29 days ago"
     */
    public function toText($datetime, string $timezone = 'local', string $fromFormat = 'Y-m-d H:i:s', bool $forceDateForNonText = false): string
    {
        return $this->dateHelper->toText($datetime, $timezone, $fromFormat, $forceDateForNonText);
    }

    /**
     * Returns full date. eg. October 8, 2014 21:19.
     *
     * @param \DateTime|string $datetime
     */
    public function toFull($datetime, string $timezone = 'local', string $fromFormat = 'Y-m-d H:i:s'): string
    {
        return $this->dateHelper->toFull($datetime, $timezone, $fromFormat);
    }
}
