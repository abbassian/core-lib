<?php

namespace Autoborna\CategoryBundle;

/**
 * Class CategoryBundle
 * Events available for CategoryBundle.
 */
final class CategoryEvents
{
    /**
     * The autoborna.category_pre_save event is thrown right before a category is persisted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_PRE_SAVE = 'autoborna.category_pre_save';

    /**
     * The autoborna.category_post_save event is thrown right after a category is persisted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_POST_SAVE = 'autoborna.category_post_save';

    /**
     * The autoborna.category_pre_delete event is thrown prior to when a category is deleted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_PRE_DELETE = 'autoborna.category_pre_delete';

    /**
     * The autoborna.category_post_delete event is thrown after a category is deleted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_POST_DELETE = 'autoborna.category_post_delete';

    /**
     * The autoborna.category_on_bundle_list_build event is thrown when a list of bundles supporting categories is build.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryTypesEvent instance.
     *
     * @var string
     */
    const CATEGORY_ON_BUNDLE_LIST_BUILD = 'autoborna.category_on_bundle_list_build';
}
