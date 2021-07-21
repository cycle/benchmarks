<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithoutProfileBench extends Benchmark
{
    public Seeds $userSeeds;

    public function setUp(array $bindings = []): void
    {
        $bindings[ConfiguratorInterface::class] = UserConfigurator::class;

        parent::setUp($bindings);

        $this->getConfigurator()->getDriver()->insertTableRows(
            'user', ['id', 'username', 'email'],
            [
                [123, 'admin', 'admin@site.com']
            ]
        );

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
    }

    /**
     * @Subject
     * @Groups({"hydrate"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function makeUser(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create($this->userSeeds->getClass());
        $entityFactory->hydrate($entity, $this->userSeeds->first());
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function createUser(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create($this->userSeeds->getClass());

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
     * @ParamProviders({"userAmounts"})
     */
    public function createUserInSingleTransaction(array $params): void
    {
        $seeds = $this->userSeeds->take($params['times']);

        $entityFactory = $this->getEntityFactory();

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        foreach ($seeds as $seed) {
            $entity = $entityFactory->create($seeds->getClass());
            $user = $entityFactory->hydrate($entity, $seed);
            $entityFactory->store($user);
        }

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function loadUser(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPK(123);
    }

    public function userAmounts(): \Generator
    {
        yield 'five records' => ['times' => 5];
        yield 'ten records' => ['times' => 10];
    }

    public function getSchema(string $mapper): array
    {
        return (new UserSchema($mapper))->toArray();
    }
}
