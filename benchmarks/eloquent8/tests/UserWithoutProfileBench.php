<?php
declare(strict_types=1);

namespace Benchmarks\Eloquent;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserWithoutProfileConfigurator;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Eloquent\EloquentDriver;
use Cycle\Benchmarks\Eloquent\EloquentEntityFactory;

class UserWithoutProfileBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileBench
{
    public function setUp(): void
    {
        $this->getContainer()->bind(DriverInterface::class, EloquentDriver::class);
        $this->getContainer()->bind(EntityFactoryInterface::class, EloquentEntityFactory::class);
        $this->getContainer()->bind(ConfiguratorInterface::class, UserWithoutProfileConfigurator::class);

        parent::setUp();
    }
}
