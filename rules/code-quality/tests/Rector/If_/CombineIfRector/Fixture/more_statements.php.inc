<?php

namespace Rector\CodeQuality\Tests\Rector\If_\CombineIfRector\MoreStatements;

class SomeClass {
    public function run()
    {
        if ($cond1) {
            if ($cond2) {
                return 'foo';
            }
            return 'bar';
        }
    }
}

?>
