<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\EntityFactory;

class CycleORMV2EntityFactory extends BaseCycleOrmEntityFactory
{
    public function create(string $class): object
    {
        return $this->orm->getMapper($class)->init([]);
    }
}
