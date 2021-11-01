<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class HasOneSelect extends DatabaseBenchmark
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
                    ['user3', 'user3@site.com'],
                ]
            )
            ->insertTableRows(
                'profile',
                ['fullName', 'user_id'],
                [
                    ['John Smith', 1],
                    ['Matthew Perry', 2],
                    ['Matthew LeBlanc', 3],
                ]
            );
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @Revs(300)
     * @Iterations(3)
     */
    public function findOneWithoutRelation(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPK(1);
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @ParamProviders({"joinableLoader", "relationLoadType"})
     * @Revs(300)
     * @Iterations(3)
     */
    public function findOneWithRelation(array $params): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPKWithProfile(1, $params['eager'], $params['method']);
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
