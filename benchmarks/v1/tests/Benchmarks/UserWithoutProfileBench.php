<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\v1\Benchmarks;

use Cycle\Benchmarks\v1\CycleOrmEntityFactory;

class UserWithoutProfileBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileBench
{
    public function getEntityFactoryClass(): string
    {
        return CycleOrmEntityFactory::class;
    }
}
