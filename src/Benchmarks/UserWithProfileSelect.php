<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\UserProfileSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithProfileSelect extends Benchmark
{
    public Seeds $userSeeds;
    public Seeds $profileSeeds;

    public function setUp(array $bindings = []): void
    {
        $bindings[ConfiguratorInterface::class] = UserConfigurator::class;

        parent::setUp($bindings);

        $this->getConfigurator()->getDriver()->insertTableRows(
            'user', ['id', 'username', 'email'],
            [
                [123, 'admin', 'admin@site.com']
            ]
        );

        $this->getConfigurator()->getDriver()->insertTableRows(
            'profile', ['id', 'fullName', 'user_id'],
            [
                [234, 'John Smith', 123]
            ]
        );

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
        $this->profileSeeds = $this->getConfigurator()->getUserProfileSeeds();
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function loadUserWithoutProfile(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPK(123);
    }

    /**
     * @Subject
     * @Groups({"find"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function loadUserWithProfile(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPKWithProfile(123);
    }

    public function getSchema(string $mapper): array
    {
        return array_merge(
            (new UserSchema($mapper))->withProfileRelation()->toArray(),
            (new UserProfileSchema($mapper))->withUserRelation()->toArray()
        );
    }
}
