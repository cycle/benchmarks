<?php
declare(strict_types=1);

namespace Benchmarks\v2;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\CycleOrmDriver;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\v2\CycleOrmEntityFactory;
use Cycle\ORM\Mapper\PromiseMapper;
use Cycle\ORM\MapperInterface;

class UserWithCommentsPromiseMapperBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithCommentsBench
{
    public function setUp(array $bindings = []): void
    {
        $bindings[DriverInterface::class] = CycleOrmDriver::class;
        $bindings[EntityFactoryInterface::class] = CycleOrmEntityFactory::class;
        $bindings[MapperInterface::class] = PromiseMapper::class;

        parent::setUp($bindings);
    }
}