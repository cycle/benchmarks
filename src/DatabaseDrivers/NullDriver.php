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
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\Database;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\SQLite\SQLiteDriver as DatabaseDriver;

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
        return new \Cycle\ORM\ORM(new Factory($this->dbal, RelationConfig::getDefault()));
    }

    public function createTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): void
    {

    }

    public function insertTableRows(string $table, array $columns = [], array $rowsets = []): void
    {

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
