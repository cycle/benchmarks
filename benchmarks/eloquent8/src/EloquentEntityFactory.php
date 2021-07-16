<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Eloquent;

use Cycle\Benchmarks\Base\Entites\UserWithoutProfile;
use Cycle\Benchmarks\Base\EntityFactory\AbstractEntityFactory;
use Illuminate\Database\DatabaseManager;

class EloquentEntityFactory extends AbstractEntityFactory
{
    private array $entitiesMap = [
        UserWithoutProfile::class => Models\UserWithoutProfile::class
    ];

    public function __construct(DatabaseManager $db)
    {
        $this->beforeCreation(function () use($db) {
            $db->beginTransaction();
        });

        $this->afterCreation(function () use($db) {
            $db->commit();
        });
    }

    public function create(string $class): object
    {
        $model = $this->entitiesMap[$class];

        return new $model;
    }

    public function store(object $entity): void
    {
        $entity->save();
    }

    public function hydrate(object $entity, array $data): object
    {
        $entity->fill($data);
        return $entity;
    }
}
