<?php
declare(strict_types=1);

namespace Benchmarks\v2;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\CycleOrmDriver;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\v2\CycleOrmEntityFactory;
use Cycle\ORM\Mapper\PromiseMapper;
use Cycle\ORM\MapperInterface;

class UserWithProfilePromiseMapperBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithProfileBench
{
    public function setUp(array $bindings = []): void
    {
        $bindings[DriverInterface::class] = CycleOrmDriver::class;
        $bindings[EntityFactoryInterface::class] = CycleOrmEntityFactory::class;
        $bindings[MapperInterface::class] = PromiseMapper::class;

        parent::setUp($bindings);
    }
}
