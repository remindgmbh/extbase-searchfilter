<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter\Tests\Unit;

use Remind\RmndSearchFilter\AbstractSearchFilterStrategy;
use TypeError;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Description of AbstractSearchFilterStrategyTest
 */
final class AbstractSearchFilterStrategyTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetSingletonInstances = true;
    }

    public function testSetAndGetFilter(): void
    {
        $subject = $this->getMockForAbstractClass(AbstractSearchFilterStrategy::class);

        $filter = [
            'test' => []
        ];

        $subject->setFilter($filter);

        $this->assertEquals($filter, $subject->getFilter());
    }

    public function testSetFilterError(): void
    {
        $subject = $this->getMockForAbstractClass(AbstractSearchFilterStrategy::class);

        $this->expectException(TypeError::class);
        $subject->setFilter(null);
    }
}
