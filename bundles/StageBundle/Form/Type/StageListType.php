<?php

namespace Autoborna\StageBundle\Form\Type;

use Autoborna\StageBundle\Entity\Stage;
use Autoborna\StageBundle\Model\StageModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserListType.
 */
class StageListType extends AbstractType
{
    private $choices = [];

    public function __construct(StageModel $model)
    {
        $choices = $model->getRepository()->getEntities([
            'filter' => [
                'force' => [
                    [
                        'column' => 's.isPublished',
                        'expr'   => 'eq',
                        'value'  => true,
                    ],
                ],
            ],
        ]);

        /** @var Stage $choice */
        foreach ($choices as $choice) {
            $this->choices[$choice->getName()] = $choice->getId();
        }

        //sort by language
        ksort($this->choices);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'           => $this->choices,
            'expanded'          => false,
            'multiple'          => true,
            'required'          => false,
            'placeholder'       => 'autoborna.core.form.chooseone',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'stage_list';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
