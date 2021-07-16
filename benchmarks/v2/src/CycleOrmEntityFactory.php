<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\v2;

use Cycle\Benchmarks\Base\BaseCycleOrmEntityFactory;

class CycleOrmEntityFactory extends BaseCycleOrmEntityFactory
{
    public function create(string $class): object
    {
        return $this->orm->getMapper($class)->init([]);
    }
}
