<?php

declare(strict_types=1);

namespace Autoborna\ReportBundle\Tests\Helper;

use Autoborna\ReportBundle\Helper\ReportHelper;
use PHPUnit\Framework\TestCase;

final class ReportHelperTest extends TestCase
{
    /**
     * @var ReportHelper
     */
    private $reportHelper;

    protected function setUp(): void
    {
        $this->reportHelper = new ReportHelper();
    }

    public function testGetStandardColumnsMethodReturnsCorrectColumns(): void
    {
        $columns = $this->reportHelper->getStandardColumns('somePrefix');

        $expectedColumnns = [
            'somePrefixid' => [
                    'label' => 'autoborna.core.id',
                    'type'  => 'int',
                    'alias' => 'somePrefixid',
                ],
            'somePrefixname' => [
                    'label' => 'autoborna.core.name',
                    'type'  => 'string',
                    'alias' => 'somePrefixname',
                ],
            'somePrefixcreated_by_user' => [
                    'label' => 'autoborna.core.createdby',
                    'type'  => 'string',
                    'alias' => 'somePrefixcreated_by_user',
                ],
            'somePrefixdate_added' => [
                    'label' => 'autoborna.report.field.date_added',
                    'type'  => 'datetime',
                    'alias' => 'somePrefixdate_added',
                ],
            'somePrefixmodified_by_user' => [
                    'label' => 'autoborna.report.field.modified_by_user',
                    'type'  => 'string',
                    'alias' => 'somePrefixmodified_by_user',
                ],
            'somePrefixdate_modified' => [
                    'label' => 'autoborna.report.field.date_modified',
                    'type'  => 'datetime',
                    'alias' => 'somePrefixdate_modified',
                ],
            'somePrefixdescription' => [
                    'label' => 'autoborna.core.description',
                    'type'  => 'string',
                    'alias' => 'somePrefixdescription',
                ],
            'somePrefixpublish_up' => [
                    'label' => 'autoborna.report.field.publish_up',
                    'type'  => 'datetime',
                    'alias' => 'somePrefixpublish_up',
                ],
            'somePrefixpublish_down' => [
                    'label' => 'autoborna.report.field.publish_down',
                    'type'  => 'datetime',
                    'alias' => 'somePrefixpublish_down',
                ],
            'somePrefixis_published' => [
                    'label' => 'autoborna.report.field.is_published',
                    'type'  => 'bool',
                    'alias' => 'somePrefixis_published',
                ],
        ];

        $this->assertEquals($expectedColumnns, $columns);
    }
}
