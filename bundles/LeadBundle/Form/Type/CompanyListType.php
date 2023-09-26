<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\EntityLookupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label'               => 'autoborna.lead.lead.companies',
                'entity_label_column' => 'companyname',
                'modal_route'         => 'autoborna_company_action',
                'modal_header'        => 'autoborna.company.new.company',
                'model'               => 'lead.company',
                'ajax_lookup_action'  => 'lead:getLookupChoiceList',
                'multiple'            => true,
                'main_entity'         => null,
            ]
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return EntityLookupType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'company_list';
    }
}
