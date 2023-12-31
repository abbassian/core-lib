<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\Provider;

use Autoborna\LeadBundle\Event\FieldOperatorsEvent;
use Autoborna\LeadBundle\Event\TypeOperatorsEvent;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Provider\FilterOperatorProviderInterface;
use Autoborna\LeadBundle\Provider\TypeOperatorProvider;
use Autoborna\LeadBundle\Segment\OperatorOptions;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TypeOperatorProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MockObject|FilterOperatorProviderInterface
     */
    private $filterOperatorPovider;

    /**
     * @var TypeOperatorProvider
     */
    private $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher            = $this->createMock(EventDispatcherInterface::class);
        $this->filterOperatorPovider = $this->createMock(FilterOperatorProviderInterface::class);
        $this->provider              = new TypeOperatorProvider(
            $this->dispatcher,
            $this->filterOperatorPovider
        );
    }

    public function testGetOperatorsIncluding(): void
    {
        $this->filterOperatorPovider->expects($this->any())
            ->method('getAllOperators')
            ->willReturn([
                OperatorOptions::EQUAL_TO => [
                    'label'        => 'equals',
                    'expr'         => 'eq',
                    'negagte_expr' => 'neq',
                ],
                OperatorOptions::NOT_EQUAL_TO => [
                    'label'        => 'not equal',
                    'expr'         => 'neq',
                    'negagte_expr' => 'eq',
                ],
            ]);

        $this->assertSame(
            ['equals' => OperatorOptions::EQUAL_TO],
            $this->provider->getOperatorsIncluding([OperatorOptions::EQUAL_TO])
        );
    }

    public function testGetOperatorsExcluding(): void
    {
        $this->filterOperatorPovider->expects($this->any())
            ->method('getAllOperators')
            ->willReturn([
                OperatorOptions::EQUAL_TO => [
                    'label'        => 'equals',
                    'expr'         => 'eq',
                    'negagte_expr' => 'neq',
                ],
                OperatorOptions::NOT_EQUAL_TO => [
                    'label'        => 'not equal',
                    'expr'         => 'neq',
                    'negagte_expr' => 'eq',
                ],
            ]);

        $this->assertNotContains(
            OperatorOptions::EQUAL_TO,
            $this->provider->getOperatorsExcluding([OperatorOptions::EQUAL_TO])
        );
    }

    public function testGetOperatorsForFieldType(): void
    {
        $this->filterOperatorPovider->expects($this->any())
            ->method('getAllOperators')
            ->willReturn([
                OperatorOptions::EQUAL_TO => [
                    'label'        => 'equals',
                    'expr'         => 'eq',
                    'negagte_expr' => 'neq',
                ],
                OperatorOptions::NOT_EQUAL_TO => [
                    'label'        => 'not equal',
                    'expr'         => 'neq',
                    'negagte_expr' => 'eq',
                ],
                OperatorOptions::IN => [
                    'label'        => 'in',
                    'expr'         => 'in',
                    'negagte_expr' => 'notIn',
                ],
            ]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                LeadEvents::COLLECT_OPERATORS_FOR_FIELD_TYPE,
                $this->callback(function (TypeOperatorsEvent $event) {
                    // Emulate a subscriber.
                    $event->setOperatorsForFieldType('text', [
                        'include' => [
                            OperatorOptions::EQUAL_TO,
                            OperatorOptions::NOT_EQUAL_TO,
                        ],
                    ]);

                    return true;
                })
            );

        $this->assertSame(
            [
                'equals'    => OperatorOptions::EQUAL_TO,
                'not equal' => OperatorOptions::NOT_EQUAL_TO,
            ],
            $this->provider->getOperatorsForFieldType('text')
        );
    }

    public function testGetOperatorsForSpecificField(): void
    {
        $this->filterOperatorPovider->expects($this->any())
            ->method('getAllOperators')
            ->willReturn([
                OperatorOptions::EQUAL_TO => [
                    'label'        => 'equals',
                    'expr'         => 'eq',
                    'negagte_expr' => 'neq',
                ],
                OperatorOptions::NOT_EQUAL_TO => [
                    'label'        => 'not equal',
                    'expr'         => 'neq',
                    'negagte_expr' => 'eq',
                ],
                OperatorOptions::STARTS_WITH => [
                    'label'        => 'starts with',
                    'expr'         => 'startsWith',
                    'negagte_expr' => 'notStartsWith',
                ],
            ]);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [
                    LeadEvents::COLLECT_OPERATORS_FOR_FIELD_TYPE,
                    $this->callback(function (TypeOperatorsEvent $event) {
                        // Emulate a subscriber.
                        $event->setOperatorsForFieldType('text', [
                            'include' => [
                                OperatorOptions::EQUAL_TO,
                                OperatorOptions::NOT_EQUAL_TO,
                            ],
                        ]);

                        return true;
                    }),
                ],
                [
                    LeadEvents::COLLECT_OPERATORS_FOR_FIELD,
                    $this->callback(function (FieldOperatorsEvent $event) {
                        // Emulate a subscriber.
                        $this->assertSame('text', $event->getType());
                        $this->assertSame('email', $event->getField());

                        // This is the important stuff. The Starts with opearator will be added.
                        $event->addOperator(OperatorOptions::STARTS_WITH);

                        return true;
                    }),
                ]
            );

        $this->assertSame(
            [
                'equals'      => OperatorOptions::EQUAL_TO,
                'not equal'   => OperatorOptions::NOT_EQUAL_TO,
                'starts with' => OperatorOptions::STARTS_WITH,
            ],
            $this->provider->getOperatorsForField('text', 'email')
        );
    }
}
