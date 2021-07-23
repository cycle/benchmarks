<?php

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\SqliteDriver;
use Cycle\Benchmarks\Base\EntityFactory\CycleORMV1EntityFactory;
use Cycle\Benchmarks\Base\EntityFactory\CycleORMV2EntityFactory;

$benchmarks = [
    \Cycle\Benchmarks\Base\Benchmarks\UserWithProfilePersist::class,
    \Cycle\Benchmarks\Base\Benchmarks\UserWithProfileSelect::class,
    \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfilePersist::class,
    \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileSelect::class,
    \Cycle\Benchmarks\Base\Benchmarks\UserWithCommentsPersist::class,
    \Cycle\Benchmarks\Base\Benchmarks\UserWithCommentsSelect::class
];

$defaultMapper = [
    'Cycle\ORM\Mapper\Mapper' => $benchmarks,
];

$promiseMapper = [
    'Cycle\ORM\Mapper\PromiseMapper' => $benchmarks
];

$stdMapper = [
    'Cycle\ORM\Mapper\StdMapper' => $benchmarks
];

return [
    'v1' => [
        'require' => [
            'cycle/orm' => '^1.5'
        ],
        'bindings' => [
            DriverInterface::class => SqliteDriver::class,
            EntityFactoryInterface::class => CycleORMV1EntityFactory::class,
        ],
        'benchmarks' => [
            'Cycle\ORM\Mapper\Mapper' => $benchmarks
        ],
    ],
    'v2' => [
        'require' => [
            'cycle/orm' => '^2.0.x-dev'
        ],
        'bindings' => [
            DriverInterface::class => SqliteDriver::class,
            EntityFactoryInterface::class => CycleORMV2EntityFactory::class,
        ],
        'benchmarks' => array_merge($promiseMapper, $defaultMapper),
    ],
    '-v2dev' => [
        'locked_paths' => [
            'vendor',
            'composer.json',
            'composer.lock'
        ],
        'require' => [
            'cycle/orm' => '^2.0.x-dev'
        ],
        'bindings' => [
            DriverInterface::class => SqliteDriver::class,
            EntityFactoryInterface::class => CycleORMV2EntityFactory::class,
        ],
        'benchmarks' => array_merge($promiseMapper, $defaultMapper),
    ]
];
