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
abstract class UserWithCommentsPersist extends Benchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;
    public Seeds $commentSeeds;

    public function setUp(array $bindings = []): void
    {
        $bindings[ConfiguratorInterface::class] = UserConfigurator::class;

        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
        $this->commentSeeds = $this->getConfigurator()->getCommentSeeds();
    }

    /**
     * @Subject
     * @Groups({"persist"})
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
