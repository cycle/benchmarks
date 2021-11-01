<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Entites\Tag;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\TagContextSchema;
use Cycle\Benchmarks\Base\Schemas\TagSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class ManyToManyPersist extends DatabaseBenchmark
{
    public Seeds $userSeeds;
    public Seeds $tagSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->tagSeeds = $this->getConfigurator()->getTagSeeds();
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"entityAmounts"})
     */
    public function createUserWithTagSingleTransaction(array $params): void
    {
        $entityFactory = $this->getEntityFactory();

        $entity = new User();
        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());

        foreach ($this->tagSeeds->take($params['times']) as $seed) {
            $tagEntity = new Tag();
            $user->tags[] = $entityFactory->hydrate($tagEntity, $seed);
        }

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());
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
    public function createUsersWithCommonTags(array $params): void
    {
        $entityFactory = $this->getEntityFactory();

        $tags = [];
        foreach ($this->tagSeeds->take($params['times']) as $tagData) {
            $tags[] = $entityFactory->hydrate(new Tag(), $tagData);
        }

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());

        foreach ($this->userSeeds->take($params['times']) as $userData) {
            /** @var User $user */
            $user = $entityFactory->hydrate(new User(), $userData);
            $user->tags = $tags;
            $entityFactory->store($user);
        }
        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation(),
            new TagSchema(),
            new TagContextSchema()
        )->toArray();
    }
}
