<?php

declare(strict_types=1);

namespace Autoborna\FormBundle\Tests\Controller;

use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\FormBundle\FormEvents;
use Autoborna\LeadBundle\Entity\Lead;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CampaignSubscriberFunctionalTest extends AutobornaMysqlTestCase
{
    protected $useCleanupRollback = false;

    /**
     * @dataProvider valueProvider
     */
    public function testComparingFormSubmissionValues(string $valueToCompare, string $submittedValue, bool $result): void
    {
        $formPayload = [
            'name'     => 'Test Form',
            'formType' => 'campaign',
            'fields'   => [
                [
                    'label'      => 'Select A',
                    'alias'      => 'select_a',
                    'type'       => 'select',
                    'properties' => [
                        'list' => [
                            'list' => [
                                [
                                    'label' => $valueToCompare,
                                    'value' => $valueToCompare,
                                ],
                                [
                                    'label' => $submittedValue,
                                    'value' => $submittedValue,
                                ],
                            ],
                        ],
                        'multiple' => false,
                    ],
                ], [
                    'label'     => 'Email',
                    'alias'     => 'email',
                    'type'      => 'email',
                    'leadField' => 'email',
                ], [
                    'label' => 'Submit',
                    'alias' => 'submit',
                    'type'  => 'button',
                ],
            ],
        ];

        // Creating the form via API so it would create the submission table.
        $this->client->request('POST', '/api/forms/new', $formPayload);
        $clientResponse = $this->client->getResponse();

        $this->assertSame(Response::HTTP_CREATED, $clientResponse->getStatusCode(), $clientResponse->getContent());
        $response = json_decode($clientResponse->getContent(), true);
        $formId   = $response['form']['id'];

        // Submitting the form.
        $crawler    = $this->client->request(Request::METHOD_GET, "/form/{$formId}");
        $saveButton = $crawler->selectButton('autobornaform[submit]');
        $form       = $saveButton->form();
        $form['autobornaform[select_a]']->setValue($submittedValue);
        $form['autobornaform[email]']->setValue('testing@ampersand.select');

        $this->client->submit($form);
        Assert::assertTrue($this->client->getResponse()->isOk(), $this->client->getResponse()->getContent());

        // Create some necessary entities.
        $campaignEvent = new Event();
        $campaignEvent->setName('Test Event');
        $campaignEvent->setType('form.field_value');
        $campaignEvent->setEventType('condition');
        $campaignEvent->setProperties(
            [
                'form'     => $formId,
                'field'    => 'select_a',
                'value'    => $valueToCompare,
                'operator' => '=',
            ]
        );

        $campaign = new Campaign();
        $campaign->setName('Test Campaign');
        $campaign->addEvent(1, $campaignEvent);

        $campaignEvent->setCampaign($campaign);

        $this->em->persist($campaignEvent);
        $this->em->persist($campaign);
        $this->em->flush();
        $this->em->clear();

        $contact = $this->em->getRepository(Lead::class)->findOneBy(['email' => 'testing@ampersand.select']);
        $event   = new CampaignExecutionEvent(
            [
                'lead'            => $contact,
                'event'           => $campaignEvent,
                'eventDetails'    => null,
                'systemTriggered' => false,
                'eventSettings'   => [],
            ],
            null
        );

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::$container->get('event_dispatcher');

        $dispatcher->dispatch(FormEvents::ON_CAMPAIGN_TRIGGER_CONDITION, $event);

        Assert::assertSame('form', $event->getChannel());
        Assert::assertSame($result, $event->getResult());
    }

    public function valueProvider(): iterable
    {
        yield [
            'test & test',
            'test & test',
            true,
        ];

        yield [
            'test & test',
            'unicorn',
            false,
        ];
    }

    protected function tearDown(): void
    {
        $tablePrefix = self::$container->getParameter('autoborna.db_table_prefix');

        parent::tearDown();

        if ($this->connection->getSchemaManager()->tablesExist("{$tablePrefix}form_results_1_test_form")) {
            $this->connection->executeQuery("DROP TABLE {$tablePrefix}form_results_1_test_form");
        }
    }
}
