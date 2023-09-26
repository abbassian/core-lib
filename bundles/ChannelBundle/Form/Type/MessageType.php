<?php

namespace Autoborna\ChannelBundle\Form\Type;

use Autoborna\ChannelBundle\Entity\Channel;
use Autoborna\ChannelBundle\Entity\Message;
use Autoborna\ChannelBundle\Model\MessageModel;
use Autoborna\CoreBundle\Form\Type\AbstractFormStandardType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class MessageType.
 */
class MessageType extends AbstractFormStandardType
{
    /**
     * @var MessageModel
     */
    protected $model;

    /**
     * MessageType constructor.
     */
    public function __construct(MessageModel $messageModel)
    {
        $this->model = $messageModel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add standard fields
        $options = array_merge($options, ['model_name' => 'channel.message', 'permission_base' => 'channel:messages']);
        parent::buildForm($builder, $options);

        // Ensure that all channels exist
        /** @var Message $message */
        $message         = $options['data'];
        $channels        = $this->model->getChannels();
        $messageChannels = $message->getChannels();

        foreach ($channels as $channelType => $channel) {
            if (!isset($messageChannels[$channelType])) {
                $message->addChannel(
                    (new Channel())
                        ->setChannel($channelType)
                        ->setMessage($message)
                );
            }
        }

        $builder->add(
            'channels',
            CollectionType::class,
            [
                'label'         => false,
                'allow_add'     => true,
                'allow_delete'  => false,
                'prototype'     => false,
                'entry_type'    => ChannelType::class,
                'by_reference'  => false,
                'entry_options' => [
                    'channels' => $channels,
                ],
                'constraints' => [
                    new Valid(),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'      => Message::class,
                'category_bundle' => 'messages',
            ]
        );
    }
}
