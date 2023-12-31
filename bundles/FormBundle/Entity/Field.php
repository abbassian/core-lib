<?php

namespace Autoborna\FormBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Autoborna\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Autoborna\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\FormBundle\ProgressiveProfiling\DisplayManager;
use Autoborna\LeadBundle\Entity\Lead;

class Field
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $showLabel = true;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $isCustom = false;

    /**
     * @var array
     */
    private $customParameters = [];

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var bool
     */
    private $isRequired = false;

    /**
     * @var string
     */
    private $validationMessage;

    /**
     * @var string
     */
    private $helpMessage;

    /**
     * @var int
     */
    private $order = 0;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $validation = [];

    /**
     * @var array<string,mixed>
     */
    private $conditions = [];

    /**
     * @var Form
     */
    private $form;

    /**
     * @var string
     */
    private $labelAttributes;

    /**
     * @var string
     */
    private $inputAttributes;

    /**
     * @var string
     */
    private $containerAttributes;

    /**
     * @var string
     */
    private $leadField;

    /**
     * @var bool
     */
    private $saveResult = true;

    /**
     * @var bool
     */
    private $isAutoFill = false;

    /**
     * @var array
     */
    private $changes;

    private $sessionId;

    /**
     * @var bool
     */
    private $showWhenValueExists;

    /**
     * @var int
     */
    private $showAfterXSubmissions;

    /**
     * @var bool
     */
    private $alwaysDisplay;

    /**
     * @var string
     */
    private $parent;

    /**
     * Reset properties on clone.
     */
    public function __clone()
    {
        $this->id   = null;
        $this->form = null;
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('form_fields')
            ->setCustomRepositoryClass('Autoborna\FormBundle\Entity\FieldRepository')
            ->addIndex(['type'], 'form_field_type_search');

        $builder->addId();

        $builder->addField('label', 'text');

        $builder->createField('showLabel', 'boolean')
            ->columnName('show_label')
            ->nullable()
            ->build();

        $builder->addField('alias', 'string');

        $builder->addField('type', 'string');

        $builder->createField('isCustom', 'boolean')
            ->columnName('is_custom')
            ->build();

        $builder->createField('customParameters', 'array')
            ->columnName('custom_parameters')
            ->nullable()
            ->build();

        $builder->createField('defaultValue', 'text')
            ->columnName('default_value')
            ->nullable()
            ->build();

        $builder->createField('isRequired', 'boolean')
            ->columnName('is_required')
            ->build();

        $builder->createField('validationMessage', 'text')
            ->columnName('validation_message')
            ->nullable()
            ->build();

        $builder->createField('helpMessage', 'text')
            ->columnName('help_message')
            ->nullable()
            ->build();

        $builder->createField('order', 'integer')
            ->columnName('field_order')
            ->nullable()
            ->build();

        $builder->createField('properties', 'array')
            ->nullable()
            ->build();

        $builder->createField('validation', 'json_array')
            ->nullable()
            ->build();

        $builder->addNullableField('parent', 'string', 'parent_id');
        $builder->addNullableField('conditions', 'json_array');

        $builder->createManyToOne('form', 'Form')
            ->inversedBy('fields')
            ->addJoinColumn('form_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->addNullableField('labelAttributes', 'string', 'label_attr');

        $builder->addNullableField('inputAttributes', 'string', 'input_attr');

        $builder->addNullableField('containerAttributes', 'string', 'container_attr');

        $builder->addNullableField('leadField', 'string', 'lead_field');

        $builder->addNullableField('saveResult', 'boolean', 'save_result');

        $builder->addNullableField('isAutoFill', 'boolean', 'is_auto_fill');

        $builder->addNullableField('showWhenValueExists', 'boolean', 'show_when_value_exists');

        $builder->addNullableField('showAfterXSubmissions', 'integer', 'show_after_x_submissions');

        $builder->addNullableField('alwaysDisplay', Types::BOOLEAN, 'always_display');
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('form')
            ->addProperties(
                [
                    'id',
                    'label',
                    'showLabel',
                    'alias',
                    'type',
                    'defaultValue',
                    'isRequired',
                    'validationMessage',
                    'helpMessage',
                    'order',
                    'properties',
                    'validation',
                    'parent',
                    'conditions',
                    'labelAttributes',
                    'inputAttributes',
                    'containerAttributes',
                    'leadField',
                    'saveResult',
                    'isAutoFill',
                ]
            )
            ->build();
    }

    /**
     * @param $prop
     * @param $val
     */
    private function isChanged($prop, $val)
    {
        if ($this->$prop != $val) {
            $this->changes[$prop] = [$this->$prop, $val];
        }
    }

    /**
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return Field
     */
    public function setLabel($label)
    {
        $this->isChanged('label', $label);
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return Field
     */
    public function setAlias($alias)
    {
        $this->isChanged('alias', $alias);
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Field
     */
    public function setType($type)
    {
        $this->isChanged('type', $type);
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set defaultValue.
     *
     * @param string $defaultValue
     *
     * @return Field
     */
    public function setDefaultValue($defaultValue)
    {
        $this->isChanged('defaultValue', $defaultValue);
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Get defaultValue.
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set isRequired.
     *
     * @param bool $isRequired
     *
     * @return Field
     */
    public function setIsRequired($isRequired)
    {
        $this->isChanged('isRequired', $isRequired);
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired.
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Proxy function to getIsRequired.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->getIsRequired();
    }

    /**
     * Set order.
     *
     * @param int $order
     *
     * @return Field
     */
    public function setOrder($order)
    {
        $this->isChanged('order', $order);
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set properties.
     *
     * @param array $properties
     *
     * @return Field
     */
    public function setProperties($properties)
    {
        $this->isChanged('properties', $properties);
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set validation.
     *
     * @param array $validation
     *
     * @return Field
     */
    public function setValidation($validation)
    {
        $this->isChanged('validation', $validation);
        $this->validation = $validation;

        return $this;
    }

    /**
     * Get validation.
     *
     * @return array
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Set validationMessage.
     *
     * @param string $validationMessage
     *
     * @return Field
     */
    public function setValidationMessage($validationMessage)
    {
        $this->isChanged('validationMessage', $validationMessage);
        $this->validationMessage = $validationMessage;

        return $this;
    }

    /**
     * Get validationMessage.
     *
     * @return string
     */
    public function getValidationMessage()
    {
        return $this->validationMessage;
    }

    /**
     * Set form.
     *
     * @return Field
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set labelAttributes.
     *
     * @param string $labelAttributes
     *
     * @return Field
     */
    public function setLabelAttributes($labelAttributes)
    {
        $this->isChanged('labelAttributes', $labelAttributes);
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    /**
     * Get labelAttributes.
     *
     * @return string
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Set inputAttributes.
     *
     * @param string $inputAttributes
     *
     * @return Field
     */
    public function setInputAttributes($inputAttributes)
    {
        $this->isChanged('inputAttributes', $inputAttributes);
        $this->inputAttributes = $inputAttributes;

        return $this;
    }

    /**
     * Get inputAttributes.
     *
     * @return string
     */
    public function getInputAttributes()
    {
        return $this->inputAttributes;
    }

    /**
     * @return mixed
     */
    public function getContainerAttributes()
    {
        return $this->containerAttributes;
    }

    /**
     * @param $containerAttributes
     *
     * @return $this
     */
    public function setContainerAttributes($containerAttributes)
    {
        $this->containerAttributes = $containerAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function convertToArray()
    {
        return get_object_vars($this);
    }

    /**
     * Set showLabel.
     *
     * @param bool $showLabel
     *
     * @return Field
     */
    public function setShowLabel($showLabel)
    {
        $this->isChanged('showLabel', $showLabel);
        $this->showLabel = $showLabel;

        return $this;
    }

    /**
     * Get showLabel.
     *
     * @return bool
     */
    public function getShowLabel()
    {
        return $this->showLabel;
    }

    /**
     * Proxy function to getShowLabel().
     *
     * @return bool
     */
    public function showLabel()
    {
        return $this->getShowLabel();
    }

    /**
     * Set helpMessage.
     *
     * @param string $helpMessage
     *
     * @return Field
     */
    public function setHelpMessage($helpMessage)
    {
        $this->isChanged('helpMessage', $helpMessage);
        $this->helpMessage = $helpMessage;

        return $this;
    }

    /**
     * Get helpMessage.
     *
     * @return string
     */
    public function getHelpMessage()
    {
        return $this->helpMessage;
    }

    /**
     * Set isCustom.
     *
     * @param bool $isCustom
     *
     * @return Field
     */
    public function setIsCustom($isCustom)
    {
        $this->isCustom = $isCustom;

        return $this;
    }

    /**
     * Get isCustom.
     *
     * @return bool
     */
    public function getIsCustom()
    {
        return $this->isCustom;
    }

    /**
     * Proxy function to getIsCustom().
     *
     * @return bool
     */
    public function isCustom()
    {
        return $this->getIsCustom();
    }

    /**
     * Set customParameters.
     *
     * @param array $customParameters
     *
     * @return Field
     */
    public function setCustomParameters($customParameters)
    {
        $this->customParameters = $customParameters;

        return $this;
    }

    /**
     * Get customParameters.
     *
     * @return array
     */
    public function getCustomParameters()
    {
        return $this->customParameters;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    public function getLeadField()
    {
        return $this->leadField;
    }

    /**
     * @param mixed $leadField
     */
    public function setLeadField($leadField)
    {
        $this->leadField = $leadField;
    }

    /**
     * @return mixed
     */
    public function getSaveResult()
    {
        return $this->saveResult;
    }

    /**
     * @param mixed $saveResult
     */
    public function setSaveResult($saveResult)
    {
        $this->saveResult = $saveResult;
    }

    /**
     * @return bool
     */
    public function getIsAutoFill()
    {
        return $this->isAutoFill;
    }

    /**
     * @param mixed $isAutoFill
     */
    public function setIsAutoFill($isAutoFill)
    {
        $this->isAutoFill = $isAutoFill;
    }

    /**
     * @return bool
     */
    public function getShowWhenValueExists()
    {
        return $this->showWhenValueExists;
    }

    /**
     * @param bool $showWhenValueExists
     */
    public function setShowWhenValueExists($showWhenValueExists)
    {
        $this->showWhenValueExists = $showWhenValueExists;
    }

    /**
     * @return int
     */
    public function getShowAfterXSubmissions()
    {
        return $this->showAfterXSubmissions;
    }

    /**
     * @param int $showAfterXSubmissions
     */
    public function setShowAfterXSubmissions($showAfterXSubmissions)
    {
        $this->showAfterXSubmissions = $showAfterXSubmissions;
    }

    /**
     * Decide if the field should be displayed based on thr progressive profiling conditions.
     *
     * @param array|null $submissions
     * @param Lead       $lead
     * @param Form       $form
     *
     * @return bool
     */
    public function showForContact($submissions = null, Lead $lead = null, Form $form = null, DisplayManager $displayManager = null)
    {
        // Always show in the kiosk mode
        if (null !== $form && true === $form->getInKioskMode()) {
            return true;
        }

        // Hide the field if there is the submission count limit and hide it until the limit is overcame
        if (!$this->alwaysDisplay && $this->showAfterXSubmissions > 0 && null !== $submissions && $this->showAfterXSubmissions > count($submissions)) {
            return false;
        }

        if (!$this->alwaysDisplay && false === $this->showWhenValueExists) {
            // Hide the field if there is the value condition and if we already know the value for this field
            if ($submissions) {
                foreach ($submissions as $submission) {
                    if (!empty($submission[$this->alias]) && !$this->isAutoFill) {
                        return false;
                    }
                }
            }

            // Hide the field if the value is already known from the lead profile
            if (null !== $lead && $this->leadField && !empty($lead->getFieldValue($this->leadField)) && !$this->isAutoFill) {
                return false;
            }
        }

        if ($displayManager && $displayManager->useProgressiveProfilingLimit()) {
            if (!$displayManager->showForField($this)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Was field displayed.
     *
     * @param mixed[] $data
     *
     * @return bool
     */
    public function showForConditionalField(array $data)
    {
        if (!$parentField = $this->findParentFieldInForm()) {
            return true;
        }

        if (!isset($data[$parentField->getAlias()])) {
            return false;
        }

        $sendValues = $data[$parentField->getAlias()];
        if (!is_array($sendValues)) {
            $sendValues = [$sendValues];
        }

        foreach ($sendValues as $value) {
            // any value
            if ('' !== $value && !empty($this->conditions['any'])) {
                return true;
            }

            if ('notIn' === $this->conditions['expr']) {
                // value not matched
                if ('' !== $value && !in_array(InputHelper::string($value), $this->conditions['values'])) {
                    return true;
                }
            } elseif (in_array(InputHelper::string($value), $this->conditions['values'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCaptchaType()
    {
        return 'captcha' === $this->type;
    }

    /**
     * @return bool
     */
    public function isFileType()
    {
        return 'file' === $this->type;
    }

    /**
     * @return bool
     */
    public function isAlwaysDisplay()
    {
        return $this->alwaysDisplay;
    }

    /**
     * @param bool $alwaysDisplay
     */
    public function setAlwaysDisplay($alwaysDisplay)
    {
        $this->alwaysDisplay = $alwaysDisplay;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param array<string, mixed> $conditions
     *
     * @return Field
     */
    public function setConditions($conditions)
    {
        $this->isChanged('conditions', $conditions);
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @param string $parent
     *
     * @return Field
     */
    public function setParent($parent)
    {
        $this->isChanged('parent', $parent);
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    private function findParentFieldInForm(): ?Field
    {
        if (!$this->parent) {
            return null;
        }

        $fields = $this->getForm()->getFields();
        foreach ($fields as $field) {
            if (intval($field->getId()) === intval($this->parent)) {
                return $field;
            }
        }

        return null;
    }
}
