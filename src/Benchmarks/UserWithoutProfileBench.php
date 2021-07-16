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
            $entityFactory->beforeCreationCallbacks()[0]();
            $entityFactory->hydrate($entity, $seed);
            $entityFactory->store($entity);
            $entityFactory->afterCreationCallbacks()[0]();
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

        $entityFactory->beforeCreationCallbacks()[0]();

        foreach ($seeds as $seed) {
            $entityFactory->hydrate($entity, $seed);
            $entityFactory->store($entity);
        }

        $entityFactory->afterCreationCallbacks()[0]();
    }

    public function userAmounts(): \Generator
    {
        yield ['times' => 1];
        yield ['times' => 10];
        yield ['times' => 50];
    }
}
