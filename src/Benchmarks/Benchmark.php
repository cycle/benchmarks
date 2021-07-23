<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\EntityFactoryInterface;
use Butschster\EntityFaker\Factory;
use Butschster\EntityFaker\Seeds\SeedRepositoryInterface;
use Cycle\Benchmarks\Base\Configurators\AbstractConfigurator;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\NullDriver;
use Cycle\ORM\MapperInterface;
use Spiral\Core\Container;

abstract class Benchmark
{
    private AbstractConfigurator $configurator;
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function setUp(array $bindings = []): void
    {
        if (!isset($bindings[DriverInterface::class])) {
            $bindings[DriverInterface::class] = NullDriver::class;
        }

        foreach ($bindings as $alias => $resolver) {
            $this->getContainer()->bind($alias, $resolver);
        }

        /** @var DriverInterface $databaseDriver */
        $databaseDriver = $this->container->get(DriverInterface::class);
        $this->configurator = $this->container->make(ConfiguratorInterface::class, [
            'driver' => $databaseDriver,
        ]);

        $this->configurator->configure(
            $this->getSchema(
                $bindings[MapperInterface::class]
            )
        );
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

    abstract public function getSchema(string $mapper): array;
}
