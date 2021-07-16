<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Base\SeedRepository;

interface ConfiguratorInterface
{
    public function configure(): void;
    public function getFactory(): Factory;
    public function getSeeds(): SeedRepository;
    public function getDriver(): DriverInterface;
    public function getSchema(): array;
}
