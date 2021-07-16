<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\Configurators\AbstractConfigurator;
use Cycle\Benchmarks\Base\Configurators\UserWithoutProfileConfigurator;
use Cycle\Benchmarks\Base\SeedRepository;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Factory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\Database;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\SQLite\SQLiteDriver;

abstract class Benchmark
{
    private DatabaseManager $dbal;
    protected \Cycle\ORM\ORMInterface $orm;
    private AbstractConfigurator $configurator;

    public function __construct()
    {
        $driver = new SQLiteDriver([
            'connection' => 'sqlite::memory:',
            'username' => 'sqlite',
            'password' => '',
            'options' => [],
            'queryCache' => true
        ]);

        $this->dbal = new DatabaseManager(
            new DatabaseConfig()
        );

        $this->dbal->addDatabase(new Database('default', '', $driver));
    }

    public function setUp(): void
    {
        $orm = $this->createOrm();

        $this->configurator = new UserWithoutProfileConfigurator(
            $orm, $this->getEntityFactoryClass()
        );

        $this->configurator->configure();
        $this->orm = $this->configurator->getOrm();
    }

    public function tearDown(): void
    {

    }

    public function getEntityFactory(): EntityFactoryInterface
    {
        return $this->configurator->getFactory()->getEntityFactory();
    }

    abstract public function getEntityFactoryClass(): string;

    public function getOrm(): ORMInterface
    {
        return $this->configurator->getOrm();
    }

    public function getSeeds(): SeedRepository
    {
        return $this->configurator->getSeeds();
    }

    private function createOrm(?SchemaInterface $schema = null): ORMInterface
    {
        return new \Cycle\ORM\ORM(
            new Factory($this->dbal, RelationConfig::getDefault()),
            $schema
        );
    }

    public function getFactory(): \Butschster\EntityFaker\Factory
    {
        return $this->configurator->getFactory();
    }

    public function getConfigurator(): AbstractConfigurator
    {
        return $this->configurator;
    }
}
