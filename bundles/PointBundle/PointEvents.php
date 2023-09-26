<?php

namespace Autoborna\PointBundle;

/**
 * Class PointEvents.
 *
 * Events available for PointBundle
 */
final class PointEvents
{
    /**
     * The autoborna.point_pre_save event is thrown right before a form is persisted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\PointEvent instance.
     *
     * @var string
     */
    const POINT_PRE_SAVE = 'autoborna.point_pre_save';

    /**
     * The autoborna.point_post_save event is thrown right after a form is persisted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\PointEvent instance.
     *
     * @var string
     */
    const POINT_POST_SAVE = 'autoborna.point_post_save';

    /**
     * The autoborna.point_pre_delete event is thrown before a form is deleted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\PointEvent instance.
     *
     * @var string
     */
    const POINT_PRE_DELETE = 'autoborna.point_pre_delete';

    /**
     * The autoborna.point_post_delete event is thrown after a form is deleted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\PointEvent instance.
     *
     * @var string
     */
    const POINT_POST_DELETE = 'autoborna.point_post_delete';

    /**
     * The autoborna.point_on_build event is thrown before displaying the point builder form to allow adding of custom actions.
     *
     * The event listener receives a Autoborna\PointBundle\Event\PointBuilderEvent instance.
     *
     * @var string
     */
    const POINT_ON_BUILD = 'autoborna.point_on_build';

    /**
     * The autoborna.point_on_action event is thrown to execute a point action.
     *
     * The event listener receives a Autoborna\PointBundle\Event\PointActionEvent instance.
     *
     * @var string
     */
    const POINT_ON_ACTION = 'autoborna.point_on_action';

    /**
     * The autoborna.point_pre_save event is thrown right before a form is persisted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\TriggerEvent instance.
     *
     * @var string
     */
    const TRIGGER_PRE_SAVE = 'autoborna.trigger_pre_save';

    /**
     * The autoborna.trigger_post_save event is thrown right after a form is persisted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\TriggerEvent instance.
     *
     * @var string
     */
    const TRIGGER_POST_SAVE = 'autoborna.trigger_post_save';

    /**
     * The autoborna.trigger_pre_delete event is thrown before a form is deleted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\TriggerEvent instance.
     *
     * @var string
     */
    const TRIGGER_PRE_DELETE = 'autoborna.trigger_pre_delete';

    /**
     * The autoborna.trigger_post_delete event is thrown after a form is deleted.
     *
     * The event listener receives a Autoborna\PointBundle\Event\TriggerEvent instance.
     *
     * @var string
     */
    const TRIGGER_POST_DELETE = 'autoborna.trigger_post_delete';

    /**
     * The autoborna.trigger_on_build event is thrown before displaying the trigger builder form to allow adding of custom actions.
     *
     * The event listener receives a Autoborna\PointBundle\Event\TriggerBuilderEvent instance.
     *
     * @var string
     */
    const TRIGGER_ON_BUILD = 'autoborna.trigger_on_build';

    /**
     * The autoborna.trigger_on_event_execute event is thrown to execute a trigger event.
     *
     * The event listener receives a Autoborna\PointBundle\Event\TriggerExecutedEvent instance.
     *
     * @var string
     */
    const TRIGGER_ON_EVENT_EXECUTE = 'autoborna.trigger_on_event_execute';
}
