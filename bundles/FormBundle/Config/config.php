<?php

use Autoborna\FormBundle\Event\Service\FieldValueTransformer;
use Autoborna\FormBundle\EventListener\CampaignSubscriber;
use Autoborna\FormBundle\EventListener\DashboardSubscriber;
use Autoborna\FormBundle\EventListener\EmailSubscriber;
use Autoborna\FormBundle\EventListener\FormSubscriber;
use Autoborna\FormBundle\EventListener\FormValidationSubscriber;
use Autoborna\FormBundle\EventListener\LeadSubscriber;
use Autoborna\FormBundle\EventListener\PageSubscriber;
use Autoborna\FormBundle\EventListener\PointSubscriber;
use Autoborna\FormBundle\EventListener\ReportSubscriber;
use Autoborna\FormBundle\EventListener\SearchSubscriber;
use Autoborna\FormBundle\EventListener\StatsSubscriber;
use Autoborna\FormBundle\EventListener\WebhookSubscriber;
use Autoborna\FormBundle\Form\Type\CampaignEventFormFieldValueType;
use Autoborna\FormBundle\Form\Type\FieldType;
use Autoborna\FormBundle\Form\Type\FormFieldFileType;
use Autoborna\FormBundle\Form\Type\FormFieldPageBreakType;
use Autoborna\FormBundle\Form\Type\FormFieldTelType;
use Autoborna\FormBundle\Form\Type\FormListType;
use Autoborna\FormBundle\Form\Type\FormType;
use Autoborna\FormBundle\Form\Type\SubmitActionEmailType;
use Autoborna\FormBundle\Form\Type\SubmitActionRepostType;
use Autoborna\FormBundle\Helper\FormFieldHelper;
use Autoborna\FormBundle\Helper\FormUploader;
use Autoborna\FormBundle\Helper\TokenHelper;
use Autoborna\FormBundle\Model\ActionModel;
use Autoborna\FormBundle\Model\FieldModel;
use Autoborna\FormBundle\Model\FormModel;
use Autoborna\FormBundle\Model\SubmissionModel;
use Autoborna\FormBundle\Model\SubmissionResultLoader;
use Autoborna\FormBundle\Validator\Constraint\FileExtensionConstraintValidator;
use Autoborna\FormBundle\Validator\UploadFieldValidator;

return [
    'routes' => [
        'main' => [
            'autoborna_formaction_action' => [
                'path'       => '/forms/action/{objectAction}/{objectId}',
                'controller' => 'AutobornaFormBundle:Action:execute',
            ],
            'autoborna_formfield_action' => [
                'path'       => '/forms/field/{objectAction}/{objectId}',
                'controller' => 'AutobornaFormBundle:Field:execute',
            ],
            'autoborna_form_index' => [
                'path'       => '/forms/{page}',
                'controller' => 'AutobornaFormBundle:Form:index',
            ],
            'autoborna_form_results' => [
                'path'       => '/forms/results/{objectId}/{page}',
                'controller' => 'AutobornaFormBundle:Result:index',
            ],
            'autoborna_form_export' => [
                'path'       => '/forms/results/{objectId}/export/{format}',
                'controller' => 'AutobornaFormBundle:Result:export',
                'defaults'   => [
                    'format' => 'csv',
                ],
            ],
            'autoborna_form_results_action' => [
                'path'       => '/forms/results/{formId}/{objectAction}/{objectId}',
                'controller' => 'AutobornaFormBundle:Result:execute',
                'defaults'   => [
                    'objectId' => 0,
                ],
            ],
            'autoborna_form_action' => [
                'path'       => '/forms/{objectAction}/{objectId}',
                'controller' => 'AutobornaFormBundle:Form:execute',
            ],
        ],
        'api' => [
            'autoborna_api_formstandard' => [
                'standard_entity' => true,
                'name'            => 'forms',
                'path'            => '/forms',
                'controller'      => 'AutobornaFormBundle:Api\FormApi',
            ],
            'autoborna_api_formresults' => [
                'path'       => '/forms/{formId}/submissions',
                'controller' => 'AutobornaFormBundle:Api\SubmissionApi:getEntities',
            ],
            'autoborna_api_formresult' => [
                'path'       => '/forms/{formId}/submissions/{submissionId}',
                'controller' => 'AutobornaFormBundle:Api\SubmissionApi:getEntity',
            ],
            'autoborna_api_contactformresults' => [
                'path'       => '/forms/{formId}/submissions/contact/{contactId}',
                'controller' => 'AutobornaFormBundle:Api\SubmissionApi:getEntitiesForContact',
            ],
            'autoborna_api_formdeletefields' => [
                'path'       => '/forms/{formId}/fields/delete',
                'controller' => 'AutobornaFormBundle:Api\FormApi:deleteFields',
                'method'     => 'DELETE',
            ],
            'autoborna_api_formdeleteactions' => [
                'path'       => '/forms/{formId}/actions/delete',
                'controller' => 'AutobornaFormBundle:Api\FormApi:deleteActions',
                'method'     => 'DELETE',
            ],
        ],
        'public' => [
            'autoborna_form_file_download' => [
                'path'       => '/forms/results/file/{submissionId}/{field}',
                'controller' => 'AutobornaFormBundle:Result:downloadFile',
            ],
            'autoborna_form_postresults' => [
                'path'       => '/form/submit',
                'controller' => 'AutobornaFormBundle:Public:submit',
            ],
            'autoborna_form_generateform' => [
                'path'       => '/form/generate.js',
                'controller' => 'AutobornaFormBundle:Public:generate',
            ],
            'autoborna_form_postmessage' => [
                'path'       => '/form/message',
                'controller' => 'AutobornaFormBundle:Public:message',
            ],
            'autoborna_form_preview' => [
                'path'       => '/form/{id}',
                'controller' => 'AutobornaFormBundle:Public:preview',
                'defaults'   => [
                    'id' => '0',
                ],
            ],
            'autoborna_form_embed' => [
                'path'       => '/form/embed/{id}',
                'controller' => 'AutobornaFormBundle:Public:embed',
            ],
            'autoborna_form_postresults_ajax' => [
                'path'       => '/form/submit/ajax',
                'controller' => 'AutobornaFormBundle:Ajax:submit',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'items' => [
                'autoborna.form.forms' => [
                    'route'    => 'autoborna_form_index',
                    'access'   => ['form:forms:viewown', 'form:forms:viewother'],
                    'parent'   => 'autoborna.core.components',
                    'priority' => 200,
                ],
            ],
        ],
    ],

    'categories' => [
        'form' => null,
    ],

    'services' => [
        'events' => [
            'autoborna.core.configbundle.subscriber.form' => [
                'class'     => \Autoborna\FormBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.form.subscriber' => [
                'class'     => FormSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.helper.mailer',
                    'autoborna.helper.core_parameters',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.form.validation.subscriber' => [
                'class'     => FormValidationSubscriber::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.form.pagebundle.subscriber' => [
                'class'     => PageSubscriber::class,
                'arguments' => [
                    'autoborna.form.model.form',
                    'autoborna.helper.token_builder.factory',
                    'translator',
                    'autoborna.security',
                ],
            ],
            'autoborna.form.pointbundle.subscriber' => [
                'class'     => PointSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.point',
                ],
            ],
            'autoborna.form.reportbundle.subscriber' => [
                'class'     => ReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.company_report_data',
                    'autoborna.form.repository.submission',
                ],
            ],
            'autoborna.form.campaignbundle.subscriber' => [
                'class'     => CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.form.model.form',
                    'autoborna.form.model.submission',
                    'autoborna.campaign.executioner.realtime',
                    'autoborna.helper.form.field_helper',
                ],
            ],
            'autoborna.form.leadbundle.subscriber' => [
                'class'     => LeadSubscriber::class,
                'arguments' => [
                    'autoborna.form.model.form',
                    'autoborna.page.model.page',
                    'autoborna.form.repository.submission',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.form.emailbundle.subscriber' => [
                'class' => EmailSubscriber::class,
            ],
            'autoborna.form.search.subscriber' => [
                'class'     => SearchSubscriber::class,
                'arguments' => [
                    'autoborna.helper.user',
                    'autoborna.form.model.form',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.form.webhook.subscriber' => [
                'class'     => WebhookSubscriber::class,
                'arguments' => [
                    'autoborna.webhook.model.webhook',
                ],
            ],
            'autoborna.form.dashboard.subscriber' => [
                'class'     => DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.form.model.submission',
                    'autoborna.form.model.form',
                    'router',
                ],
            ],
            'autoborna.form.stats.subscriber' => [
                'class'     => StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.form.subscriber.determine_winner' => [
                'class'     => \Autoborna\FormBundle\EventListener\DetermineWinnerSubscriber::class,
                'arguments' => [
                    'autoborna.form.repository.submission',
                    'translator',
                ],
            ],
            'autoborna.form.conditional.subscriber' => [
                'class'     => \Autoborna\FormBundle\EventListener\FormConditionalSubscriber::class,
                'arguments' => [
                    'autoborna.form.model.form',
                    'autoborna.form.model.field',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.formconfig' => [
                'class'     => \Autoborna\FormBundle\Form\Type\ConfigFormType::class,
                    'alias' => 'formconfig',
            ],
            'autoborna.form.type.form' => [
                'class'     => FormType::class,
                'arguments' => [
                    'autoborna.security',
                ],
            ],
            'autoborna.form.type.field' => [
                'class'       => FieldType::class,
                'arguments'   => [
                    'translator',
                ],
                'methodCalls' => [
                    'setFieldModel' => ['autoborna.form.model.field'],
                    'setFormModel'  => ['autoborna.form.model.form'],
                ],
            ],
            'autoborna.form.type.field_propertypagebreak' => [
                'class'     => FormFieldPageBreakType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.field_propertytel' => [
                'class'     => FormFieldTelType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.field_propertyemail' => [
                'class'     => \Autoborna\FormBundle\Form\Type\FormFieldEmailType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.field_propertyfile' => [
                'class'     => FormFieldFileType::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'translator',
                ],
            ],
            'autoborna.form.type.form_list' => [
                'class'     => FormListType::class,
                'arguments' => [
                    'autoborna.security',
                    'autoborna.form.model.form',
                    'autoborna.helper.user',
                ],
            ],
            'autoborna.form.type.campaignevent_form_field_value' => [
                'class'     => CampaignEventFormFieldValueType::class,
                'arguments' => [
                    'autoborna.form.model.form',
                ],
            ],
            'autoborna.form.type.form_submitaction_sendemail' => [
                'class'       => SubmitActionEmailType::class,
                'arguments'   => [
                    'translator',
                    'autoborna.helper.core_parameters',
                ],
                'methodCalls' => [
                    'setFieldModel' => ['autoborna.form.model.field'],
                    'setFormModel'  => ['autoborna.form.model.form'],
                ],
            ],
            'autoborna.form.type.form_submitaction_repost' => [
                'class'       => SubmitActionRepostType::class,
                'methodCalls' => [
                    'setFieldModel' => ['autoborna.form.model.field'],
                    'setFormModel'  => ['autoborna.form.model.form'],
                ],
            ],
            'autoborna.form.type.field.conditional' => [
                'class'       => \Autoborna\FormBundle\Form\Type\FormFieldConditionType::class,
                'arguments'   => [
                    'autoborna.form.model.field',
                    'autoborna.form.helper.properties.accessor',
                ],
            ],
        ],
        'models' => [
            'autoborna.form.model.action' => [
                'class' => ActionModel::class,
            ],
            'autoborna.form.model.field' => [
                'class'     => FieldModel::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                ],
            ],
            'autoborna.form.model.form' => [
                'class'     => FormModel::class,
                'arguments' => [
                    'request_stack',
                    'autoborna.helper.templating',
                    'autoborna.helper.theme',
                    'autoborna.form.model.action',
                    'autoborna.form.model.field',
                    'autoborna.helper.form.field_helper',
                    'autoborna.lead.model.field',
                    'autoborna.form.helper.form_uploader',
                    'autoborna.tracker.contact',
                    'autoborna.schema.helper.column',
                    'autoborna.schema.helper.table',
                ],
            ],
            'autoborna.form.model.submission' => [
                'class'     => SubmissionModel::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.helper.templating',
                    'autoborna.form.model.form',
                    'autoborna.page.model.page',
                    'autoborna.lead.model.lead',
                    'autoborna.campaign.model.campaign',
                    'autoborna.campaign.membership.manager',
                    'autoborna.lead.model.field',
                    'autoborna.lead.model.company',
                    'autoborna.helper.form.field_helper',
                    'autoborna.form.validator.upload_field_validator',
                    'autoborna.form.helper.form_uploader',
                    'autoborna.lead.service.device_tracking_service',
                    'autoborna.form.service.field.value.transformer',
                    'autoborna.helper.template.date',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.form.model.submission_result_loader' => [
                'class'     => SubmissionResultLoader::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.form.repository.form' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => \Autoborna\FormBundle\Entity\Form::class,
            ],
            'autoborna.form.repository.submission' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => \Autoborna\FormBundle\Entity\Submission::class,
            ],
        ],
        'other' => [
            'autoborna.helper.form.field_helper' => [
                'class'     => FormFieldHelper::class,
                'arguments' => [
                    'translator',
                    'validator',
                ],
            ],
            'autoborna.form.helper.form_uploader' => [
                'class'     => FormUploader::class,
                'arguments' => [
                    'autoborna.helper.file_uploader',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.form.helper.token' => [
                'class'     => TokenHelper::class,
                'arguments' => [
                    'autoborna.form.model.form',
                    'autoborna.security',
                ],
            ],
            'autoborna.form.service.field.value.transformer' => [
                'class'     => FieldValueTransformer::class,
                'arguments' => [
                    'router',
                ],
            ],
            'autoborna.form.helper.properties.accessor' => [
                'class'     => \Autoborna\FormBundle\Helper\PropertiesAccessor::class,
                'arguments' => [
                    'autoborna.form.model.form',
                ],
            ],
        ],
        'validator' => [
            'autoborna.form.validator.upload_field_validator' => [
                'class'     => UploadFieldValidator::class,
                'arguments' => [
                    'autoborna.core.validator.file_upload',
                ],
            ],
            'autoborna.form.validator.constraint.file_extension_constraint_validator' => [
                'class'     => FileExtensionConstraintValidator::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
                'tags' => [
                    'name'  => 'validator.constraint_validator',
                    'alias' => 'file_extension_constraint_validator',
                ],
            ],
        ],
        'fixtures' => [
            'autoborna.form.fixture.form' => [
                'class'     => \Autoborna\FormBundle\DataFixtures\ORM\LoadFormData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.form.model.form', 'autoborna.form.model.field', 'autoborna.form.model.action'],
            ],
            'autoborna.form.fixture.form_result' => [
                'class'     => \Autoborna\FormBundle\DataFixtures\ORM\LoadFormResultData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.page.model.page', 'autoborna.form.model.submission'],
            ],
        ],
    ],

    'parameters' => [
        'form_upload_dir'        => '%kernel.root_dir%/../media/files/form',
        'blacklisted_extensions' => ['php', 'sh'],
        'do_not_submit_emails'   => [],
    ],
];
