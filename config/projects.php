<?php

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\Configurators;
use Cycle\Benchmarks\Base\DatabaseDrivers;
use Cycle\Benchmarks\Base\EntityFactory;
use Cycle\Benchmarks\Base\Benchmarks;

$userConfigurator = [
    Configurators\ConfiguratorInterface::class => Configurators\UserConfigurator::class,
];

$benchmarks = [
    Benchmarks\BelongsToWithHasOnePersist::class => $userConfigurator,
    Benchmarks\BelongsToWithHasOneSelect::class => $userConfigurator,
    Benchmarks\HasOnePersist::class => $userConfigurator,
    Benchmarks\HasOneSelect::class => $userConfigurator,
    Benchmarks\SingleEntityPersist::class => $userConfigurator,
    Benchmarks\SingleEntitySelect::class => $userConfigurator,
    Benchmarks\HasManyPersist::class => $userConfigurator,
    Benchmarks\HasManySelect::class => $userConfigurator,
    Benchmarks\ManyToManyPersist::class => $userConfigurator,
    Benchmarks\ManyToManyPersistBackRelations::class => $userConfigurator,
    Benchmarks\Hydrator::class => $userConfigurator,
];

return [
    'v1' => [
        'require' => [
            'cycle/orm' => '^1.5',
        ],
        'bindings' => [
            DatabaseDrivers\DriverInterface::class => DatabaseDrivers\SqliteDriver::class,
            EntityFactoryInterface::class => EntityFactory\CycleORMV1EntityFactory::class,
            'Cycle\ORM\MapperInterface' => 'Cycle\ORM\Mapper\Mapper',
        ],
        'benchmarks' => $benchmarks,
    ],
    // Laminas Reflection hydrator
    'v2refhyd' => [
        'require' => [
            'cycle/orm' => 'dev-master#a47e3aa2d91a7e7bf2850f58d322391510fc2eba',
        ],
        'bindings' => [
            DatabaseDrivers\DriverInterface::class => DatabaseDrivers\SqliteDriver::class,
            EntityFactoryInterface::class => EntityFactory\CycleORMV2EntityFactory::class,
            'Cycle\ORM\MapperInterface' => 'Cycle\ORM\Mapper\Mapper',
        ],
        'benchmarks' => $benchmarks,
    ],
    'v2' => [
        'require' => [
            'cycle/orm' => '^2.0.x-dev',
        ],
        'bindings' => [
            DatabaseDrivers\DriverInterface::class => DatabaseDrivers\SqliteDriver::class,
            EntityFactoryInterface::class => EntityFactory\CycleORMV2EntityFactory::class,
            'Cycle\ORM\MapperInterface' => 'Cycle\ORM\Mapper\Mapper',
        ],
        'benchmarks' => $benchmarks,
    ],
    'v2promise' => [
        'require' => [
            'cycle/orm' => '^2.0.x-dev',
        ],
        'bindings' => [
            DatabaseDrivers\DriverInterface::class => DatabaseDrivers\SqliteDriver::class,
            EntityFactoryInterface::class => EntityFactory\CycleORMV2EntityFactory::class,
            'Cycle\ORM\MapperInterface' => 'Cycle\ORM\Mapper\PromiseMapper',
        ],
        'benchmarks' => $benchmarks,
    ],
    '-v2dev' => [
        'locked_paths' => [
            'vendor',
            'composer.json',
            'composer.lock',
        ],
        'require' => [
            'cycle/orm' => '^2.0.x-dev',
        ],
        'bindings' => [
            DatabaseDrivers\DriverInterface::class => DatabaseDrivers\SqliteDriver::class,
            EntityFactoryInterface::class => EntityFactory\CycleORMV2EntityFactory::class,
            'Cycle\ORM\MapperInterface' => 'Cycle\ORM\Mapper\Mapper',
        ],
        'benchmarks' => $benchmarks,
    ],
];
