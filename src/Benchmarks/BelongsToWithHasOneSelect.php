<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\ProfileNestedSchema;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class BelongsToWithHasOneSelect extends DatabaseBenchmark
{
    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->getConfigurator()->getDriver()
            ->insertTableRows(
                'user',
                ['username', 'email'],
                [
                    ['user1', 'user1@site.com'],
                    ['user2', 'user2@site.com'],
                    ['user3', 'user3@site.com']
                ]
            )
            ->insertTableRows(
                'profile',
                ['fullName', 'user_id'],
                [
                    ['John Smith', 1],
                    ['Matthew Perry', 2],
                    ['Matthew LeBlanc', 3]
                ]
            )
            ->insertTableRows(
                'nested',
                ['label', 'profile_id'],
                [
                    ['label 1', 1],
                    ['label 2', 1],
                    ['label 3', 2]
                ]
            );
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"joinableLoader", "relationLoadType"})
     */
    public function findOneWithRelations(array $params): void
    {
        $profile = $this->getConfigurator()
            ->getUserProfileRepository()
            ->findByPKWithUserAndNested(1, $params['eager'], $params['method']);

        $profile->user->id;
        $profile->nested->id;
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
