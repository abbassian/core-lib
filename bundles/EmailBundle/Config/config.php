<?php

return [
    'routes' => [
        'main' => [
            'autoborna_email_index' => [
                'path'       => '/emails/{page}',
                'controller' => 'AutobornaEmailBundle:Email:index',
            ],
            'autoborna_email_graph_stats' => [
                'path'       => '/emails-graph-stats/{objectId}/{isVariant}/{dateFrom}/{dateTo}',
                'controller' => 'AutobornaEmailBundle:EmailGraphStats:view',
            ],
            'autoborna_email_action' => [
                'path'       => '/emails/{objectAction}/{objectId}',
                'controller' => 'AutobornaEmailBundle:Email:execute',
            ],
            'autoborna_email_contacts' => [
                'path'       => '/emails/view/{objectId}/contact/{page}',
                'controller' => 'AutobornaEmailBundle:Email:contacts',
            ],
        ],
        'api' => [
            'autoborna_api_emailstandard' => [
                'standard_entity' => true,
                'name'            => 'emails',
                'path'            => '/emails',
                'controller'      => 'AutobornaEmailBundle:Api\EmailApi',
            ],
            'autoborna_api_sendemail' => [
                'path'       => '/emails/{id}/send',
                'controller' => 'AutobornaEmailBundle:Api\EmailApi:send',
                'method'     => 'POST',
            ],
            'autoborna_api_sendcontactemail' => [
                'path'       => '/emails/{id}/contact/{leadId}/send',
                'controller' => 'AutobornaEmailBundle:Api\EmailApi:sendLead',
                'method'     => 'POST',
            ],
            'autoborna_api_reply' => [
                'path'       => '/emails/reply/{trackingHash}',
                'controller' => 'AutobornaEmailBundle:Api\EmailApi:reply',
                'method'     => 'POST',
            ],
        ],
        'public' => [
            'autoborna_plugin_tracker' => [
                'path'         => '/plugin/{integration}/tracking.gif',
                'controller'   => 'AutobornaEmailBundle:Public:pluginTrackingGif',
                'requirements' => [
                    'integration' => '.+',
                ],
            ],
            'autoborna_email_tracker' => [
                'path'       => '/email/{idHash}.gif',
                'controller' => 'AutobornaEmailBundle:Public:trackingImage',
            ],
            'autoborna_email_webview' => [
                'path'       => '/email/view/{idHash}',
                'controller' => 'AutobornaEmailBundle:Public:index',
            ],
            'autoborna_email_unsubscribe' => [
                'path'       => '/email/unsubscribe/{idHash}',
                'controller' => 'AutobornaEmailBundle:Public:unsubscribe',
            ],
            'autoborna_email_resubscribe' => [
                'path'       => '/email/resubscribe/{idHash}',
                'controller' => 'AutobornaEmailBundle:Public:resubscribe',
            ],
            'autoborna_mailer_transport_callback' => [
                'path'       => '/mailer/{transport}/callback',
                'controller' => 'AutobornaEmailBundle:Public:mailerCallback',
                'method'     => ['GET', 'POST'],
            ],
            'autoborna_email_preview' => [
                'path'       => '/email/preview/{objectId}',
                'controller' => 'AutobornaEmailBundle:Public:preview',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'items' => [
                'autoborna.email.emails' => [
                    'route'    => 'autoborna_email_index',
                    'access'   => ['email:emails:viewown', 'email:emails:viewother'],
                    'parent'   => 'autoborna.core.channels',
                    'priority' => 100,
                ],
            ],
        ],
    ],
    'categories' => [
        'email' => null,
    ],
    'services' => [
        'events' => [
            'autoborna.email.subscriber.aggregate_stats' => [
                'class'     => \Autoborna\EmailBundle\EventListener\GraphAggregateStatsSubscriber::class,
                'arguments' => [
                    'autoborna.email.helper.stats_collection',
                ],
            ],
            'autoborna.email.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.email.model.email',
                    'translator',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.email.queue.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\QueueSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                ],
            ],
            'autoborna.email.momentum.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\MomentumSubscriber::class,
                'arguments' => [
                    'autoborna.transport.momentum.callback',
                    'autoborna.queue.service',
                    'autoborna.email.helper.request.storage',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.email.monitored.bounce.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\ProcessBounceSubscriber::class,
                'arguments' => [
                    'autoborna.message.processor.bounce',
                ],
            ],
            'autoborna.email.monitored.unsubscribe.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\ProcessUnsubscribeSubscriber::class,
                'arguments' => [
                    'autoborna.message.processor.unsubscribe',
                    'autoborna.message.processor.feedbackloop',
                ],
            ],
            'autoborna.email.monitored.unsubscribe.replier' => [
                'class'     => \Autoborna\EmailBundle\EventListener\ProcessReplySubscriber::class,
                'arguments' => [
                    'autoborna.message.processor.replier',
                    'autoborna.helper.cache_storage',
                ],
            ],
            'autoborna.emailbuilder.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\BuilderSubscriber::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.email.model.email',
                    'autoborna.page.model.trackable',
                    'autoborna.page.model.redirect',
                    'translator',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.emailtoken.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\TokenSubscriber::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.lead.helper.primary_company',
                ],
            ],
            'autoborna.email.generated_columns.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\GeneratedColumnSubscriber::class,
            ],
            'autoborna.email.campaignbundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'autoborna.campaign.executioner.realtime',
                    'autoborna.email.model.send_email_to_user',
                    'translator',
                ],
            ],
            'autoborna.email.campaignbundle.condition_subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\CampaignConditionSubscriber::class,
                'arguments' => [
                    'autoborna.validator.email',
                ],
            ],
            'autoborna.email.formbundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.email.reportbundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'autoborna.lead.model.company_report_data',
                    'autoborna.email.repository.stat',
                    'autoborna.generated.columns.provider',
                ],
            ],
            'autoborna.email.leadbundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.email.repository.emailReply',
                    'autoborna.email.repository.stat',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.email.pointbundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\PointSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.point',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.email.touser.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\EmailToUserSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.send_email_to_user',
                ],
            ],
            'autoborna.email.search.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.helper.user',
                    'autoborna.email.model.email',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.email.webhook.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\WebhookSubscriber::class,
                'arguments' => [
                    'autoborna.webhook.model.webhook',
                ],
            ],
            'autoborna.email.configbundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\ConfigSubscriber::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.email.pagebundle.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'autoborna.campaign.executioner.realtime',
                    'request_stack',
                ],
            ],
            'autoborna.email.dashboard.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'router',
                ],
            ],
            'autoborna.email.dashboard.best.hours.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\DashboardBestHoursSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                ],
            ],
            'autoborna.email.broadcast.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\BroadcastSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'doctrine.orm.entity_manager',
                    'translator',
                    'autoborna.lead.model.lead',
                    'autoborna.email.model.email',
                ],
            ],
            'autoborna.email.messagequeue.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\MessageQueueSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                ],
            ],
            'autoborna.email.channel.subscriber' => [
                'class' => \Autoborna\EmailBundle\EventListener\ChannelSubscriber::class,
            ],
            'autoborna.email.stats.subscriber' => [
                'class'     => \Autoborna\EmailBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.email.subscriber.contact_tracker' => [
                'class'     => \Autoborna\EmailBundle\EventListener\TrackingSubscriber::class,
                'arguments' => [
                    'autoborna.email.repository.stat',
                ],
            ],
            'autoborna.email.subscriber.determine_winner' => [
                'class'     => \Autoborna\EmailBundle\EventListener\DetermineWinnerSubscriber::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.email' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\EmailType::class,
                'arguments' => [
                    'translator',
                    'doctrine.orm.entity_manager',
                    'autoborna.stage.model.stage',
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.theme',
                ],
            ],
            'autoborna.form.type.email.utm_tags' => [
                'class' => \Autoborna\EmailBundle\Form\Type\EmailUtmTagsType::class,
            ],
            'autoborna.form.type.emailvariant' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\VariantType::class,
                'arguments' => ['autoborna.email.model.email'],
            ],
            'autoborna.form.type.email_list' => [
                'class' => \Autoborna\EmailBundle\Form\Type\EmailListType::class,
            ],
            'autoborna.form.type.email_click_decision' => [
                'class' => \Autoborna\EmailBundle\Form\Type\EmailClickDecisionType::class,
            ],
            'autoborna.form.type.emailopen_list' => [
                'class' => \Autoborna\EmailBundle\Form\Type\EmailOpenType::class,
            ],
            'autoborna.form.type.emailsend_list' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\EmailSendType::class,
                'arguments' => ['router'],
            ],
            'autoborna.form.type.formsubmit_sendemail_admin' => [
                'class' => \Autoborna\EmailBundle\Form\Type\FormSubmitActionUserEmailType::class,
            ],
            'autoborna.email.type.email_abtest_settings' => [
                'class' => \Autoborna\EmailBundle\Form\Type\AbTestPropertiesType::class,
            ],
            'autoborna.email.type.batch_send' => [
                'class' => \Autoborna\EmailBundle\Form\Type\BatchSendType::class,
            ],
            'autoborna.form.type.emailconfig' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\ConfigType::class,
                'arguments' => [
                    'translator',
                    'autoborna.email.transport_type',
                ],
            ],
            'autoborna.form.type.coreconfig_monitored_mailboxes' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\ConfigMonitoredMailboxesType::class,
                'arguments' => [
                    'autoborna.helper.mailbox',
                ],
            ],
            'autoborna.form.type.coreconfig_monitored_email' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\ConfigMonitoredEmailType::class,
                'arguments' => 'event_dispatcher',
            ],
            'autoborna.form.type.email_dashboard_emails_in_time_widget' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\DashboardEmailsInTimeWidgetType::class,
            ],
            'autoborna.form.type.email_dashboard_sent_email_to_contacts_widget' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\DashboardSentEmailToContactsWidgetType::class,
            ],
            'autoborna.form.type.email_dashboard_most_hit_email_redirects_widget' => [
                'class'     => \Autoborna\EmailBundle\Form\Type\DashboardMostHitEmailRedirectsWidgetType::class,
            ],
            'autoborna.form.type.email_to_user' => [
                'class' => Autoborna\EmailBundle\Form\Type\EmailToUserType::class,
            ],
        ],
        'other' => [
            'autoborna.spool.delegator' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Spool\DelegatingSpool::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'swiftmailer.mailer.default.transport.real',
                ],
            ],

            // Mailers
            'autoborna.transport.spool' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Transport\SpoolTransport::class,
                'arguments' => [
                    'swiftmailer.mailer.default.transport.eventdispatcher',
                    'autoborna.spool.delegator',
                ],
            ],

            'autoborna.transport.amazon' => [
                'class'        => \Autoborna\EmailBundle\Swiftmailer\Transport\AmazonTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    '%autoborna.mailer_amazon_region%',
                    '%autoborna.mailer_amazon_other_region%',
                    '%autoborna.mailer_port%',
                    'autoborna.transport.amazon.callback',
                ],
                'methodCalls' => [
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.amazon_api' => [
                'class'        => \Autoborna\EmailBundle\Swiftmailer\Transport\AmazonApiTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    'translator',
                    'autoborna.transport.amazon.callback',
                    'monolog.logger.autoborna',
                ],
                'methodCalls' => [
                    'setRegion' => [
                        '%autoborna.mailer_amazon_region%',
                        '%autoborna.mailer_amazon_other_region%',
                    ],
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.mandrill' => [
                'class'        => 'Autoborna\EmailBundle\Swiftmailer\Transport\MandrillTransport',
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    'translator',
                    'autoborna.email.model.transport_callback',
                ],
                'methodCalls'  => [
                    'setUsername'      => ['%autoborna.mailer_user%'],
                    'setPassword'      => ['%autoborna.mailer_api_key%'],
                ],
            ],
            'autoborna.transport.mailjet' => [
                'class'        => 'Autoborna\EmailBundle\Swiftmailer\Transport\MailjetTransport',
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    'autoborna.email.model.transport_callback',
                    '%autoborna.mailer_mailjet_sandbox%',
                    '%autoborna.mailer_mailjet_sandbox_default_mail%',
                ],
                'methodCalls' => [
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.momentum' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Transport\MomentumTransport::class,
                'arguments' => [
                    'autoborna.transport.momentum.callback',
                    'autoborna.transport.momentum.facade',
                ],
                'tag'          => 'autoborna.email_transport',
                'tagArguments' => [
                    \Autoborna\EmailBundle\Model\TransportType::TRANSPORT_ALIAS => 'autoborna.email.config.mailer_transport.momentum',
                    \Autoborna\EmailBundle\Model\TransportType::FIELD_HOST      => true,
                    \Autoborna\EmailBundle\Model\TransportType::FIELD_PORT      => true,
                    \Autoborna\EmailBundle\Model\TransportType::FIELD_API_KEY   => true,
                ],
            ],
            'autoborna.transport.momentum.adapter' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Momentum\Adapter\Adapter::class,
                'arguments' => [
                    'autoborna.transport.momentum.sparkpost',
                ],
            ],
            'autoborna.transport.momentum.service.swift_message' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Momentum\Service\SwiftMessageService::class,
            ],
            'autoborna.transport.momentum.validator.swift_message' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Momentum\Validator\SwiftMessageValidator\SwiftMessageValidator::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.transport.momentum.callback' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Momentum\Callback\MomentumCallback::class,
                'arguments' => [
                    'autoborna.email.model.transport_callback',
                ],
            ],
            'autoborna.transport.momentum.facade' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Momentum\Facade\MomentumFacade::class,
                'arguments' => [
                    'autoborna.transport.momentum.adapter',
                    'autoborna.transport.momentum.service.swift_message',
                    'autoborna.transport.momentum.validator.swift_message',
                    'autoborna.transport.momentum.callback',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.transport.momentum.sparkpost' => [
                'class'     => \SparkPost\SparkPost::class,
                'factory'   => ['@autoborna.sparkpost.factory', 'create'],
                'arguments' => [
                    '%autoborna.mailer_host%',
                    '%autoborna.mailer_api_key%',
                    '%autoborna.mailer_port%',
                ],
            ],
            'autoborna.transport.sendgrid' => [
                'class'        => \Autoborna\EmailBundle\Swiftmailer\Transport\SendgridTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'methodCalls'  => [
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.sendgrid_api' => [
                'class'        => \Autoborna\EmailBundle\Swiftmailer\Transport\SendgridApiTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    'autoborna.transport.sendgrid_api.facade',
                    'autoborna.transport.sendgrid_api.calback',
                ],
            ],
            'autoborna.transport.sendgrid_api.facade' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\SendGrid\SendGridApiFacade::class,
                'arguments' => [
                    'autoborna.transport.sendgrid_api.sendgrid_wrapper',
                    'autoborna.transport.sendgrid_api.message',
                    'autoborna.transport.sendgrid_api.response',
                ],
            ],
            'autoborna.transport.sendgrid_api.mail.base' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailBase::class,
                'arguments' => [
                    'autoborna.helper.plain_text_message',
                ],
            ],
            'autoborna.transport.sendgrid_api.mail.personalization' => [
                'class' => \Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailPersonalization::class,
            ],
            'autoborna.transport.sendgrid_api.mail.metadata' => [
                'class' => \Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailMetadata::class,
            ],
            'autoborna.transport.sendgrid_api.mail.attachment' => [
                'class' => \Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailAttachment::class,
            ],
            'autoborna.transport.sendgrid_api.message' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\SendGrid\SendGridApiMessage::class,
                'arguments' => [
                    'autoborna.transport.sendgrid_api.mail.base',
                    'autoborna.transport.sendgrid_api.mail.personalization',
                    'autoborna.transport.sendgrid_api.mail.metadata',
                    'autoborna.transport.sendgrid_api.mail.attachment',
                ],
            ],
            'autoborna.transport.sendgrid_api.response' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\SendGrid\SendGridApiResponse::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.transport.sendgrid_api.sendgrid_wrapper' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\SendGrid\SendGridWrapper::class,
                'arguments' => [
                    'autoborna.transport.sendgrid_api.sendgrid',
                ],
            ],
            'autoborna.transport.sendgrid_api.sendgrid' => [
                'class'     => \SendGrid::class,
                'arguments' => [
                    '%autoborna.mailer_api_key%',
                ],
            ],
            'autoborna.transport.sendgrid_api.calback' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\SendGrid\Callback\SendGridApiCallback::class,
                'arguments' => [
                    'autoborna.email.model.transport_callback',
                ],
            ],
            'autoborna.transport.amazon.callback' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Amazon\AmazonCallback::class,
                'arguments' => [
                    'translator',
                    'monolog.logger.autoborna',
                    'autoborna.http.client',
                    'autoborna.email.model.transport_callback',
                ],
            ],
            'autoborna.transport.elasticemail' => [
                'class'        => 'Autoborna\EmailBundle\Swiftmailer\Transport\ElasticemailTransport',
                'arguments'    => [
                    'translator',
                    'monolog.logger.autoborna',
                    'autoborna.email.model.transport_callback',
                ],
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'methodCalls'  => [
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.pepipost' => [
                'class'        => \Autoborna\EmailBundle\Swiftmailer\Transport\PepipostTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    'translator',
                    'monolog.logger.autoborna',
                    'autoborna.email.model.transport_callback',
                ],
                'methodCalls' => [
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.postmark' => [
                'class'        => 'Autoborna\EmailBundle\Swiftmailer\Transport\PostmarkTransport',
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'methodCalls'  => [
                    'setUsername' => ['%autoborna.mailer_user%'],
                    'setPassword' => ['%autoborna.mailer_password%'],
                ],
            ],
            'autoborna.transport.sparkpost' => [
                'class'        => 'Autoborna\EmailBundle\Swiftmailer\Transport\SparkpostTransport',
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    '%autoborna.mailer_api_key%',
                    'translator',
                    'autoborna.email.model.transport_callback',
                    'autoborna.sparkpost.factory',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.sparkpost.factory' => [
                'class'     => \Autoborna\EmailBundle\Swiftmailer\Sparkpost\SparkpostFactory::class,
                'arguments' => [
                    'autoborna.guzzle.client',
                ],
            ],
            'autoborna.guzzle.client.factory' => [
                'class' => \Autoborna\EmailBundle\Swiftmailer\Guzzle\ClientFactory::class,
            ],
            /**
             * Needed for Sparkpost integration. Can be removed when this integration is moved to
             * its own plugin.
             */
            'autoborna.guzzle.client' => [
                'class'     => \Http\Adapter\Guzzle7\Client::class,
                'factory'   => ['@autoborna.guzzle.client.factory', 'create'],
            ],
            'autoborna.helper.mailbox' => [
                'class'     => 'Autoborna\EmailBundle\MonitoredEmail\Mailbox',
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.paths',
                ],
            ],
            'autoborna.message.search.contact' => [
                'class'     => \Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder::class,
                'arguments' => [
                    'autoborna.email.repository.stat',
                    'autoborna.lead.repository.lead',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.message.processor.bounce' => [
                'class'     => \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce::class,
                'arguments' => [
                    'swiftmailer.mailer.default.transport.real',
                    'autoborna.message.search.contact',
                    'autoborna.email.repository.stat',
                    'autoborna.lead.model.lead',
                    'translator',
                    'monolog.logger.autoborna',
                    'autoborna.lead.model.dnc',
                ],
            ],
            'autoborna.message.processor.unsubscribe' => [
                'class'     => \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscribe::class,
                'arguments' => [
                    'swiftmailer.mailer.default.transport.real',
                    'autoborna.message.search.contact',
                    'translator',
                    'monolog.logger.autoborna',
                    'autoborna.lead.model.dnc',
                ],
            ],
            'autoborna.message.processor.feedbackloop' => [
                'class'     => \Autoborna\EmailBundle\MonitoredEmail\Processor\FeedbackLoop::class,
                'arguments' => [
                    'autoborna.message.search.contact',
                    'translator',
                    'monolog.logger.autoborna',
                    'autoborna.lead.model.dnc',
                ],
            ],
            'autoborna.message.processor.replier' => [
                'class'     => \Autoborna\EmailBundle\MonitoredEmail\Processor\Reply::class,
                'arguments' => [
                    'autoborna.email.repository.stat',
                    'autoborna.message.search.contact',
                    'autoborna.lead.model.lead',
                    'event_dispatcher',
                    'monolog.logger.autoborna',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.helper.mailer' => [
                'class'     => \Autoborna\EmailBundle\Helper\MailHelper::class,
                'arguments' => [
                    'autoborna.factory',
                    'mailer',
                ],
            ],
            'autoborna.helper.plain_text_message' => [
                'class'     => \Autoborna\EmailBundle\Helper\PlainTextMessageHelper::class,
            ],
            'autoborna.validator.email' => [
                'class'     => \Autoborna\EmailBundle\Helper\EmailValidator::class,
                'arguments' => [
                    'translator',
                    'event_dispatcher',
                ],
            ],
            'autoborna.email.fetcher' => [
                'class'     => \Autoborna\EmailBundle\MonitoredEmail\Fetcher::class,
                'arguments' => [
                    'autoborna.helper.mailbox',
                    'event_dispatcher',
                    'translator',
                ],
            ],
            'autoborna.email.helper.stat' => [
                'class'     => \Autoborna\EmailBundle\Stat\StatHelper::class,
                'arguments' => [
                    'autoborna.email.repository.stat',
                ],
            ],
            'autoborna.email.helper.request.storage' => [
                'class'     => \Autoborna\EmailBundle\Helper\RequestStorageHelper::class,
                'arguments' => [
                    'autoborna.helper.cache_storage',
                ],
            ],
            'autoborna.email.helper.stats_collection' => [
                'class'     => \Autoborna\EmailBundle\Helper\StatsCollectionHelper::class,
                'arguments' => [
                    'autoborna.email.stats.helper_container',
                ],
            ],
            'autoborna.email.stats.helper_container' => [
                'class' => \Autoborna\EmailBundle\Stats\StatHelperContainer::class,
            ],
            'autoborna.email.stats.helper_bounced' => [
                'class'     => \Autoborna\EmailBundle\Stats\Helper\BouncedHelper::class,
                'arguments' => [
                    'autoborna.stats.aggregate.collector',
                    'doctrine.dbal.default_connection',
                    'autoborna.generated.columns.provider',
                    'autoborna.helper.user',
                ],
                'tag' => 'autoborna.email_stat_helper',
            ],
            'autoborna.email.stats.helper_clicked' => [
                'class'     => \Autoborna\EmailBundle\Stats\Helper\ClickedHelper::class,
                'arguments' => [
                    'autoborna.stats.aggregate.collector',
                    'doctrine.dbal.default_connection',
                    'autoborna.generated.columns.provider',
                    'autoborna.helper.user',
                ],
                'tag' => 'autoborna.email_stat_helper',
            ],
            'autoborna.email.stats.helper_failed' => [
                'class'     => \Autoborna\EmailBundle\Stats\Helper\FailedHelper::class,
                'arguments' => [
                    'autoborna.stats.aggregate.collector',
                    'doctrine.dbal.default_connection',
                    'autoborna.generated.columns.provider',
                    'autoborna.helper.user',
                ],
                'tag' => 'autoborna.email_stat_helper',
            ],
            'autoborna.email.stats.helper_opened' => [
                'class'     => \Autoborna\EmailBundle\Stats\Helper\OpenedHelper::class,
                'arguments' => [
                    'autoborna.stats.aggregate.collector',
                    'doctrine.dbal.default_connection',
                    'autoborna.generated.columns.provider',
                    'autoborna.helper.user',
                ],
                'tag' => 'autoborna.email_stat_helper',
            ],
            'autoborna.email.stats.helper_sent' => [
                'class'     => \Autoborna\EmailBundle\Stats\Helper\SentHelper::class,
                'arguments' => [
                    'autoborna.stats.aggregate.collector',
                    'doctrine.dbal.default_connection',
                    'autoborna.generated.columns.provider',
                    'autoborna.helper.user',
                ],
                'tag' => 'autoborna.email_stat_helper',
            ],
            'autoborna.email.stats.helper_unsubscribed' => [
                'class'     => \Autoborna\EmailBundle\Stats\Helper\UnsubscribedHelper::class,
                'arguments' => [
                    'autoborna.stats.aggregate.collector',
                    'doctrine.dbal.default_connection',
                    'autoborna.generated.columns.provider',
                    'autoborna.helper.user',
                ],
                'tag' => 'autoborna.email_stat_helper',
            ],
        ],
        'models' => [
            'autoborna.email.model.email' => [
                'class'     => \Autoborna\EmailBundle\Model\EmailModel::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.helper.theme',
                    'autoborna.helper.mailbox',
                    'autoborna.helper.mailer',
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.company',
                    'autoborna.page.model.trackable',
                    'autoborna.user.model.user',
                    'autoborna.channel.model.queue',
                    'autoborna.email.model.send_email_to_contacts',
                    'autoborna.tracker.device',
                    'autoborna.page.repository.redirect',
                    'autoborna.helper.cache_storage',
                    'autoborna.tracker.contact',
                    'autoborna.lead.model.dnc',
                    'autoborna.email.helper.stats_collection',
                    'autoborna.security',
                ],
            ],
            'autoborna.email.model.send_email_to_user' => [
                'class'     => \Autoborna\EmailBundle\Model\SendEmailToUser::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'event_dispatcher',
                    'autoborna.lead.validator.custom_field',
                    'autoborna.validator.email',
                ],
            ],
            'autoborna.email.model.send_email_to_contacts' => [
                'class'     => \Autoborna\EmailBundle\Model\SendEmailToContact::class,
                'arguments' => [
                    'autoborna.helper.mailer',
                    'autoborna.email.helper.stat',
                    'autoborna.lead.model.dnc',
                    'translator',
                ],
            ],
            'autoborna.email.model.transport_callback' => [
                'class'     => \Autoborna\EmailBundle\Model\TransportCallback::class,
                'arguments' => [
                    'autoborna.lead.model.dnc',
                    'autoborna.message.search.contact',
                    'autoborna.email.repository.stat',
                ],
            ],
            'autoborna.email.transport_type' => [
                'class'     => \Autoborna\EmailBundle\Model\TransportType::class,
                'arguments' => [],
            ],
        ],
        'commands' => [
            'autoborna.email.command.fetch' => [
                'class'     => \Autoborna\EmailBundle\Command\ProcessFetchEmailCommand::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.email.fetcher',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.email.command.queue' => [
                'class'     => \Autoborna\EmailBundle\Command\ProcessEmailQueueCommand::class,
                'arguments' => [
                    'swiftmailer.mailer.default.transport.real',
                    'event_dispatcher',
                    'autoborna.helper.core_parameters',
                ],
                'tag' => 'console.command',
            ],
        ],
        'validator' => [
            'autoborna.email.validator.multiple_emails_valid_validator' => [
                'class'     => \Autoborna\EmailBundle\Validator\MultipleEmailsValidValidator::class,
                'arguments' => [
                    'autoborna.validator.email',
                ],
                'tag' => 'validator.constraint_validator',
            ],
            'autoborna.email.validator.email_or_token_list_validator' => [
                'class'     => \Autoborna\EmailBundle\Validator\EmailOrEmailTokenListValidator::class,
                'arguments' => [
                    'autoborna.validator.email',
                    'autoborna.lead.validator.custom_field',
                ],
                'tag' => 'validator.constraint_validator',
            ],
        ],
        'repositories' => [
            'autoborna.email.repository.email' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\EmailBundle\Entity\Email::class,
                ],
            ],
            'autoborna.email.repository.emailReply' => [
                'class'     => \Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\EmailBundle\Entity\EmailReply::class,
                ],
            ],
            'autoborna.email.repository.stat' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\EmailBundle\Entity\Stat::class,
                ],
            ],
        ],
        'fixtures' => [
            'autoborna.email.fixture.email' => [
                'class'     => Autoborna\EmailBundle\DataFixtures\ORM\LoadEmailData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.email.model.email'],
            ],
        ],
    ],
    'parameters' => [
        'mailer_api_key'                 => null, // Api key from mail delivery provider.
        'mailer_from_name'               => 'Autoborna',
        'mailer_from_email'              => 'email@yoursite.com',
        'mailer_reply_to_email'          => null,
        'mailer_return_path'             => null,
        'mailer_transport'               => 'smtp',
        'mailer_append_tracking_pixel'   => true,
        'mailer_convert_embed_images'    => false,
        'mailer_host'                    => '',
        'mailer_port'                    => null,
        'mailer_user'                    => null,
        'mailer_password'                => null,
        'mailer_encryption'              => null, //tls or ssl,
        'mailer_auth_mode'               => null, //plain, login or cram-md5
        'mailer_amazon_region'           => 'us-east-1',
        'mailer_amazon_other_region'     => null,
        'mailer_custom_headers'          => [],
        'mailer_spool_type'              => 'memory', //memory = immediate; file = queue
        'mailer_spool_path'              => '%kernel.root_dir%/../var/spool',
        'mailer_spool_msg_limit'         => null,
        'mailer_spool_time_limit'        => null,
        'mailer_spool_recover_timeout'   => 900,
        'mailer_spool_clear_timeout'     => 1800,
        'unsubscribe_text'               => null,
        'webview_text'                   => null,
        'unsubscribe_message'            => null,
        'resubscribe_message'            => null,
        'monitored_email'                => [
            'general' => [
                'address'         => null,
                'host'            => null,
                'port'            => '993',
                'encryption'      => '/ssl',
                'user'            => null,
                'password'        => null,
                'use_attachments' => false,
            ],
            'EmailBundle_bounces' => [
                'address'           => null,
                'host'              => null,
                'port'              => '993',
                'encryption'        => '/ssl',
                'user'              => null,
                'password'          => null,
                'override_settings' => 0,
                'folder'            => null,
            ],
            'EmailBundle_unsubscribes' => [
                'address'           => null,
                'host'              => null,
                'port'              => '993',
                'encryption'        => '/ssl',
                'user'              => null,
                'password'          => null,
                'override_settings' => 0,
                'folder'            => null,
            ],
            'EmailBundle_replies' => [
                'address'           => null,
                'host'              => null,
                'port'              => '993',
                'encryption'        => '/ssl',
                'user'              => null,
                'password'          => null,
                'override_settings' => 0,
                'folder'            => null,
            ],
        ],
        'mailer_is_owner'                     => false,
        'default_signature_text'              => null,
        'email_frequency_number'              => 0,
        'email_frequency_time'                => 'DAY',
        'show_contact_preferences'            => false,
        'show_contact_frequency'              => false,
        'show_contact_pause_dates'            => false,
        'show_contact_preferred_channels'     => false,
        'show_contact_categories'             => false,
        'show_contact_segments'               => false,
        'mailer_mailjet_sandbox'              => false,
        'mailer_mailjet_sandbox_default_mail' => null,
        'disable_trackable_urls'              => false,
        'theme_email_default'                 => 'blank',
    ],
];
