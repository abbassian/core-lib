<?php

namespace Autoborna\ReportBundle;

/**
 * Class ReportEvents.
 *
 * Events available for ReportBundle
 */
final class ReportEvents
{
    /**
     * The autoborna.report_pre_save event is dispatched right before a report is persisted.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportEvent instance.
     *
     * @var string
     */
    const REPORT_PRE_SAVE = 'autoborna.report_pre_save';

    /**
     * The autoborna.report_post_save event is dispatched right after a report is persisted.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportEvent instance.
     *
     * @var string
     */
    const REPORT_POST_SAVE = 'autoborna.report_post_save';

    /**
     * The autoborna.report_pre_delete event is dispatched prior to when a report is deleted.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportEvent instance.
     *
     * @var string
     */
    const REPORT_PRE_DELETE = 'autoborna.report_pre_delete';

    /**
     * The autoborna.report_post_delete event is dispatched after a report is deleted.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportEvent instance.
     *
     * @var string
     */
    const REPORT_POST_DELETE = 'autoborna.report_post_delete';

    /**
     * The autoborna.report_on_build event is dispatched before displaying the report builder form to allow
     * bundles to specify report sources and columns.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportBuilderEvent instance.
     *
     * @var string
     */
    const REPORT_ON_BUILD = 'autoborna.report_on_build';

    /**
     * The autoborna.report_on_generate event is dispatched when generating a report to build the base query.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportGeneratorEvent instance.
     *
     * @var string
     */
    const REPORT_ON_GENERATE = 'autoborna.report_on_generate';

    /**
     * The autoborna.report_query_pre_execute event is dispatched to allow a plugin to alter the query before execution.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportQueryEvent instance.
     *
     * @var string
     */
    const REPORT_QUERY_PRE_EXECUTE = 'autoborna.report_query_pre_execute';

    /**
     * The autoborna.report_on_display event is dispatched when displaying a report.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportDataEvent instance.
     *
     * @var string
     */
    const REPORT_ON_DISPLAY = 'autoborna.report_on_display';

    /**
     * The autoborna.report_on_graph_generate event is dispatched to generate a graph data.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportGraphEvent instance.
     *
     * @var string
     */
    const REPORT_ON_GRAPH_GENERATE = 'autoborna.report_on_graph_generate';

    /**
     * The autoborna.report_schedule_send event is dispatched to send an exported report to a user.
     *
     * The event listener receives a Autoborna\ReportBundle\Event\ReportScheduleSendEvent instance.
     *
     * @var string
     */
    const REPORT_SCHEDULE_SEND = 'autoborna.report_schedule_send';
}
