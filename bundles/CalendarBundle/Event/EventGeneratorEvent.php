<?php

namespace Autoborna\CalendarBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class EventGeneratorEvent.
 */
class EventGeneratorEvent extends Event
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var int
     */
    private $entityId;

    /**
     * @var \Autoborna\CoreBundle\Model\FormModel
     */
    private $model;

    /**
     * @var \Autoborna\CoreBundle\Entity\FormEntity
     */
    private $entity;

    /**
     * @var string
     */
    private $contentTemplate;

    /**
     * @var bool
     */
    private $access = false;

    /**
     * @var string
     */
    private $formName;

    /**
     * @param string $source
     * @param int    $id
     */
    public function __construct($source, $entityId)
    {
        $this->source   = $source;
        $this->entityId = $entityId;
    }

    /**
     * Set content template.
     *
     * @param string $contentTemplate
     */
    public function setContentTemplate($contentTemplate)
    {
        $this->contentTemplate = $contentTemplate;
    }

    /**
     * Fetches the event source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Fetches the event entityId.
     *
     * @return int
     */
    public function getEntityId()
    {
        return (int) $this->entityId;
    }

    /**
     * Fetches the event model.
     *
     * @return \Autoborna\CoreBundle\Model\FormModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the event model.
     */
    public function setModel(\Autoborna\CoreBundle\Model\FormModel $model)
    {
        $this->model = $model;
    }

    /**
     * Fetches the event entity.
     *
     * @return \Autoborna\CoreBundle\Entity\FormEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the event entity.
     */
    public function setEntity(\Autoborna\CoreBundle\Entity\FormEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Fetches the events content template.
     *
     * @return string
     */
    public function getContentTemplate()
    {
        return $this->contentTemplate;
    }

    /**
     * Confirmes that user can access the entity.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return $this->access;
    }

    /**
     * Set the event access.
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * Fetches the name of the form which should be loaded in the modal.
     *
     * @return string
     */
    public function getFormName()
    {
        return $this->formName;
    }

    /**
     * Set the event formName.
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;
    }
}
