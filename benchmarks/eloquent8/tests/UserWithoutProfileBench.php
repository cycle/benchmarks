<?php
declare(strict_types=1);

namespace Benchmarks\Eloquent;

use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserWithoutProfileConfigurator;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Eloquent\EloquentDriver;

class UserWithoutProfileBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileBench
{
    public function setUp(): void
    {
        $this->getContainer()->bind(DriverInterface::class, EloquentDriver::class);
        $this->getContainer()->bind(ConfiguratorInterface::class, UserWithoutProfileConfigurator::class);

        parent::setUp();
    }
}
