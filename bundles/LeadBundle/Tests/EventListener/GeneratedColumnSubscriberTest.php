<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\EventListener;

use Autoborna\CoreBundle\Doctrine\GeneratedColumn\GeneratedColumn;
use Autoborna\CoreBundle\Event\GeneratedColumnsEvent;
use Autoborna\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Autoborna\LeadBundle\EventListener\GeneratedColumnSubscriber;
use Autoborna\LeadBundle\Model\ListModel;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class GeneratedColumnSubscriberTest extends TestCase
{
    /**
     * @var MockObject&TranslatorInterface
     */
    private $translator;

    private GeneratedColumnSubscriber $generatedColumnSubscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $segmentModel = new class() extends ListModel {
            public function __construct()
            {
            }
        };

        $this->translator                = $this->createMock(TranslatorInterface::class);
        $this->generatedColumnSubscriber = new GeneratedColumnSubscriber($segmentModel, $this->translator);
    }

    public function testInGeneratedColumnsBuild(): void
    {
        $event = new GeneratedColumnsEvent();

        $this->generatedColumnSubscriber->onGeneratedColumnsBuild($event);

        /** @var GeneratedColumn $generatedColumn */
        $generatedColumn = $event->getGeneratedColumns()->current();

        Assert::assertSame(MAUTIC_TABLE_PREFIX.'leads', $generatedColumn->getTableName());
        Assert::assertSame('generated_email_domain', $generatedColumn->getColumnName());
        Assert::assertSame('VARCHAR(255) AS (SUBSTRING(email, LOCATE("@", email) + 1)) COMMENT \'(DC2Type:generated)\'', $generatedColumn->getColumnDefinition());
    }

    public function testOnGenerateSegmentFilters(): void
    {
        $event = new LeadListFiltersChoicesEvent(
            [],
            [],
            $this->translator,
            new Request()
        );

        $this->translator->method('trans')
            ->with('autoborna.email.segment.choice.generated_email_domain')
            ->willReturn('translated string');

        $this->generatedColumnSubscriber->onGenerateSegmentFilters($event);

        Assert::assertSame(
            [
                'label'      => 'translated string',
                'properties' => ['type' => 'text'],
                'operators'  => [
                    'autoborna.lead.list.form.operator.equals'     => '=',
                    'autoborna.lead.list.form.operator.notequals'  => '!=',
                    'autoborna.lead.list.form.operator.isempty'    => 'empty',
                    'autoborna.lead.list.form.operator.isnotempty' => '!empty',
                    'autoborna.lead.list.form.operator.islike'     => 'like',
                    'autoborna.lead.list.form.operator.isnotlike'  => '!like',
                    'autoborna.lead.list.form.operator.regexp'     => 'regexp',
                    'autoborna.lead.list.form.operator.notregexp'  => '!regexp',
                    'autoborna.core.operator.starts.with'          => 'startsWith',
                    'autoborna.core.operator.ends.with'            => 'endsWith',
                    'autoborna.core.operator.contains'             => 'contains',
                ],
                'object' => 'lead',
            ],
            $event->getChoices()['lead']['generated_email_domain']
        );
    }
}
