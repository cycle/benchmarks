<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\TagContextSchema;
use Cycle\Benchmarks\Base\Schemas\TagSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class ManyToManySelect extends DatabaseBenchmark
{
    public Seeds $userSeeds;
    public Seeds $tagSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->tagSeeds = $this->getConfigurator()->getTagSeeds();

        $this->getConfigurator()->getDriver()
            ->insertTableRows(
                'user',
                ['username', 'email'],
                [
                    ['user1', 'user1@site.com'],
                    ['user2', 'user2@site.com'],
                ]
            )
            ->insertTableRows(
                'tag',
                ['name'],
                [
                    ['tag a'],
                    ['tag b'],
                    ['tag c'],
                ]
            )
            ->insertTableRows(
                'tag_user_map',
                ['user_id', 'tag_id', 'as'],
                [
                    [1, 1, 'primary'],
                    [1, 2, 'secondary'],
                    [2, 3, 'primary'],
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
            ->findByPKWithTags(1, $params['eager'], $params['method']);

        $user->tags;
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
        $tags = $this->getConfigurator()
            ->geTagRepository()
            ->findAllForUser(1, $params['eager']);

        foreach ($tags as $tag) {
            foreach ($tag->users as $user) {
                $user->id;
            }
        }
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation(),
            (new TagSchema())->withUsersRelation(),
            new TagContextSchema()
        )->toArray();
    }
}
