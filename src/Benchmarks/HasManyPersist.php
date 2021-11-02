<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Schemas\CommentSchema;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class HasManyPersist extends DatabaseBenchmark
{
    public Seeds $userSeeds;
    public Seeds $commentSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->commentSeeds = $this->getConfigurator()->getCommentSeeds();
    }

    /**
     * @Subject
     * @Groups({"persist"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"commentAmounts"})
     * @Revs(300)
     * @Iterations(3)
     */
    public function createUserWithCommentsSingleTransaction(array $params): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = new User();
        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());

        foreach ($this->commentSeeds->take($params['times']) as $seed) {
            $commentEntity = new Comment();
            $user->addComment(
                $entityFactory->hydrate($commentEntity, $seed)
            );
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
     * @ParamProviders({"commentAmounts"})
     * @Revs(300)
     * @Iterations(3)
     */
    public function createUserWithComments(array $params): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = new User();
        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());

        foreach ($this->commentSeeds->take($params['times']) as $seed) {
            $commentEntity = new Comment();
            $user->addComment(
                $entityFactory->hydrate($commentEntity, $seed)
            );

            $this->runCallbacks($entityFactory->beforeCreationCallbacks());
            $entityFactory->store($user);
            $this->runCallbacks($entityFactory->afterCreationCallbacks());
        }
    }

    public function commentAmounts(): \Generator
    {
        yield 'one comment' => ['times' => 1];
        yield 'ten comments' => ['times' => 10];
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation(),
            (new UserProfileSchema())->withUserRelation(),
            (new CommentSchema())->withUserRelation()
        )->toArray();
    }
}
