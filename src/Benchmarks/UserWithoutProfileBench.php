<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Seeds\Seeds;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithoutProfileBench extends Benchmark
{
    public Seeds $userSeeds;

    public function setUp(): void
    {
        $this->getContainer()->bind(ConfiguratorInterface::class, UserConfigurator::class);

        parent::setUp();

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
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders("userAmounts")
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
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function loadUser()
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
}
