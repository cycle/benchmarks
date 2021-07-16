<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Cycle\Benchmarks\Base\Entites\UserWithoutProfile;

abstract class UserWithoutProfileBench extends Benchmark
{
    /**
     * @Subject
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders("userAmounts")
     *
     * @Revs(200)
     * @Iterations(2)
     */
    public function makeUser(array $params): void
    {
        $seeds = $this->getSeeds()->get(UserWithoutProfile::class)
            ->take($params['times']);

        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create(UserWithoutProfile::class);

        foreach ($seeds as $seed) {
            $entityFactory->hydrate($entity, $seed);
        }
    }

    /**
     * @Subject
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders("userAmounts")
     *
     * @Revs(200)
     * @Iterations(2)
     */
    public function createUser(array $params): void
    {
        $seeds = $this->getSeeds()->get(UserWithoutProfile::class)
            ->take($params['times']);

        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create(UserWithoutProfile::class);

        foreach ($seeds as $seed) {
            $this->runCallbacks($entityFactory->beforeCreationCallbacks());
            $entityFactory->hydrate($entity, $seed);
            $entityFactory->store($entity);
            $this->runCallbacks($entityFactory->afterCreationCallbacks());
        }
    }

    /**
     * @Subject
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders("userAmounts")
     *
     * @Revs(200)
     * @Iterations(2)
     */
    public function createUserSingleTransaction(array $params): void
    {
        $seeds = $this->getSeeds()->get(UserWithoutProfile::class)
            ->take($params['times']);

        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create(UserWithoutProfile::class);

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        foreach ($seeds as $seed) {
            $entityFactory->hydrate($entity, $seed);
            $entityFactory->store($entity);
        }

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    public function userAmounts(): \Generator
    {
        yield ['times' => 1];
        yield ['times' => 10];
        yield ['times' => 50];
    }
}
