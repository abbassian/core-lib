<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Processor\Bounce\Mapper;

use Autoborna\EmailBundle\MonitoredEmail\Exception\CategoryNotFound;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Definition\Category as Definition;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Mapper\Category;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Mapper\CategoryMapper;

class CategoryMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Test that the Category object is returned
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Mapper\CategoryMapper::map()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Definition\Category
     */
    public function testCategoryIsMapped()
    {
        $category = CategoryMapper::map(Definition::ANTISPAM);

        $this->assertInstanceOf(Category::class, $category);
    }

    /**
     * @testdox Test that exception is thrown if a category is not found
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Mapper\CategoryMapper::map()
     */
    public function testExceptionIsThrownWithUnrecognizedCategory()
    {
        $this->expectException(CategoryNotFound::class);

        CategoryMapper::map('bippitybop');
    }
}
