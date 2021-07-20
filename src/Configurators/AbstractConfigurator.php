<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Base\Seeds\InMemorySeedRepository;
use Cycle\Benchmarks\Base\Seeds\SeedRepositoryInterface;

abstract class AbstractConfigurator implements ConfiguratorInterface
{
    protected const SEED_TIMES = 100;
    protected SeedRepositoryInterface $seeds;

    public function __construct(private DriverInterface $driver)
    {
    }

    public function configure(array $schema): void
    {
        $this->driver->setSchema($schema);
        $this->driver->configure();

        $this->createTables();
        $this->defineEntities($this->getFactory());
        $this->seedEntityData();
    }

    abstract public function createTables(): void;

    abstract public function defineEntities(Factory $factory): void;

    public function getFactory(): Factory
    {
        return $this->getDriver()->getFactory();
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getSeeds(): SeedRepositoryInterface
    {
        return $this->seeds;
    }

    protected function seedEntityData(): void
    {
        $this->seeds = new InMemorySeedRepository(
            $this->getFactory()->raw(self::SEED_TIMES)
        );
    }

//    protected function seedEntityData(): void
//    {
//        $this->seeds = new FileSeedRepository(
//            $this->getFactory()->export(ROOT . '/runtime/seeds', self::SEED_TIMES, false)
//        );
//    }
}
