<?php

namespace Autoborna\AssetBundle\Form\Type;

use Autoborna\CategoryBundle\Form\Type\CategoryListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormSubmitActionDownloadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'asset',
            AssetListType::class,
            [
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'autoborna.asset.form.submit.assets',
                'label_attr'  => ['class' => 'control-label'],
                'placeholder' => 'autoborna.asset.form.submit.latest.category',
                'required'    => false,
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.asset.form.submit.assets_descr',
                ],
            ]
        );

        $builder->add(
            'category',
            CategoryListType::class,
            [
                'label'         => 'autoborna.asset.form.submit.latest.category',
                'label_attr'    => ['class' => 'control-label'],
                'placeholder'   => false,
                'required'      => false,
                'bundle'        => 'asset',
                'return_entity' => false,
                'attr'          => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.asset.form.submit.latest.category_descr',
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'asset_submitaction_downloadfile';
    }
}
