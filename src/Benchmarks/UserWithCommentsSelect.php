<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\CommentSchema;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithCommentsSelect extends Benchmark
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
     * @Groups({"select"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function findAllCommentsForUser(): void
    {

    }

    public function getSchema(string $mapper): array
    {
        return []
            + (new UserSchema($mapper))->withProfileRelation()->toArray()
            + (new UserProfileSchema($mapper))->withUserRelation()->toArray()
            + (new CommentSchema($mapper))->withUserRelation()->toArray();
    }
}
