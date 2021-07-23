<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Configurators\ConfiguratorInterface;
use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class UserWithoutProfileSelect extends Benchmark
{
    public Seeds $userSeeds;

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

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
    }

    /**
     * @Subject
     * @Groups({"hydrate"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function makeUser(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = $entityFactory->create($this->userSeeds->getClass());
        $entityFactory->hydrate($entity, $this->userSeeds->first());
    }

    /**
     * @Subject
     * @Groups({"select"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     */
    public function findByPk(): void
    {
        $this->getConfigurator()
            ->getUserRepository()
            ->findByPK(123);
    }

    public function getSchema(string $mapper): array
    {
        return (new UserSchema($mapper))->toArray();
    }
}
