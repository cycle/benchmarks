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
use Spiral\Database\ForeignKeyInterface;

class SqliteV1Driver extends AbstractDriver
{
    private DatabaseManager $dbal;
    private ORMInterface $orm;
    private DatabaseInterface $database;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $driver = new DatabaseDriver([
            'connection' => 'sqlite::memory:',
            'username' => 'sqlite',
            'password' => '',
            'options' => [],
            'queryCache' => false
        ]);

        $this->dbal = new DatabaseManager(
            new DatabaseConfig()
        );

        $this->dbal->addDatabase(new Database('default', '', $driver));
        $this->orm = $this->createOrm();
    }

    private function createOrm(): ORMInterface
    {
        return new \Cycle\ORM\ORM(
            new Factory($this->dbal, RelationConfig::getDefault()),
            new Schema([])
        );
    }

    public function configure(): void
    {
        $this->database = $this->orm->getFactory()->database('default');
        parent::configure();
    }

    public function createTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): self
    {
        $this->database->execute('DROP TABLE IF EXISTS ' . $table);

        $schema = $this->database->table($table)->getSchema();

        $renderer = new TableRenderer();
        $renderer->renderColumns($schema, $columns, $defaults);

        foreach ($fk as $column => $options) {
            $schema->foreignKey([$column])->references($options['table'], [$options['column']]);
        }

        if (!empty($pk)) {
            $schema->setPrimaryKeys($pk);
        }

        $schema->save();

        return $this;
    }

    public function insertTableRows(string $table, array $columns = [], array $rowsets = [], bool $truncate = true): self
    {
        if ($truncate) {
            //$this->database->execute('DELETE FROM  ' . $table);
        }

        $this->database
            ->insert($table)
            ->columns($columns)
            ->values($rowsets)
            ->run();

        return $this;
    }


    public function makeFK(string $from, string $fromKey, string $to, string $toColumn, string $onDelete = ForeignKeyInterface::CASCADE, string $onUpdate = ForeignKeyInterface::CASCADE): self
    {
        $schema = $this->database->table($from)->getSchema();
        $schema->foreignKey([$fromKey])->references($to, [$toColumn])->onDelete($onDelete)->onUpdate($onUpdate);
        $schema->save();

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
