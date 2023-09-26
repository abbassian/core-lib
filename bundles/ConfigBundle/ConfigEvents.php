<?php

namespace Autoborna\ConfigBundle;

/**
 * Class ConfigEvents
 * Events available for ConfigBundle.
 */
final class ConfigEvents
{
    /**
     * The autoborna.config_on_generate event is thrown when the configuration form is generated.
     *
     * The event listener receives a
     * Autoborna\ConfigBundle\Event\ConfigGenerateEvent instance.
     *
     * @var string
     */
    const CONFIG_ON_GENERATE = 'autoborna.config_on_generate';

    /**
     * The autoborna.config_pre_save event is thrown right before config data are saved.
     *
     * The event listener receives a Autoborna\ConfigBundle\Event\ConfigEvent instance.
     *
     * @var string
     */
    const CONFIG_PRE_SAVE = 'autoborna.config_pre_save';

    /**
     * The autoborna.config_post_save event is thrown right after config data are saved.
     *
     * The event listener receives a Autoborna\ConfigBundle\Event\ConfigEvent instance.
     *
     * @var string
     */
    const CONFIG_POST_SAVE = 'autoborna.config_post_save';
}
