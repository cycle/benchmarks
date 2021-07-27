<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class SingleEntitySelect extends DatabaseBenchmark
{
    public Seeds $userSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->getConfigurator()->getDriver()->insertTableRows(
            'user',
            ['username', 'email'],
            [
                ['user1', 'user1@site.com'],
                ['user2', 'user2@site.com'],
                ['user3', 'user3@site.com'],
            ]
        );

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
    }

    /**
     * @Subject
     * @Groups({"select"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function findOne(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPK(1);
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            new UserSchema()
        )->toArray();
    }
}
