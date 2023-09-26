<?php

namespace Autoborna\CoreBundle\Doctrine\Mapping;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder as OrmClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Autoborna\CategoryBundle\Entity\Category;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\LeadBundle\Entity\Lead;

/**
 * Override Doctrine's builder classes to add support to orphanRemoval until the fix is incorporated into Doctrine release
 * See @see https://github.com/doctrine/doctrine2/pull/1326/.
 */
class ClassMetadataBuilder extends OrmClassMetadataBuilder
{
    /**
     * Max length of indexed VARCHAR fields for UTF8MB4 encoding.
     */
    public const MAX_VARCHAR_INDEXED_LENGTH = 191;

    public function __construct(ClassMetadataInfo $cm)
    {
        parent::__construct($cm);

        // Default all Autoborna entities to explicit
        $this->setChangeTrackingPolicyDeferredExplicit();
    }

    /**
     * Creates a ManyToOne Association Builder.
     *
     * Note: This method does not add the association, you have to call build() on the AssociationBuilder.
     *
     * @param string $name
     * @param string $targetEntity
     *
     * @return AssociationBuilder
     */
    public function createManyToOne($name, $targetEntity)
    {
        return new AssociationBuilder(
            $this,
            [
                'fieldName'    => $name,
                'targetEntity' => $targetEntity,
            ],
            ClassMetadata::MANY_TO_ONE
        );
    }

    /**
     * Creates a OneToOne Association Builder.
     *
     * @param string $name
     * @param string $targetEntity
     *
     * @return AssociationBuilder
     */
    public function createOneToOne($name, $targetEntity)
    {
        return new AssociationBuilder(
            $this,
            [
                'fieldName'    => $name,
                'targetEntity' => $targetEntity,
            ],
            ClassMetadata::ONE_TO_ONE
        );
    }

    /**
     * Creates a ManyToMany Association Builder.
     *
     * @param string $name
     * @param string $targetEntity
     *
     * @return ManyToManyAssociationBuilder
     */
    public function createManyToMany($name, $targetEntity)
    {
        return new ManyToManyAssociationBuilder(
            $this,
            [
                'fieldName'    => $name,
                'targetEntity' => $targetEntity,
            ],
            ClassMetadata::MANY_TO_MANY
        );
    }

    /**
     * Creates a one to many association builder.
     *
     * @param string $name
     * @param string $targetEntity
     *
     * @return OneToManyAssociationBuilder
     */
    public function createOneToMany($name, $targetEntity)
    {
        return new OneToManyAssociationBuilder(
            $this,
            [
                'fieldName'    => $name,
                'targetEntity' => $targetEntity,
            ],
            ClassMetadata::ONE_TO_MANY
        );
    }

    /**
     * Add Id column.
     *
     * @return $this
     */
    public function addId()
    {
        $this->createField('id', Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->option('unsigned', true)
            ->build();

        return $this;
    }

    /**
     * Adds autogenerated ID field type of BIGINT UNSIGNED.
     *
     * @param string $name
     * @param string $columnName
     * @param bool   $isPrimary
     * @param bool   $isNullable
     *
     * @return ClassMetadataBuilder
     */
    public function addBigIntIdField($fieldName = 'id', $columnName = 'id', $isPrimary = true, $isNullable = false)
    {
        $cm = $this->getClassMetadata();
        $cm->mapField(
            [
                'fieldName'  => $fieldName,
                'columnName' => $columnName,
                'id'         => $isPrimary,
                'nullable'   => $isNullable,
                'type'       => Types::BIGINT,
                'options'    => [
                    'unsigned' => true,
                ],
            ]
        );

        if ($isPrimary) {
            $cm->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_AUTO);
        }

        return $this;
    }

    /**
     * Add UUID as Id.
     *
     * @return $this
     */
    public function addUuid()
    {
        $this->createField('id', 'guid')
            ->makePrimaryKey()
            ->build();

        return $this;
    }

    /**
     * Add id, name, and description columns.
     *
     * @param string $nameColumn
     * @param string $descriptionColumn
     *
     * @return $this
     */
    public function addIdColumns($nameColumn = 'name', $descriptionColumn = 'description')
    {
        $this->addId();

        if ($nameColumn) {
            $this->createField($nameColumn, Types::STRING)
                ->build();
        }

        if ($descriptionColumn) {
            $this->createField($descriptionColumn, Types::TEXT)
                ->nullable()
                ->build();
        }

        return $this;
    }

    /**
     * Add category to metadata.
     *
     * @return $this
     */
    public function addCategory()
    {
        $this->createManyToOne('category', Category::class)
            ->cascadeMerge()
            ->cascadeDetach()
            ->addJoinColumn('category_id', 'id', true, false, 'SET NULL')
            ->build();

        return $this;
    }

    /**
     * Add publish up and down dates to metadata.
     *
     * @return $this
     */
    public function addPublishDates()
    {
        $this->createField('publishUp', Types::DATETIME_MUTABLE)
            ->columnName('publish_up')
            ->nullable()
            ->build();

        $this->createField('publishDown', Types::DATETIME_MUTABLE)
            ->columnName('publish_down')
            ->nullable()
            ->build();

        return $this;
    }

    /**
     * Added dateAdded column.
     *
     * @param bool|false $nullable
     *
     * @return $this
     */
    public function addDateAdded($nullable = false)
    {
        $dateAdded = $this->createField('dateAdded', Types::DATETIME_MUTABLE)
            ->columnName('date_added');

        if ($nullable) {
            $dateAdded->nullable();
        }

        $dateAdded->build();

        return $this;
    }

    /**
     * Add a contact column.
     *
     * @param bool|false $nullable
     * @param string     $onDelete
     * @param bool|false $isPrimaryKey
     * @param null       $inversedBy
     *
     * @return $this
     */
    public function addContact($nullable = false, $onDelete = 'CASCADE', $isPrimaryKey = false, $inversedBy = null)
    {
        $lead = $this->createManyToOne('contact', Lead::class);

        if ($isPrimaryKey) {
            $lead->makePrimaryKey();
        }

        if ($inversedBy) {
            $lead->inversedBy($inversedBy);
        }

        $lead
            ->addJoinColumn('contact_id', 'id', $nullable, false, $onDelete)
            ->build();

        return $this;
    }

    /**
     * Add a lead column.
     *
     * @param bool|false $nullable
     * @param string     $onDelete
     * @param bool|false $isPrimaryKey
     * @param null       $inversedBy
     *
     * @deprecated Use addContact instead; existing implementations will need a migration to rename lead_id to contact_id
     *
     * @return $this
     */
    public function addLead($nullable = false, $onDelete = 'CASCADE', $isPrimaryKey = false, $inversedBy = null)
    {
        $lead = $this->createManyToOne('lead', Lead::class);

        if ($isPrimaryKey) {
            $lead->makePrimaryKey();
        }

        if ($inversedBy) {
            $lead->inversedBy($inversedBy);
        }

        $lead
            ->addJoinColumn('lead_id', 'id', $nullable, false, $onDelete)
            ->build();

        return $this;
    }

    /**
     * Adds IP address.
     *
     * @param bool $nullable
     *
     * @return $this
     */
    public function addIpAddress($nullable = false)
    {
        $this->createManyToOne('ipAddress', IpAddress::class)
            ->cascadePersist()
            ->cascadeMerge()
            ->cascadeDetach()
            ->addJoinColumn('ip_id', 'id', $nullable)
            ->build();

        return $this;
    }

    /**
     * Add a nullable field.
     *
     * @param string      $name
     * @param string      $type
     * @param string|null $columnName
     *
     * @return $this
     */
    public function addNullableField($name, $type = Types::STRING, $columnName = null)
    {
        $field = $this->createField($name, $type)
            ->nullable();

        if (null !== $columnName) {
            $field->columnName($columnName);
        }

        if ($this->isIndexedVarchar($columnName ?? $name, $type)) {
            $field->length(self::MAX_VARCHAR_INDEXED_LENGTH);
        }

        $field->build();

        return $this;
    }

    /**
     * Add a field with a custom column name.
     *
     * @param      $name
     * @param      $type
     * @param      $columnName
     * @param bool $nullable
     *
     * @return $this
     */
    public function addNamedField($name, $type, $columnName, $nullable = false)
    {
        $field = $this->createField($name, $type)
            ->columnName($columnName);

        if ($nullable) {
            $field->nullable();
        }

        if ($this->isIndexedVarchar($columnName ?? $name, $type)) {
            $field->length(self::MAX_VARCHAR_INDEXED_LENGTH);
        }

        $field->build();

        return $this;
    }

    /**
     * Adds Field. Overridden for IDE suggestions when stringing methods in entity class.
     *
     * @param string $name
     * @param string $type
     *
     * @return $this
     */
    public function addField($name, $type, array $mapping = [])
    {
        if ($this->isIndexedVarchar($name, $type)) {
            $mapping['length'] = self::MAX_VARCHAR_INDEXED_LENGTH;
        }

        return parent::addField($name, $type, $mapping);
    }

    public function createField($name, $type)
    {
        $mapping = [
            'fieldName' => $name,
            'type'      => $type,
        ];

        if ($this->isIndexedVarchar($name, $type)) {
            $mapping['length'] = self::MAX_VARCHAR_INDEXED_LENGTH;
        }

        return new FieldBuilder($this, $mapping);
    }

    /**
     * @param string  $name
     * @param mixed[] $flags
     * @param mixed[] $options
     */
    public function addIndex(array $columns, $name, array $flags = null, array $options = null): self
    {
        $cm = $this->getClassMetadata();

        if (!isset($cm->table['indexes'])) {
            $cm->table['indexes'] = [];
        }

        $definition = ['columns' => $columns];

        if (null !== $flags) {
            $definition['flags'] = $flags;
        }

        if (null !== $options) {
            $definition['options'] = $options;
        }

        $cm->table['indexes'][$name] = $definition;

        return $this;
    }

    /**
     * @deprecated this method will be removed as MySQL does not support partial indices whatsoever
     *
     * @param string $name
     * @param string $where
     *
     * @return self
     */
    public function addPartialIndex(array $columns, $name, $where)
    {
        return $this->addIndex($columns, $name, null, ['where' => $where]);
    }

    /**
     * @param mixed[] $columns
     */
    public function addFulltextIndex(array $columns, string $name): self
    {
        return $this->addIndex($columns, $name, ['fulltext']);
    }

    /**
     * UTF8MB4 encoding needs max length of 191 instead of 255 that UTF8 needed. Doctrine does not take care of it by itself.
     */
    public function isIndexedVarchar(string $name, string $type): bool
    {
        return Types::STRING === $type || isset($this->getClassMetadata()->table['indexes'][$name]);
    }

    /**
     * Adds Index with options.
     *
     * @param list<string>         $columns
     * @param array<string, mixed> $options
     */
    public function addIndexWithOptions(array $columns, string $name, array $options): ClassMetadataBuilder
    {
        $cm = $this->getClassMetadata();

        $cm->table['indexes'][$name] = [
            'columns' => $columns,
            'options' => $options,
        ];

        return $this;
    }
}
