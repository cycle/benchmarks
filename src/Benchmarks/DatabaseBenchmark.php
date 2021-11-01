<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\EntityFactoryInterface;
use Butschster\EntityFaker\Factory;
use Butschster\EntityFaker\Seeds\SeedRepositoryInterface;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\ORM\MapperInterface;
use Generator;

abstract class DatabaseBenchmark extends Benchmark
{
    private ConfiguratorInterface $configurator;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->configurator = $this->getContainer()->make(ConfiguratorInterface::class);

        if (!isset($bindings[MapperInterface::class])) {
            $bindings[MapperInterface::class] = 'Cycle\ORM\Mapper\Mapper';
        }

        $this->configurator->configure(
            $this->getSchema($bindings[MapperInterface::class])
        );
    }

    public function getConfigurator(): ConfiguratorInterface
    {
        return $this->configurator;
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

    public function joinableLoader(): Generator
    {
        if (!class_exists('Cycle\ORM\Select\JoinableLoader')) {
            return;
        }

        yield 'postload' => ['method' => \Cycle\ORM\Select\JoinableLoader::POSTLOAD]; // By default
//        yield 'inload' => ['method' => JoinableLoader::INLOAD];
//        yield 'join' => ['method' => JoinableLoader::JOIN];
//        yield 'left_join' => ['method' => JoinableLoader::LEFT_JOIN];
    }

    public function entityAmounts(): Generator
    {
        yield 'Amount:5' => ['times' => 5];
        yield 'Amount:10' => ['times' => 10];
    }

    public function relationLoadType(): Generator
    {
        yield 'lazy' => ['eager' => true];
        yield 'eager' => ['eager' => false];
    }

    abstract public function getSchema(string $mapper): array;
}
