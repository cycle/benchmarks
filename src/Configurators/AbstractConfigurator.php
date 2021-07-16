<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\SeedRepository;
use Cycle\Benchmarks\Base\TableRenderer;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Spiral\Database\Database;

abstract class AbstractConfigurator
{
    private ORMInterface $orm;
    protected SeedRepository $seeds;

    public function __construct(ORMInterface $orm, SchemaInterface $schema)
    {
        $this->orm = $orm->withSchema($schema);
        $this->seeds = new SeedRepository([]);
    }

    abstract public function configure(): void;

    abstract public function getFactory(): \Butschster\EntityFaker\Factory;

    public function getOrm(): ORMInterface
    {
        return $this->orm;
    }

    public function makeTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): void
    {
        $schema = $this->getDatabase()->table($table)->getSchema();

        $renderer = new TableRenderer();
        $renderer->renderColumns($schema, $columns, $defaults);

        foreach ($fk as $column => $options) {
            $schema->foreignKey([$column])->references($options['table'], [$options['column']]);
        }

        if (!empty($pk)) {
            $schema->setPrimaryKeys([$pk]);
        }

        $schema->save();
    }

    protected function getDatabase(): Database
    {
        return $this->orm->getFactory()->database('default');
    }

    public function getSeeds(): SeedRepository
    {
        return $this->seeds;
    }

    protected function seedEntityData(): void
    {
        $this->seeds = new SeedRepository(
            $this->getFactory()->export(ROOT . '/runtime/seeds', 1000, false)
        );
    }
}
