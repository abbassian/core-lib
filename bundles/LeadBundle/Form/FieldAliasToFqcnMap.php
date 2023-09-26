<?php

namespace Autoborna\LeadBundle\Form;

use Autoborna\CoreBundle\Form\Type\BooleanType;
use Autoborna\CoreBundle\Form\Type\CountryType;
use Autoborna\CoreBundle\Form\Type\LocaleType;
use Autoborna\CoreBundle\Form\Type\LookupType;
use Autoborna\CoreBundle\Form\Type\MultiselectType;
use Autoborna\CoreBundle\Form\Type\RegionType;
use Autoborna\CoreBundle\Form\Type\SelectType;
use Autoborna\CoreBundle\Form\Type\TelType;
use Autoborna\CoreBundle\Form\Type\TimezoneType;
use Autoborna\LeadBundle\Exception\FieldNotFoundException;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Provides map between Autoborna 2 (Symfony 2.8) form aliases and Autoborna 3 (Symfony 3.4) FQCN.
 */
final class FieldAliasToFqcnMap
{
    /**
     * @format [field alias => field FQCN]
     */
    public const MAP = [
        'boolean'     => BooleanType::class,
        'country'     => CountryType::class,
        'date'        => DateType::class,
        'datetime'    => DateTimeType::class,
        'email'       => EmailType::class,
        'hidden'      => HiddenType::class,
        'locale'      => LocaleType::class,
        'lookup'      => LookupType::class,
        'multiselect' => MultiselectType::class,
        'number'      => NumberType::class,
        'region'      => RegionType::class,
        'select'      => SelectType::class,
        'tel'         => TelType::class,
        'text'        => TextType::class,
        'textarea'    => TextareaType::class,
        'time'        => TimeType::class,
        'timezone'    => TimezoneType::class,
        'url'         => UrlType::class,
    ];

    public static function getFqcn(string $alias): string
    {
        if (array_key_exists($alias, self::MAP)) {
            return self::MAP[$alias];
        }

        throw new FieldNotFoundException("Field with alias {$alias} not found");
    }
}
