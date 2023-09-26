<?php

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\FormBundle\Entity\FormRepository;
use Autoborna\FormBundle\Model\FormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PointActionFormSubmitType.
 */
class FormListType extends AbstractType
{
    private $viewOther;

    /**
     * @var FormRepository
     */
    private $repo;

    public function __construct(CorePermissions $security, FormModel $model, UserHelper $userHelper)
    {
        $this->viewOther = $security->isGranted('form:forms:viewother');
        $this->repo      = $model->getRepository();

        $this->repo->setCurrentUser($userHelper->getUser());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $viewOther = $this->viewOther;
        $repo      = $this->repo;

        $resolver->setDefaults([
            'choices' => function (Options $options) use ($repo, $viewOther) {
                static $choices;

                if (is_array($choices)) {
                    return $choices;
                }

                $choices = [];

                $forms = $repo->getFormList('', 0, 0, $viewOther, $options['form_type']);
                foreach ($forms as $form) {
                    $choices[$form['name']] = $form['id'];
                }

                //sort by language
                ksort($choices);

                return $choices;
            },
            'expanded'          => false,
            'multiple'          => true,
            'placeholder'       => false,
            'form_type'         => null,
        ]);

        $resolver->setDefined(['form_type']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'form_list';
    }

    /**
     * @return string|\Symfony\Component\Form\FormTypeInterface|null
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}