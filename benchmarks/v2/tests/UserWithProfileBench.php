<?php
declare(strict_types=1);

namespace Benchmarks\v2;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\CycleOrmDriver;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\v2\CycleOrmEntityFactory;

class UserWithProfileBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithProfileBench
{
    public function setUp(): void
    {
        $this->getContainer()->bind(DriverInterface::class, CycleOrmDriver::class);
        $this->getContainer()->bind(EntityFactoryInterface::class, CycleOrmEntityFactory::class);

        parent::setUp();
    }
}
