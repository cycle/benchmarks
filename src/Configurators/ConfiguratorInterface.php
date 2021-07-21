<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Butschster\EntityFaker\Seeds\SeedRepositoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;

interface ConfiguratorInterface
{
    public function configure(array $schema): void;
    public function getFactory(): Factory;
    public function getSeeds(): SeedRepositoryInterface;
    public function getDriver(): DriverInterface;
}
