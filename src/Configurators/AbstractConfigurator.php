<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Base\SeedRepository;

abstract class AbstractConfigurator implements ConfiguratorInterface
{
    private SeedRepository $seeds;
    private DriverInterface $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->seeds = new SeedRepository([]);
    }

    public function configure(): void
    {
        $this->driver->setSchema($this->getSchema());
        $this->driver->configure();
    }

    public function getFactory(): Factory
    {
        return $this->getDriver()->getFactory();
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getSeeds(): SeedRepository
    {
        return $this->seeds;
    }

    protected function seedEntityData(): void
    {
        $this->seeds = new SeedRepository(
            $this->getFactory()->export(ROOT . '/runtime/seeds', 1000, false)
        );
    }
}
