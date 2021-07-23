# Benchmark tool for CycleORM

## Запуск бенчмарков

```
// Запуск всех проектов с настройками по умолчанию
$ php bench run

// Запуск каждого проекта в отдельном процессе (Рекомендуется)
$ php bench run -p

// Запуск определенного проекта или нескольких проектов
$ php bench run v1
$ php bench run v1 v2 
$ php bench run eloquent8

// Запуск с указанием кол-ва итераций (Переопределяет значение в самом бенчмарке)
$ php bench run -i 2

// Запуск с указанием кол-ва прогонов каждого субъекта (Переопределяет значение в самом бенчмарке)
$ php bench run -r 200

// Фильтрация бенчмарков при запуске
$ php bench run -f 200
$ php bench run -f benchFoo
$ php bench run -f HashBench::benchFoo
$ php bench run -f Hash.*

// Фильтрация бенчмарков по группам при запуске
$ php bench run -g select -g persist

// Подмена конфига phpbench
$ php bench run -c phpbench-dev.json

// Параметры запуска можно комбинировать
$ php bench run -p -r 200 -i 2 -f HashBench::benchFoo -c phpbench-dev.json v1 v2
```

## Генерация проектов бенчмарков

Генерация проектов производится на основе конфига `config/projects.php`

```php
return [
    // Название проекта может начинаться со знака "-", проект будет игнорироваться в .gitignore
    'project_name' => [
        'boilerplate' => 'default', // Шаблон проекта по умолчанию (optional)
        // composer requires
        'require' => [
            'cycle/orm' => '^1.5'
        ],
        'locked_paths' => [ // Файлы и директории которые не нужно удалять при перегенерации (optional)
            'vendor',
            'composer.json',
            'composer.lock'
        ],
        // Bindings for container for all benchmarks
        'bindings' => [
            \Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface::class => \Cycle\Benchmarks\Base\DatabaseDrivers\SqliteDriver::class,
            \Butschster\EntityFaker\EntityFactoryInterface::class => \Cycle\Benchmarks\Base\EntityFactory\CycleORMV1EntityFactory::class,
        ],
        'benchmarks' => [
            // Бенчмарки группируются по мапперам
            'Cycle\ORM\Mapper\Mapper' => [
                \Cycle\Benchmarks\Base\Benchmarks\UserWithProfilePersist::class => [
                    // Bindings for specific benchmarks
                    \Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface::class => \Cycle\Benchmarks\Base\DatabaseDrivers\NullDriver::class,
                ],
                \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfilePersist::class,
                \Cycle\Benchmarks\Base\Benchmarks\UserWithoutProfileSelect::class,
                \Cycle\Benchmarks\Base\Benchmarks\UserWithCommentsPersist::class,
                \Cycle\Benchmarks\Base\Benchmarks\UserWithCommentsSelect::class
            ]
        ],
    ]
];
```

### Запуск генерации бенчмарков

```
// Создание проектов и бенчмарков, которые еще не созданы
$ php bench generate

// Все созданные проекты будут перегенерированы заново (Все файлы проекта будут удалены, кроме перечисленных в locked_paths)
$ php bench generate -o

// Генерация только перечисленных проектов
$ php bench generate v1 v2
```
