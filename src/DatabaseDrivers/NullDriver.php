<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Factory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Schema;
use Spiral\Core\Container;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\SQLite\SQLiteDriver as DatabaseDriver;
use Cycle\Database\ForeignKeyInterface;

class NullDriver extends AbstractDriver
{
    private DatabaseManager $dbal;
    private ORMInterface $orm;
    private DatabaseInterface $database;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $driver = new \Cycle\Benchmarks\Base\DatabaseDrivers\Null\NullDriver([]);

        $this->dbal = new DatabaseManager(
            new DatabaseConfig()
        );

        $this->dbal->addDatabase(new Database('default', '', $driver));
        $this->orm = $this->createOrm();
    }

    private function createOrm(): ORMInterface
    {
        return new \Cycle\ORM\ORM(new Factory($this->dbal, RelationConfig::getDefault()), new Schema([]));
    }

    public function createTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): self
    {
        return $this;
    }

    public function insertTableRows(string $table, array $columns = [], array $rowsets = [], bool $truncate = true): self
    {
        return $this;
    }

    public function makeFK(string $from, string $fromKey, string $to, string $toColumn, string $onDelete = ForeignKeyInterface::CASCADE, string $onUpdate = ForeignKeyInterface::CASCADE): DriverInterface
    {
        return $this;
    }

    public function getRepository(string $entity): RepositoryInterface
    {
        return $this->orm->getRepository($entity);
    }

    public function setSchema(array $schema): void
    {
        $this->orm = $this->orm->withSchema(new Schema($schema));
    }

    public function createEntityFactory(): EntityFactoryInterface
    {
        return $this->container->make(EntityFactoryInterface::class, [
            'orm' => $this->orm
        ]);
    }
}
