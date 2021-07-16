<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Eloquent;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\AbstractDriver;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Spiral\Core\Container;

class EloquentDriver extends AbstractDriver
{
    private Manager $capsule;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->capsule = new Manager;

        $this->capsule->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
        ]);

        $this->capsule->bootEloquent();
    }

    public function configure(): void
    {
        $this->entityFactory = $this->container->make(EntityFactoryInterface::class, [
            'db' => $this->capsule->getDatabaseManager()
        ]);

        parent::configure();
    }

    public function createTable(string $table, array $columns, array $fk = [], array $pk = null, array $defaults = []): void
    {
        $this->capsule->getDatabaseManager()->getSchemaBuilder()
            ->create($table, function (Blueprint $table) use($columns) {
                foreach ($columns as $column => $type) {
                    $table->{$type}($column);
                }
            });
    }

    public function setSchema(array $schema): void
    {
        // TODO: Implement setSchema() method.
    }
}
