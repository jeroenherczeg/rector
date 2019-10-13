<?php

declare(strict_types=1);

namespace Rector\Php74\Tests\Rector\Function_\ReservedFnFunctionRector;

use Iterator;
use PhpParser\Parser\Tokens;
use Rector\Php74\Rector\Function_\ReservedFnFunctionRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class ReservedFnFunctionRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        if (defined(Tokens::class . '::T_FN')) {
            $this->markTestSkipped('fn is reserved name in PHP 7.4');
        }

        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc'];
    }

    protected function getRectorClass(): string
    {
        return ReservedFnFunctionRector::class;
    }
}
