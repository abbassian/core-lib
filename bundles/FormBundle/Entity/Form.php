<?php

namespace Autoborna\FormBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Autoborna\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Autoborna\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Autoborna\CoreBundle\Entity\FormEntity;
use Autoborna\CoreBundle\Helper\InputHelper;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class Form.
 */
class Form extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $formAttributes;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var \Autoborna\CategoryBundle\Entity\Category
     **/
    private $category;

    /**
     * @var string
     */
    private $cachedHtml;

    /**
     * @var string
     */
    private $postAction = 'return';

    /**
     * @var string
     */
    private $postActionProperty;

    /**
     * @var \DateTime
     */
    private $publishUp;

    /**
     * @var \DateTime
     */
    private $publishDown;

    /**
     * @var ArrayCollection
     */
    private $fields;

    /**
     * @var ArrayCollection
     */
    private $actions;

    /**
     * @var string
     */
    private $template;

    /**
     * @var bool
     */
    private $inKioskMode = false;

    /**
     * @var bool
     */
    private $renderStyle = false;

    /**
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="form", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"dateSubmitted" = "DESC"})
     *
     * @var ArrayCollection
     */
    private $submissions;

    /**
     * @var int
     */
    public $submissionCount;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var bool
     */
    private $noIndex;

    /**
     * @var int
     */
    private $progressiveProfilingLimit;

    /**
     * This var is used to cache the result once gained from the loop.
     *
     * @var bool
     */
    private $usesProgressiveProfiling;

    public function __clone()
    {
        $this->id = null;

        parent::__clone();
    }

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->fields      = new ArrayCollection();
        $this->actions     = new ArrayCollection();
        $this->submissions = new ArrayCollection();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('forms')
            ->setCustomRepositoryClass('Autoborna\FormBundle\Entity\FormRepository');

        $builder->addIdColumns();

        $builder->addField('alias', 'string');

        $builder->addNullableField('formAttributes', 'string', 'form_attr');

        $builder->addCategory();

        $builder->createField('cachedHtml', 'text')
            ->columnName('cached_html')
            ->nullable()
            ->build();

        $builder->createField('postAction', 'string')
            ->columnName('post_action')
            ->build();

        $builder->createField('postActionProperty', Types::TEXT)
            ->columnName('post_action_property')
            ->nullable()
            ->build();

        $builder->addPublishDates();

        $builder->createOneToMany('fields', 'Field')
            ->setIndexBy('id')
            ->setOrderBy(['order' => 'ASC'])
            ->mappedBy('form')
            ->cascadeAll()
            ->fetchExtraLazy()
            ->build();

        $builder->createOneToMany('actions', 'Action')
            ->setIndexBy('id')
            ->setOrderBy(['order' => 'ASC'])
            ->mappedBy('form')
            ->cascadeAll()
            ->fetchExtraLazy()
            ->build();

        $builder->createField('template', 'string')
            ->nullable()
            ->build();

        $builder->createField('inKioskMode', 'boolean')
            ->columnName('in_kiosk_mode')
            ->nullable()
            ->build();

        $builder->createField('renderStyle', 'boolean')
            ->columnName('render_style')
            ->nullable()
            ->build();

        $builder->createOneToMany('submissions', 'Submission')
            ->setOrderBy(['dateSubmitted' => 'DESC'])
            ->mappedBy('form')
            ->fetchExtraLazy()
            ->build();

        $builder->addNullableField('formType', 'string', 'form_type');

        $builder->createField('noIndex', 'boolean')
            ->columnName('no_index')
            ->nullable()
            ->build();

        $builder->addNullableField('progressiveProfilingLimit', Type::INTEGER, 'progressive_profiling_limit');
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank([
            'message' => 'autoborna.core.name.required',
            'groups'  => ['form'],
        ]));

        $metadata->addPropertyConstraint('postActionProperty', new Assert\NotBlank([
            'message' => 'autoborna.form.form.postactionproperty_message.notblank',
            'groups'  => ['messageRequired'],
        ]));

        $metadata->addPropertyConstraint('postActionProperty', new Assert\NotBlank([
            'message' => 'autoborna.form.form.postactionproperty_redirect.notblank',
            'groups'  => ['urlRequired'],
        ]));

        $metadata->addPropertyConstraint('postActionProperty', new Assert\Url([
            'message' => 'autoborna.form.form.postactionproperty_redirect.notblank',
            'groups'  => ['urlRequiredPassTwo'],
        ]));

        $metadata->addPropertyConstraint('formType', new Assert\Choice([
            'choices' => ['standalone', 'campaign'],
        ]));

        $metadata->addPropertyConstraint('progressiveProfilingLimit', new Assert\GreaterThan([
            'value'   => 0,
            'message' => 'autoborna.form.form.progressive_profiling_limit.error',
            'groups'  => ['progressiveProfilingLimit'],
        ]));
    }

    /**
     * @return array
     */
    public static function determineValidationGroups(\Symfony\Component\Form\Form $form)
    {
        $data   = $form->getData();
        $groups = ['form'];

        $postAction = $data->getPostAction();

        if ('message' == $postAction) {
            $groups[] = 'messageRequired';
        } elseif ('redirect' == $postAction) {
            $groups[] = 'urlRequired';
        }

        if ('' != $data->getProgressiveProfilingLimit()) {
            $groups[] = 'progressiveProfilingLimit';
        }

        return $groups;
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('form')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'alias',
                    'category',
                ]
            )
            ->addProperties(
                [
                    'description',
                    'cachedHtml',
                    'publishUp',
                    'publishDown',
                    'fields',
                    'actions',
                    'template',
                    'inKioskMode',
                    'renderStyle',
                    'formType',
                    'postAction',
                    'postActionProperty',
                    'noIndex',
                    'formAttributes',
                ]
            )
            ->build();
    }

    /**
     * @param $prop
     * @param $val
     */
    protected function isChanged($prop, $val)
    {
        if ('actions' == $prop || 'fields' == $prop) {
            //changes are already computed so just add them
            $this->changes[$prop][$val[0]] = $val[1];
        } else {
            parent::isChanged($prop, $val);
        }
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
     * Set name.
     *
     * @param string $name
     *
     * @return Form
     */
    public function setName($name)
    {
        $this->isChanged('name', $name);
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Form
     */
    public function setDescription($description)
    {
        $this->isChanged('description', $description);
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription($truncate = false, $length = 45)
    {
        if ($truncate) {
            if (strlen($this->description) > $length) {
                return substr($this->description, 0, $length).'...';
            }
        }

        return $this->description;
    }

    /**
     * Set cachedHtml.
     *
     * @param string $cachedHtml
     *
     * @return Form
     */
    public function setCachedHtml($cachedHtml)
    {
        $this->cachedHtml = $cachedHtml;

        return $this;
    }

    /**
     * Get cachedHtml.
     *
     * @return string
     */
    public function getCachedHtml()
    {
        return $this->cachedHtml;
    }

    /**
     * Get render style.
     *
     * @return string
     */
    public function getRenderStyle()
    {
        return $this->renderStyle;
    }

    /**
     * Set postAction.
     *
     * @param string $postAction
     *
     * @return Form
     */
    public function setPostAction($postAction)
    {
        $this->isChanged('postAction', $postAction);
        $this->postAction = $postAction;

        return $this;
    }

    /**
     * Get postAction.
     *
     * @return string
     */
    public function getPostAction()
    {
        return $this->postAction;
    }

    /**
     * Set postActionProperty.
     *
     * @param string $postActionProperty
     *
     * @return Form
     */
    public function setPostActionProperty($postActionProperty)
    {
        $this->isChanged('postActionProperty', $postActionProperty);
        $this->postActionProperty = $postActionProperty;

        return $this;
    }

    /**
     * Get postActionProperty.
     *
     * @return string
     */
    public function getPostActionProperty()
    {
        return $this->postActionProperty;
    }

    /**
     * Get result count.
     */
    public function getResultCount()
    {
        return count($this->submissions);
    }

    /**
     * Set publishUp.
     *
     * @param \DateTime $publishUp
     *
     * @return Form
     */
    public function setPublishUp($publishUp)
    {
        $this->isChanged('publishUp', $publishUp);
        $this->publishUp = $publishUp;

        return $this;
    }

    /**
     * Get publishUp.
     *
     * @return \DateTime
     */
    public function getPublishUp()
    {
        return $this->publishUp;
    }

    /**
     * Set publishDown.
     *
     * @param \DateTime $publishDown
     *
     * @return Form
     */
    public function setPublishDown($publishDown)
    {
        $this->isChanged('publishDown', $publishDown);
        $this->publishDown = $publishDown;

        return $this;
    }

    /**
     * Get publishDown.
     *
     * @return \DateTime
     */
    public function getPublishDown()
    {
        return $this->publishDown;
    }

    /**
     * Add a field.
     *
     * @param $key
     *
     * @return Form
     */
    public function addField($key, Field $field)
    {
        if ($changes = $field->getChanges()) {
            $this->isChanged('fields', [$key, $changes]);
        }
        $this->fields[$key] = $field;

        return $this;
    }

    /**
     * Remove a field.
     *
     * @param $key
     */
    public function removeField($key, Field $field)
    {
        if ($changes = $field->getChanges()) {
            $this->isChanged('fields', [$key, $changes]);
        }
        $this->fields->removeElement($field);
    }

    /**
     * Get fields.
     *
     * @return \Doctrine\Common\Collections\Collection|Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get array of field aliases.
     *
     * @return array
     */
    public function getFieldAliases()
    {
        $aliases = [];
        $fields  = $this->getFields();

        if ($fields) {
            foreach ($fields as $field) {
                $aliases[] = $field->getAlias();
            }
        }

        return $aliases;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return Form
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
     * Add submissions.
     *
     * @return Form
     */
    public function addSubmission(Submission $submissions)
    {
        $this->submissions[] = $submissions;

        return $this;
    }

    /**
     * Remove submissions.
     */
    public function removeSubmission(Submission $submissions)
    {
        $this->submissions->removeElement($submissions);
    }

    /**
     * Get submissions.
     *
     * @return \Doctrine\Common\Collections\Collection|Submission[]
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }

    /**
     * Add actions.
     *
     * @param $key
     *
     * @return Form
     */
    public function addAction($key, Action $action)
    {
        if ($changes = $action->getChanges()) {
            $this->isChanged('actions', [$key, $changes]);
        }
        $this->actions[$key] = $action;

        return $this;
    }

    /**
     * Remove action.
     */
    public function removeAction(Action $action)
    {
        $this->actions->removeElement($action);
    }

    /**
     * Removes all actions.
     */
    public function clearActions()
    {
        $this->actions = new ArrayCollection();
    }

    /**
     * Get actions.
     *
     * @return \Doctrine\Common\Collections\Collection|Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getInKioskMode()
    {
        return $this->inKioskMode;
    }

    /**
     * @param mixed $inKioskMode
     */
    public function setInKioskMode($inKioskMode)
    {
        $this->inKioskMode = $inKioskMode;
    }

    /**
     * @param mixed $renderStyle
     */
    public function setRenderStyle($renderStyle)
    {
        $this->renderStyle = $renderStyle;
    }

    /**
     * @return mixed
     */
    public function isInKioskMode()
    {
        return $this->getInKioskMode();
    }

    /**
     * @return mixed
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @param mixed $formType
     *
     * @return Form
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;

        return $this;
    }

    /**
     * @param bool|null $noIndex
     */
    public function setNoIndex($noIndex)
    {
        $sanitizedNoIndex = null === $noIndex ? null : (bool) $noIndex;
        $this->isChanged('noIndex', $sanitizedNoIndex);
        $this->noIndex = $sanitizedNoIndex;
    }

    /**
     * @return bool|null
     */
    public function getNoIndex()
    {
        return $this->noIndex;
    }

    /**
     * @param string $formAttributes
     *
     * @return Form
     */
    public function setFormAttributes($formAttributes)
    {
        $this->isChanged('formAttributes', $formAttributes);
        $this->formAttributes = $formAttributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormAttributes()
    {
        return $this->formAttributes;
    }

    /**
     * @return bool
     */
    public function isStandalone()
    {
        return 'campaign' != $this->formType;
    }

    /**
     * Generate a form name for HTML attributes.
     */
    public function generateFormName()
    {
        $name = strtolower(
            InputHelper::alphanum(
                InputHelper::transliterate(
                    $this->name
                )
            )
        );

        return (empty($name)) ? 'form-'.$this->id : $name;
    }

    /**
     * Check if some Progressive Profiling setting is turned on on any of the form fields.
     *
     * @return bool
     */
    public function usesProgressiveProfiling()
    {
        if (null !== $this->usesProgressiveProfiling) {
            return $this->usesProgressiveProfiling;
        }

        // Progressive profiling must be turned off in the kiosk mode
        if (false === $this->getInKioskMode()) {
            if ('' != $this->getProgressiveProfilingLimit()) {
                $this->usesProgressiveProfiling = true;

                return $this->usesProgressiveProfiling;
            }

            // Search for a field with a progressive profiling setting on
            foreach ($this->fields->toArray() as $field) {
                if (false === $field->getShowWhenValueExists() || $field->getShowAfterXSubmissions() > 0) {
                    $this->usesProgressiveProfiling = true;

                    return $this->usesProgressiveProfiling;
                }
            }
        }

        $this->usesProgressiveProfiling = false;

        return $this->usesProgressiveProfiling;
    }

    /**
     * @param int $progressiveProfilingLimit
     *
     * @return Form
     */
    public function setProgressiveProfilingLimit($progressiveProfilingLimit)
    {
        $this->isChanged('progressiveProfilingLimit', $progressiveProfilingLimit);
        $this->progressiveProfilingLimit = $progressiveProfilingLimit;

        return $this;
    }

    /**
     * @return int
     */
    public function getProgressiveProfilingLimit()
    {
        if (0 === $this->progressiveProfilingLimit) {
            $this->progressiveProfilingLimit = '';
        }

        return $this->progressiveProfilingLimit;
    }
}
