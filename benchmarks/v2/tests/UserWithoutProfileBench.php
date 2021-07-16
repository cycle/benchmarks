<?php
declare(strict_types=1);

namespace Benchmarks\v2;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserWithoutProfileConfigurator;
use Cycle\Benchmarks\Base\DatabaseDrivers\CycleOrmDriver;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\v2\CycleOrmEntityFactory;

class UserWithoutProfileBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileBench
{
    public function setUp(): void
    {
        $this->getContainer()->bind(DriverInterface::class, CycleOrmDriver::class);
        $this->getContainer()->bind(EntityFactoryInterface::class, CycleOrmEntityFactory::class);
        $this->getContainer()->bind(ConfiguratorInterface::class, UserWithoutProfileConfigurator::class);

        parent::setUp();
    }
}
