<?php

declare(strict_types=1);

namespace Rector\Nette\Tests\Rector\Identical\StartsWithFunctionToNetteUtilsStringsRector;

use Iterator;
use Rector\Nette\Rector\Identical\StartsWithFunctionToNetteUtilsStringsRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class StartsWithFunctionToNetteUtilsStringsRectorTest extends AbstractRectorTestCase
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
        return StartsWithFunctionToNetteUtilsStringsRector::class;
    }
}
