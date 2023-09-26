<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\UserBundle\Model\UserModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeOwnerType extends AbstractType
{
    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'owner',
            ChoiceType::class,
            [
                'label'             => 'autoborna.lead.batch.add_to',
                'multiple'          => false,
                'choices'           => $this->userModel->getOwnerListChoices(),
                'required'          => true,
                'label_attr'        => ['class' => 'control-label'],
                'attr'              => ['class' => 'form-control'],
            ]
        );

        $builder->add(
          'buttons',
          FormButtonsType::class
        );
    }
}
