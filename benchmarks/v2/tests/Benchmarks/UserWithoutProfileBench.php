<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\v2\Benchmarks;

use Cycle\Benchmarks\v2\CycleOrmEntityFactory;

class UserWithoutProfileBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileBench
{
    public function getEntityFactoryClass(): string
    {
        return CycleOrmEntityFactory::class;
    }
}
