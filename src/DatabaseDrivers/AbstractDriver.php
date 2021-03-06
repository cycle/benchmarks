<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Butschster\EntityFaker\EntityFactoryInterface;
use Spiral\Core\Container;

abstract class AbstractDriver implements DriverInterface
{
    protected EntityFactoryInterface $entityFactory;
    protected \Butschster\EntityFaker\Factory $factory;

    public function __construct(
        protected Container $container
    ) {
    }

    public function configure(): void
    {
        $this->entityFactory = $this->createEntityFactory();

        $this->factory = new \Butschster\EntityFaker\Factory(
            $this->entityFactory,
            \Faker\Factory::create()
        );
    }

    public function getEntityFactory(): EntityFactoryInterface
    {
        return $this->entityFactory;
    }

    public function getFactory(): \Butschster\EntityFaker\Factory
    {
        return $this->factory;
    }
}
