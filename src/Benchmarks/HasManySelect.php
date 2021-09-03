<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\CommentSchema;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class HasManySelect extends DatabaseBenchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;
    public Seeds $commentSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
        $this->commentSeeds = $this->getConfigurator()->getCommentSeeds();

        $this->getConfigurator()->getDriver()
            ->insertTableRows(
                'user',
                ['username', 'email'],
                [
                    ['user1', 'user1@site.com'],
                    ['user2', 'user2@site.com'],
                    ['user3', 'user3@site.com'],
                ]
            )
            ->insertTableRows(
                'comment',
                ['text', 'user_id'],
                [
                    ['Hello world 1', 1],
                    ['Hello world 11', 1],
                    ['Hello world 111', 1],
                    ['Hello world 2', 2],
                    ['Hello world 22', 2],
                    ['Hello world 222', 2],
                    ['Hello world 3', 3],
                    ['Hello world 33', 3],
                    ['Hello world1 333', 3],
                ]
            );
    }

    /**
     * @Subject
     * @Groups({"select"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"joinableLoader", "relationLoadType"})
     * @Revs(300)
     * @Iterations(3)
     */
    public function findOneEntityWithRelations(array $params): void
    {
        $user = $this->getConfigurator()
            ->getUserRepository()
            ->findByPKWithComments(1, $params['eager'], $params['method']);

        $user->comments;
    }

    /**
     * @Subject
     * @Groups({"select"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"relationLoadType"})
     * @Revs(300)
     * @Iterations(3)
     */
    public function findAllForEntity(array $params): void
    {
        $comments = $this->getConfigurator()
            ->getCommentRepository()
            ->findAllForUser(1, $params['eager']);

        foreach ($comments as $comment) {
            $comment->user->username;
        }
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation()->withCommentsRelation(),
            (new CommentSchema())->withUserRelation()
        )->toArray();
    }
}
