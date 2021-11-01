# Benchmark tool for CycleORM

## Benchmark tool for CycleORM

```
// Run all projects with default settings
$ php bench run

// Run each project in a separate process (Recommended)
$ php bench run -p

// Run a specific project or multiple projects
$ php bench run v1
$ php bench run v1 v2 
$ php bench run eloquent8

// Run with number of iterations (Overrides the value in the benchmark itself)
$ php bench run -i 2

// Run with a number of runs of each subject (Overrides the value in the benchmark itself)
$ php bench run -r 200

// Benchmark filtering on startup
$ php bench run -f 200
$ php bench run -f benchFoo
$ php bench run -f HashBench::benchFoo
$ php bench run -f Hash.*

// Benchmark filtering by group on startup
$ php bench run -g select -g persist

// phpbench configuration change
$ php bench run -c phpbench-dev.json

// Run parameters can be combined
$ php bench run -p -r 200 -i 2 -f HashBench::benchFoo -c phpbench-dev.json v1 v2
```

## Benchmark project generation

Projects are generated based on the `config/projects.php` config

```php
return [
    // Project name can begin with "-", the project will be ignored in .gitignore
    'project_name' => [
        'boilerplate' => 'default', // default project template (optional)
        // composer requires
        'require' => [
            'cycle/orm' => '^1.5'.
        ],
        'locked_paths' => [ // files and directories that should not be removed by regeneration (optional)
            'vendor',
            'composer.json',
            'composer.lock'
        ],
        // Bindings for container for all benchmarks
        'bindings' => [
            \Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface::class => \Cycle\Benchmarks\Base\DatabaseDrivers\SqliteDriver::class,
            \Butschster\EntityFaker\EntityFactoryInterface::class => \Cycle\Benchmarks\Base\EntityFactory\CycleORMV1EntityFactory::class,
            'Cycle\ORM\MapperInterface' => 'Cycle\ORM\Mapper\Mapper',
        ],
        'benchmarks' => [
            \Cycle\Benchmarks\Base\Benchmarks\HasOnePersist::class => [
                // Bindings for specific benchmarks
                \Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface::class => \Cycle\Benchmarks\Base\DatabaseDrivers\NullDriver::class,
            ],
            \Cycle\Benchmarks\Base\Benchmarks\SingleEntityPersist::class,
            \Cycle\Benchmarks\Base\Benchmarks\SingleEntitySelect::class,
            \Cycle\Benchmarks\Base\Benchmarks\HasManyPersist::class,
            \Cycle\Benchmarks\Base\Benchmarks\HasManySelect::class
        ],
    ]
];
```

### Generate benchmarks

```
// Creation of projects and benchmarks which have not yet been created.
$ php bench generate

// All generated projects will be generated anew (all files of the project will be deleted, except those listed in the locked_paths)
$ php bench generate -o

// Generate only the listed projects
$ php bench generate v1 v2
```

### View reports

```
// Retrieve last run report
$ php bench report
$ php bench report latest

// Retrieve the previous report relative to the current one
$ php bench report latest-1
$ php php bench report latest-2
$ php php bench report latest-n

// Retrieve report by ID
$ php bench report 1346426b575f15ce2a67562db9170e9fb54f3ec6
```
