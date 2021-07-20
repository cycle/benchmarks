<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\EntityFactoryInterface;
use Butschster\EntityFaker\Factory;
use Cycle\Benchmarks\Base\Configurators\AbstractConfigurator;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Base\Seeds\SeedRepositoryInterface;
use Spiral\Core\Container;

abstract class Benchmark
{
    private AbstractConfigurator $configurator;
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function setUp(): void
    {
        /** @var DriverInterface $databaseDriver */
        $databaseDriver = $this->container->get(DriverInterface::class);
        $this->configurator = $this->container->make(ConfiguratorInterface::class, [
            'driver' => $databaseDriver,
        ]);
        $this->configurator->configure();
    }

    public function tearDown(): void
    {

    }

    public function getEntityFactory(): EntityFactoryInterface
    {
        return $this->getFactory()->getEntityFactory();
    }

    public function getSeeds(): SeedRepositoryInterface
    {
        return $this->configurator->getSeeds();
    }

    public function getFactory(): Factory
    {
        return $this->configurator->getFactory();
    }

    public function getConfigurator(): ConfiguratorInterface
    {
        return $this->configurator;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param array<callable> $callbacks
     */
    public function runCallbacks(array $callbacks): void
    {
        foreach ($callbacks as $callback) {
            $callback();
        }
    }
}
