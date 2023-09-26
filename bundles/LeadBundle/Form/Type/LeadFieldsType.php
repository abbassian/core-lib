<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Helper\ArrayHelper;
use Autoborna\LeadBundle\Model\FieldModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeadFieldsType extends AbstractType
{
    /**
     * @var FieldModel
     */
    protected $fieldModel;

    public function __construct(FieldModel $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $fieldList = ArrayHelper::flipArray($this->fieldModel->getFieldList());
                if ($options['with_tags']) {
                    $fieldList['Core']['autoborna.lead.field.tags'] = 'tags';
                }
                if ($options['with_company_fields']) {
                    $fieldList['Company'] = array_flip($this->fieldModel->getFieldList(false, true, ['isPublished' => true, 'object' => 'company']));
                }
                if ($options['with_utm']) {
                    $fieldList['UTM']['autoborna.lead.field.utmcampaign'] = 'utm_campaign';
                    $fieldList['UTM']['autoborna.lead.field.utmcontent']  = 'utm_content';
                    $fieldList['UTM']['autoborna.lead.field.utmmedium']   = 'utm_medium';
                    $fieldList['UTM']['autoborna.lead.field.umtsource']   = 'utm_source';
                    $fieldList['UTM']['autoborna.lead.field.utmterm']     = 'utm_term';
                }

                return $fieldList;
            },
            'global_only'         => false,
            'required'            => false,
            'with_company_fields' => false,
            'with_tags'           => false,
            'with_utm'            => false,
        ]);
    }

    /**
     * @return string|\Symfony\Component\Form\FormTypeInterface|null
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leadfields_choices';
    }
}
