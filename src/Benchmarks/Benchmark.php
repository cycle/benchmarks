<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Spiral\Core\Container;

abstract class Benchmark
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function setUp(array $bindings = []): void
    {
        foreach ($bindings as $alias => $resolver) {
            $this->getContainer()->bind($alias, $resolver);
        }
    }

    public function tearDown(): void
    {
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
