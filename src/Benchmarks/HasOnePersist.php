<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class HasOnePersist extends DatabaseBenchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @Revs(300)
     * @Iterations(3)
     */
    public function store(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = new User();
        $profileEntity = new UserProfile();

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
     * @ParamProviders({"entityAmounts"})
     * @Revs(300)
     * @Iterations(3)
     */
    public function storeManySingleTransaction(array $params): void
    {
        $userSeeds = $this->userSeeds->take($params['times']);
        $entityFactory = $this->getEntityFactory();

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        foreach ($userSeeds as $seed) {
            $entity = new User();
            $user = $entityFactory->hydrate($entity, $seed);

            $profileEntity = new UserProfile();
            $profile = $entityFactory->hydrate(
                $profileEntity,
                $this->profileSeeds->random()->first()
            );

            $user->setProfile($profile);
            $entityFactory->store($entity);
        }

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation(),
            (new UserProfileSchema())->withUserRelation()
        )->toArray();
    }
}
