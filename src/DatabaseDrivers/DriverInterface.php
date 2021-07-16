<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Butschster\EntityFaker\EntityFactoryInterface;

interface DriverInterface
{
    public function configure(): void;
    public function createTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): void;
    public function getEntityFactory(): EntityFactoryInterface;
    public function getFactory(): \Butschster\EntityFaker\Factory;
    public function setSchema(array $schema): void;
    public function createEntityFactory(): EntityFactoryInterface;
}
