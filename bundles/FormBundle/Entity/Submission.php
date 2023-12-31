<?php

namespace Autoborna\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Autoborna\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Autoborna\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\PageBundle\Entity\Page;

/**
 * Class Submission.
 */
class Submission
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Form
     **/
    private $form;

    /**
     * @var \Autoborna\CoreBundle\Entity\IpAddress
     */
    private $ipAddress;

    /**
     * @var \Autoborna\LeadBundle\Entity\Lead
     */
    private $lead;

    /**
     * @var string
     */
    private $trackingId;

    /**
     * @var \DateTime
     */
    private $dateSubmitted;

    /**
     * @var string
     */
    private $referer;

    /**
     * @var \Autoborna\PageBundle\Entity\Page
     */
    private $page;

    /**
     * @var array
     */
    private $results = [];

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('form_submissions')
            ->setCustomRepositoryClass('Autoborna\FormBundle\Entity\SubmissionRepository')
            ->addIndex(['tracking_id'], 'form_submission_tracking_search')
            ->addIndex(['date_submitted'], 'form_date_submitted');

        $builder->addBigIntIdField();

        $builder->createManyToOne('form', 'Form')
            ->inversedBy('submissions')
            ->addJoinColumn('form_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->addIpAddress();

        $builder->addLead(true, 'SET NULL');

        $builder->createField('trackingId', 'string')
            ->columnName('tracking_id')
            ->nullable()
            ->build();

        $builder->createField('dateSubmitted', 'datetime')
            ->columnName('date_submitted')
            ->build();

        $builder->addField('referer', 'text');

        $builder->createManyToOne('page', 'Autoborna\PageBundle\Entity\Page')
            ->addJoinColumn('page_id', 'id', true, false, 'SET NULL')
            ->fetchExtraLazy()
            ->build();
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('submission')
            ->addProperties(
                [
                    'id',
                    'ipAddress',
                    'form',
                    'lead',
                    'trackingId',
                    'dateSubmitted',
                    'referer',
                    'page',
                    'results',
                ]
            )
            ->setGroupPrefix('submissionEvent')
            ->addProperties(
                [
                    'id',
                    'ipAddress',
                    'form',
                    'trackingId',
                    'dateSubmitted',
                    'referer',
                    'page',
                    'results',
                ]
            )
            ->build();
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
     * Set dateSubmitted.
     *
     * @param \DateTime $dateSubmitted
     *
     * @return Submission
     */
    public function setDateSubmitted($dateSubmitted)
    {
        $this->dateSubmitted = $dateSubmitted;

        return $this;
    }

    /**
     * Get dateSubmitted.
     *
     * @return \DateTime
     */
    public function getDateSubmitted()
    {
        return $this->dateSubmitted;
    }

    /**
     * Set referer.
     *
     * @param string $referer
     *
     * @return Submission
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer.
     *
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Set form.
     *
     * @return Submission
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
     * Set ipAddress.
     *
     * @param \Autoborna\CoreBundle\Entity\IpAddress $ipAddress
     *
     * @return Submission
     */
    public function setIpAddress(IpAddress $ipAddress = null)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress.
     *
     * @return \Autoborna\CoreBundle\Entity\IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Get results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get results.
     *
     * @param $results
     *
     * @return Submission
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Set page.
     *
     * @param \Autoborna\PageBundle\Entity\Page $page
     *
     * @return Submission
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page.
     *
     * @return \Autoborna\PageBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @return $this
     */
    public function setLead(Lead $lead = null)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * @param $trackingId
     *
     * @return $this
     */
    public function setTrackingId($trackingId)
    {
        $this->trackingId = $trackingId;

        return $this;
    }

    /**
     * This method is used by standard entity algorithms to check if the current
     * user has permission to view/edit/delete this item. Provide the form creator for it.
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->getForm()->getCreatedBy();
    }

    /**
     * @param string $alias
     *
     * @return Field|null
     */
    public function getFieldByAlias($alias)
    {
        foreach ($this->getForm()->getFields() as $field) {
            if ($field->getAlias() === $alias) {
                return $field;
            }
        }

        return null;
    }
}
