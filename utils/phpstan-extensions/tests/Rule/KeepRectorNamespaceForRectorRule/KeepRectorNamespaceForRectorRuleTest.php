<?php

declare(strict_types=1);

namespace Rector\PHPStanExtensions\Tests\Rule\KeepRectorNamespaceForRectorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Rector\PHPStanExtensions\Rule\KeepRectorNamespaceForRectorRule;

final class KeepRectorNamespaceForRectorRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/Rector/ClassInCorrectNamespaceRector.php', []];

        $errorMessage = sprintf(KeepRectorNamespaceForRectorRule::ERROR_MESSAGE, 'WrongClass');
        yield [__DIR__ . '/Source/Rector/WrongClass.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return new KeepRectorNamespaceForRectorRule();
    }
}
