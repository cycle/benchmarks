<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\CommentSchema;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;
use Cycle\Benchmarks\Base\Seeds\Seeds;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithCommentsBench extends Benchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;
    public Seeds $commentSeeds;

    public function setUp(array $bindings = []): void
    {
        $bindings[ConfiguratorInterface::class] = UserConfigurator::class;

        parent::setUp($bindings);

        $this->getConfigurator()->getDriver()->insertTableRows(
            'user', ['id', 'username', 'email'],
            [
                [100, 'admin', 'admin@site.com']
            ]
        );

        $this->getConfigurator()->getDriver()->insertTableRows(
            'profile', ['id', 'fullName', 'user_id'],
            [
                [200, 'John Smith', 100]
            ]
        );

        $this->getConfigurator()->getDriver()->insertTableRows(
            'comment', ['id', 'text', 'user_id'],
            [
                [300, 'Hello world', 100],
                [301, 'Hello world1', 100],
                [302, 'Hello world1', 100]
            ]
        );

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
        $this->commentSeeds = $this->getConfigurator()->getCommentSeeds();
    }

    /**
     * @Subject
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"commentAmounts"})
     */
    public function createUserWithComments(array $params): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create($this->userSeeds->getClass());
        $profileEntity = $entityFactory->create($this->profileSeeds->getClass());
        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());

        $profile = $entityFactory->hydrate(
            $profileEntity,
            $this->profileSeeds->first()
        );

        $user->setProfile($profile);

        foreach ($this->commentSeeds->take($params['times']) as $seed) {
            $commentEntity = $entityFactory->create($this->commentSeeds->getClass());
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
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function createUserWithExistsComments(): void
    {
        $entityFactory = $this->getEntityFactory();

        $comments = $this->getConfigurator()->getCommentRepository()->findAll();
        $entity = $entityFactory->create($this->userSeeds->getClass());
        $user = $entityFactory->hydrate($entity, $this->userSeeds->first());

        $profile = $this
            ->getConfigurator()
            ->getUserProfileRepository()
            ->findByPK(200);

        $user->setProfile($profile);

        foreach ($comments as $comment) {
            $user->addComment($comment);
        }

        $this->runCallbacks($entityFactory->beforeCreationCallbacks());
        $entityFactory->store($user);
        $this->runCallbacks($entityFactory->afterCreationCallbacks());
    }

    public function commentAmounts(): \Generator
    {
        yield 'one comment' => ['times' => 1];
        yield 'ten comments' => ['times' => 10];
    }

    public function getSchema(string $mapper): array
    {
        return []
            + (new UserSchema($mapper))->withProfileRelation()->toArray()
            + (new UserProfileSchema($mapper))->withUserRelation()->toArray()
            + (new CommentSchema($mapper))->withUserRelation()->toArray();
    }
}
