<?php

declare(strict_types=1);

namespace Rector\Php74\Tests\Rector\Double\RealToFloatTypeCastRector;

use Iterator;
use Rector\Php74\Rector\Double\RealToFloatTypeCastRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class RealToFloatTypeCastRectorTest extends AbstractRectorTestCase
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
        return RealToFloatTypeCastRector::class;
    }
}
