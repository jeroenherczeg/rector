<?php

declare(strict_types=1);

namespace Rector\MagicDisclosure\Tests\Rector\MethodCall\DefluentMethodCallRector\Source;

final class FluentInterfaceClass extends InterFluentInterfaceClass
{
    public function someFunction(): self
    {
        return $this;
    }

    public function otherFunction(): self
    {
        return $this;
    }
}
