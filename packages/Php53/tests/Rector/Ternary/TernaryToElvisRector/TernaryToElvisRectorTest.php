<?php

declare(strict_types=1);

namespace Rector\Php53\Tests\Rector\Ternary\TernaryToElvisRector;

use Iterator;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class TernaryToElvisRectorTest extends AbstractRectorTestCase
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
        yield [__DIR__ . '/Fixture/yolo.php.inc'];
    }

    protected function getRectorClass(): string
    {
        return TernaryToElvisRector::class;
    }
}
