<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;
use Generator;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithProfileBench extends Benchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;

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

        $this->getConfigurator()->getDriver()->insertTableRows(
            'profile', ['id', 'fullName', 'user_id'],
            [
                [234, 'John Smith', 123]
            ]
        );

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function createUserWithProfile(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create($this->userSeeds->getClass());
        $profileEntity = $entityFactory->create($this->profileSeeds->getClass());

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        $profile = $entityFactory->hydrate(
            $profileEntity,
            $this->profileSeeds->first()
        );

        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());
        $user->setProfile($profile);
        $entityFactory->store($user);

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function createUserWithExistsProfile(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create($this->userSeeds->getClass());

        $profile = $this
            ->getConfigurator()
            ->getUserProfileRepository()
            ->findByPK(234);

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());
        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());
        $user->setProfile($profile);
        $entityFactory->store($entity);
        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"userAmounts"})
     */
    public function createUserWithProfileSingleTransaction(array $params): void
    {
        $userSeeds = $this->userSeeds->take($params['times']);
        $entityFactory = $this->getEntityFactory();

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        foreach ($userSeeds as $seed) {
            $entity = $entityFactory->create($userSeeds->getClass());
            $user = $entityFactory->hydrate($entity, $seed);

            $profileEntity = $entityFactory->create($this->profileSeeds->getClass());
            $profile = $entityFactory->hydrate(
                $profileEntity,
                $this->profileSeeds->random()->first()
            );

            $user->setProfile($profile);
            $entityFactory->store($entity);
        }

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function loadUserWithoutProfile(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPK(123);
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function loadUserWithProfile(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPKWithProfile(123);
    }

    public function userAmounts(): Generator
    {
        yield 'five records' => ['times' => 5];
        yield 'ten records' => ['times' => 10];
    }

    public function getSchema(string $mapper): array
    {
        return array_merge(
            (new UserSchema($mapper))->withProfileRelation()->toArray(),
            (new UserProfileSchema($mapper))->withUserRelation()->toArray()
        );
    }
}
