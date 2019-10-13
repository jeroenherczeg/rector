<?php

declare(strict_types=1);

namespace Rector\ElasticSearchDSL\Tests\Rector\MethodCall\MigrateFilterToQueryRector;

use Iterator;
use Rector\ElasticSearchDSL\Rector\MethodCall\MigrateFilterToQueryRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class MigrateFilterToQueryRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc'];
    }

    protected function getRectorClass(): string
    {
        return MigrateFilterToQueryRector::class;
    }
}
