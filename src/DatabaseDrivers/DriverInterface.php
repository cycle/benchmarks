<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\ORM\RepositoryInterface;
use Spiral\Database\ForeignKeyInterface;

interface DriverInterface
{
    public function configure(): void;
    public function createTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): self;
    public function insertTableRows(string $table, array $columns = [], array $rowsets = [], bool $truncate = true): self;
    public function makeFK(string $from, string $fromKey, string $to, string $toColumn, string $onDelete = ForeignKeyInterface::CASCADE, string $onUpdate = ForeignKeyInterface::CASCADE): self;
    public function getRepository(string $entity): RepositoryInterface;
    public function getEntityFactory(): EntityFactoryInterface;
    public function getFactory(): \Butschster\EntityFaker\Factory;
    public function setSchema(array $schema): void;
    public function createEntityFactory(): EntityFactoryInterface;
}
