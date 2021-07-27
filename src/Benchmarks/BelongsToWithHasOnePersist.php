<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Entites\ProfileNested;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Schemas\ProfileNestedSchema;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class BelongsToWithHasOnePersist extends DatabaseBenchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;
    public Seeds $profileNestedSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
        $this->profileNestedSeeds = $this->getConfigurator()->getProfileNestedSeeds();
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
        $profileNestedEntity = new ProfileNested();

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        $profile = $entityFactory->hydrate(
            $profileEntity,
            $this->profileSeeds->first()
        );

        $profileNested = $entityFactory->hydrate(
            $profileNestedEntity,
            $this->profileNestedSeeds->first()
        );

        $profile->setNested($profileNested);

        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());
        $user->setProfile($profile);
        $entityFactory->store($user);

        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation(),
            (new UserProfileSchema())->withUserRelation()->withNestedRelation(),
            (new ProfileNestedSchema())->withProfileRelation()
        )->toArray();
    }
}
