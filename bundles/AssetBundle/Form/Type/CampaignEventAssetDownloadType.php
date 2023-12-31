<?php

namespace Autoborna\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CampaignEventAssetDownloadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'assets',
            AssetListType::class,
            [
                'label'      => 'autoborna.asset.campaign.event.assets',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.asset.campaign.event.assets.descr',
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'campaignevent_assetdownload';
    }
}
