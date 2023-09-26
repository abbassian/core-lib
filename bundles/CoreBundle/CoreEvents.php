<?php

namespace Autoborna\CoreBundle;

/**
 * Class CoreEvents.
 */
final class CoreEvents
{
    /**
     * The autoborna.build_menu event is thrown to render menu items.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\MenuEvent instance.
     *
     * @var string
     */
    const BUILD_MENU = 'autoborna.build_menu';

    /**
     * The autoborna.build_route event is thrown to build Autoborna bundle routes.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\RouteEvent instance.
     *
     * @var string
     */
    const BUILD_ROUTE = 'autoborna.build_route';

    /**
     * The autoborna.global_search event is thrown to build global search results from applicable bundles.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\GlobalSearchEvent instance.
     *
     * @var string
     */
    const GLOBAL_SEARCH = 'autoborna.global_search';

    /**
     * The autoborna.list_stats event is thrown to build statistical results from applicable bundles/database tables.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\StatsEvent instance.
     *
     * @var string
     */
    const LIST_STATS = 'autoborna.list_stats';

    /**
     * The autoborna.build_command_list event is thrown to build global search's autocomplete list.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\CommandListEvent instance.
     *
     * @var string
     */
    const BUILD_COMMAND_LIST = 'autoborna.build_command_list';

    /**
     * The autoborna.on_fetch_icons event is thrown to fetch icons of menu items.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\IconEvent instance.
     *
     * @var string
     */
    const FETCH_ICONS = 'autoborna.on_fetch_icons';

    /**
     * The autoborna.build_canvas_content event is dispatched to populate the content for the right panel.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\SidebarCanvasEvent instance.
     *
     * @var string
     *
     * @deprecated Deprecated in Autoborna 4.3. Will be removed in Autoborna 5.0
     */
    const BUILD_CANVAS_CONTENT = 'autoborna.build_canvas_content';

    /**
     * The autoborna.pre_upgrade is dispatched before an upgrade.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\UpgradeEvent instance.
     *
     * @var string
     */
    const PRE_UPGRADE = 'autoborna.pre_upgrade';

    /**
     * The autoborna.post_upgrade is dispatched after an upgrade.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\UpgradeEvent instance.
     *
     * @var string
     */
    const POST_UPGRADE = 'autoborna.post_upgrade';

    /**
     * The autoborna.build_embeddable_js event is dispatched to allow plugins to extend the autoborna tracking js.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\BuildJsEvent instance.
     *
     * @var string
     */
    const BUILD_MAUTIC_JS = 'autoborna.build_embeddable_js';

    /**
     * The autoborna.maintenance_cleanup_data event is dispatched to purge old data.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\MaintenanceEvent instance.
     *
     * @var string
     */
    const MAINTENANCE_CLEANUP_DATA = 'autoborna.maintenance_cleanup_data';

    /**
     * The autoborna.view_inject_custom_buttons event is dispatched to inject custom buttons into Autoborna's UI by plugins/other bundles.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\CustomButtonEvent instance.
     *
     * @var string
     */
    const VIEW_INJECT_CUSTOM_BUTTONS = 'autoborna.view_inject_custom_buttons';

    /**
     * The autoborna.view_inject_custom_content event is dispatched by views to collect custom content to be injected in UIs.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\CustomContentEvent instance.
     *
     * @var string
     */
    const VIEW_INJECT_CUSTOM_CONTENT = 'autoborna.view_inject_custom_content';

    /**
     * The autoborna.view_inject_custom_template event is dispatched when a template is to be rendered giving opportunity to change template or
     * vars.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\CustomTemplateEvent instance.
     *
     * @var string
     */
    const VIEW_INJECT_CUSTOM_TEMPLATE = 'autoborna.view_inject_custom_template';

    /**
     * The autoborna.view_inject_custom_assets event is dispatched when assets are rendered.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\CustomAssetsEvent instance.
     *
     * @var string
     */
    const VIEW_INJECT_CUSTOM_ASSETS = 'autoborna.view_inject_custom_assets';

    /**
     * The autoborna.on_form_type_build event is dispatched by views to inject custom fields into any form.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\CustomFormEvent instance.
     *
     * @var string
     *
     * @deprecated since Autoborna 4 because it is not used anywhere
     */
    const ON_FORM_TYPE_BUILD = 'autoborna.on_form_type_build';

    /**
     * The autoborna.on_generated_columns_build event is dispatched when a list of generated columns is being built.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\GeneratedColumnsEvent instance.
     *
     * @var string
     */
    const ON_GENERATED_COLUMNS_BUILD = 'autoborna.on_generated_columns_build';
}
