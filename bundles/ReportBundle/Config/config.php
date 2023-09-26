<?php

return [
    'routes' => [
        'main' => [
            'autoborna_report_index' => [
                'path'       => '/reports/{page}',
                'controller' => 'AutobornaReportBundle:Report:index',
            ],
            'autoborna_report_export' => [
                'path'       => '/reports/view/{objectId}/export/{format}',
                'controller' => 'AutobornaReportBundle:Report:export',
                'defaults'   => [
                    'format' => 'csv',
                ],
            ],
            'autoborna_report_download' => [
                'path'       => '/reports/download/{reportId}/{format}',
                'controller' => 'AutobornaReportBundle:Report:download',
                'defaults'   => [
                    'format' => 'csv',
                ],
            ],
            'autoborna_report_view' => [
                'path'       => '/reports/view/{objectId}/{reportPage}',
                'controller' => 'AutobornaReportBundle:Report:view',
                'defaults'   => [
                    'reportPage' => 1,
                ],
                'requirements' => [
                    'reportPage' => '\d+',
                ],
            ],
            'autoborna_report_schedule_preview' => [
                'path'       => '/reports/schedule/preview/{isScheduled}/{scheduleUnit}/{scheduleDay}/{scheduleMonthFrequency}',
                'controller' => 'AutobornaReportBundle:Schedule:index',
                'defaults'   => [
                    'isScheduled'            => 0,
                    'scheduleUnit'           => '',
                    'scheduleDay'            => '',
                    'scheduleMonthFrequency' => '',
                ],
            ],
            'autoborna_report_schedule' => [
                'path'       => '/reports/schedule/{reportId}/now',
                'controller' => 'AutobornaReportBundle:Schedule:now',
            ],
            'autoborna_report_action' => [
                'path'       => '/reports/{objectAction}/{objectId}',
                'controller' => 'AutobornaReportBundle:Report:execute',
            ],
        ],
        'api' => [
            'autoborna_api_getreports' => [
                'path'       => '/reports',
                'controller' => 'AutobornaReportBundle:Api\ReportApi:getEntities',
            ],
            'autoborna_api_getreport' => [
                'path'       => '/reports/{id}',
                'controller' => 'AutobornaReportBundle:Api\ReportApi:getReport',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'autoborna.report.reports' => [
                'route'     => 'autoborna_report_index',
                'iconClass' => 'fa-line-chart',
                'access'    => [
                    'report:reports:viewown',
                    'report:reports:viewother',
                ],
                'priority' => 20,
            ],
        ],
    ],

    'services' => [
        'events' => [
            'autoborna.report.configbundle.subscriber' => [
                'class' => \Autoborna\ReportBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.report.search.subscriber' => [
                'class'     => \Autoborna\ReportBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.helper.user',
                    'autoborna.report.model.report',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.report.report.subscriber' => [
                'class'     => \Autoborna\ReportBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.report.dashboard.subscriber' => [
                'class'     => \Autoborna\ReportBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.report.model.report',
                    'autoborna.security',
                ],
            ],
            'autoborna.report.scheduler.report_scheduler_subscriber' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\EventListener\ReportSchedulerSubscriber::class,
                'arguments' => [
                    'autoborna.report.model.scheduler_planner',
                ],
            ],
            'autoborna.report.report.schedule_subscriber' => [
                'class'     => \Autoborna\ReportBundle\EventListener\SchedulerSubscriber::class,
                'arguments' => [
                    'autoborna.report.model.send_schedule',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.reportconfig' => [
                'class'     => \Autoborna\ReportBundle\Form\Type\ConfigType::class,
            ],
            'autoborna.form.type.report' => [
                'class'     => \Autoborna\ReportBundle\Form\Type\ReportType::class,
                'arguments' => [
                    'autoborna.report.model.report',
                ],
            ],
            'autoborna.form.type.filter_selector' => [
                'class' => \Autoborna\ReportBundle\Form\Type\FilterSelectorType::class,
            ],
            'autoborna.form.type.table_order' => [
                'class'     => \Autoborna\ReportBundle\Form\Type\TableOrderType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.report_filters' => [
                'class'     => 'Autoborna\ReportBundle\Form\Type\ReportFiltersType',
                'arguments' => 'autoborna.factory',
            ],
            'autoborna.form.type.report_dynamic_filters' => [
                'class' => 'Autoborna\ReportBundle\Form\Type\DynamicFiltersType',
            ],
            'autoborna.form.type.report_widget' => [
                'class'     => 'Autoborna\ReportBundle\Form\Type\ReportWidgetType',
                'arguments' => 'autoborna.report.model.report',
            ],
            'autoborna.form.type.aggregator' => [
                'class'     => \Autoborna\ReportBundle\Form\Type\AggregatorType::class,
                'arguments' => 'translator',
            ],
            'autoborna.form.type.report.settings' => [
                'class' => \Autoborna\ReportBundle\Form\Type\ReportSettingsType::class,
            ],
        ],
        'helpers' => [
            'autoborna.report.helper.report' => [
                'class' => \Autoborna\ReportBundle\Helper\ReportHelper::class,
                'alias' => 'report',
            ],
        ],
        'models' => [
            'autoborna.report.model.report' => [
                'class'     => \Autoborna\ReportBundle\Model\ReportModel::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.templating',
                    'autoborna.channel.helper.channel_list',
                    'autoborna.lead.model.field',
                    'autoborna.report.helper.report',
                    'autoborna.report.model.csv_exporter',
                    'autoborna.report.model.excel_exporter',
                ],
            ],
            'autoborna.report.model.csv_exporter' => [
                'class'     => \Autoborna\ReportBundle\Model\CsvExporter::class,
                'arguments' => [
                    'autoborna.helper.template.formatter',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.report.model.excel_exporter' => [
                'class'     => \Autoborna\ReportBundle\Model\ExcelExporter::class,
                'arguments' => [
                    'autoborna.helper.template.formatter',
                ],
            ],
            'autoborna.report.model.scheduler_builder' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Builder\SchedulerBuilder::class,
                'arguments' => [
                    'autoborna.report.model.scheduler_template_factory',
                ],
            ],
            'autoborna.report.model.scheduler_template_factory' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Factory\SchedulerTemplateFactory::class,
                'arguments' => [],
            ],
            'autoborna.report.model.scheduler_date_builder' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Date\DateBuilder::class,
                'arguments' => [
                    'autoborna.report.model.scheduler_builder',
                ],
            ],
            'autoborna.report.model.scheduler_planner' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Model\SchedulerPlanner::class,
                'arguments' => [
                    'autoborna.report.model.scheduler_date_builder',
                    'doctrine.orm.default_entity_manager',
                ],
            ],
            'autoborna.report.model.send_schedule' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Model\SendSchedule::class,
                'arguments' => [
                    'autoborna.helper.mailer',
                    'autoborna.report.model.message_schedule',
                    'autoborna.report.model.file_handler',
                ],
            ],
            'autoborna.report.model.file_handler' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Model\FileHandler::class,
                'arguments' => [
                    'autoborna.helper.file_path_resolver',
                    'autoborna.helper.file_properties',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.report.model.message_schedule' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Model\MessageSchedule::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.file_properties',
                    'autoborna.helper.core_parameters',
                    'router',
                ],
            ],
            'autoborna.report.model.report_exporter' => [
                'class'     => \Autoborna\ReportBundle\Model\ReportExporter::class,
                'arguments' => [
                    'autoborna.report.model.schedule_model',
                    'autoborna.report.model.report_data_adapter',
                    'autoborna.report.model.report_export_options',
                    'autoborna.report.model.report_file_writer',
                    'event_dispatcher',
                ],
            ],
            'autoborna.report.model.schedule_model' => [
                'class'     => \Autoborna\ReportBundle\Model\ScheduleModel::class,
                'arguments' => [
                    'doctrine.orm.default_entity_manager',
                    'autoborna.report.model.scheduler_planner',
                ],
            ],
            'autoborna.report.model.report_data_adapter' => [
                'class'     => \Autoborna\ReportBundle\Adapter\ReportDataAdapter::class,
                'arguments' => [
                    'autoborna.report.model.report',
                ],
            ],
            'autoborna.report.model.report_export_options' => [
                'class'     => \Autoborna\ReportBundle\Model\ReportExportOptions::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.report.model.report_file_writer' => [
                'class'     => \Autoborna\ReportBundle\Model\ReportFileWriter::class,
                'arguments' => [
                    'autoborna.report.model.csv_exporter',
                    'autoborna.report.model.export_handler',
                ],
            ],
            'autoborna.report.model.export_handler' => [
                'class'     => \Autoborna\ReportBundle\Model\ExportHandler::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.file_path_resolver',
                ],
            ],
        ],
        'validator' => [
            'autoborna.report.validator.schedule_is_valid_validator' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Validator\ScheduleIsValidValidator::class,
                'arguments' => [
                    'autoborna.report.model.scheduler_builder',
                ],
                'tag' => 'validator.constraint_validator',
            ],
        ],
        'command' => [
            'autoborna.report.command.export_scheduler' => [
                'class'     => \Autoborna\ReportBundle\Scheduler\Command\ExportSchedulerCommand::class,
                'arguments' => [
                    'autoborna.report.model.report_exporter',
                    'translator',
                ],
                'tag' => 'console.command',
            ],
        ],
        'fixtures' => [
            'autoborna.report.fixture.report' => [
                'class' => \Autoborna\ReportBundle\DataFixtures\ORM\LoadReportData::class,
                'tag'   => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
            ],
        ],
    ],

    'parameters' => [
        'report_temp_dir'                     => '%kernel.root_dir%/../media/files/temp',
        'report_export_batch_size'            => 1000,
        'report_export_max_filesize_in_bytes' => 5000000,
        'csv_always_enclose'                  => false,
    ],
];
