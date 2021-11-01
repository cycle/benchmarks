<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class SingleEntityPersist extends DatabaseBenchmark
{
    public Seeds $userSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function store(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = new User();

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());
        $entityFactory->store($user);

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"entityAmounts"})
     */
    public function storeManySingleTransaction(array $params): void
    {
        $seeds = $this->userSeeds->take($params['times']);

        $entityFactory = $this->getEntityFactory();

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        foreach ($seeds as $seed) {
            $entity = new User();
            $user = $entityFactory->hydrate($entity, $seed);
            $entityFactory->store($user);
        }

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            new UserSchema()
        )->toArray();
    }
}
