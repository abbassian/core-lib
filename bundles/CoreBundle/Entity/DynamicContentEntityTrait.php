<?php

namespace Autoborna\CoreBundle\Entity;

use Autoborna\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

trait DynamicContentEntityTrait
{
    /**
     * Keep the default content set outside of $dynamicContent so that it can be used if $dynamicContent is emptied.
     *
     * @var array
     */
    public static $defaultDynamicContent = [
        [
            'tokenName' => 'Dynamic Content 1',
            'content'   => 'Default Dynamic Content',
            'filters'   => [
                [
                    'content' => null,
                    'filters' => [
                        [
                            'glue'     => null,
                            'field'    => null,
                            'object'   => null,
                            'type'     => null,
                            'operator' => null,
                            'display'  => null,
                            'filter'   => null,
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    private $dynamicContent = [];

    protected static function addDynamicContentMetadata(ClassMetadataBuilder $builder)
    {
        $builder->createField('dynamicContent', 'array')
            ->columnName('dynamic_content')
            ->nullable()
            ->build();
    }

    /**
     * @return array
     */
    public function getDynamicContent()
    {
        return (empty($this->dynamicContent)) ? $this->getDefaultDynamicContent() : $this->dynamicContent;
    }

    /**
     * @param $dynamicContent
     *
     * @return $this
     */
    public function setDynamicContent($dynamicContent)
    {
        if (empty($dynamicContent)) {
            $dynamicContent = $this->getDefaultDynamicContent();
        }

        $this->dynamicContent = $dynamicContent;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultDynamicContent()
    {
        return self::$defaultDynamicContent;
    }
}
