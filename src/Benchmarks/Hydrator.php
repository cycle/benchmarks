<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

class Hydrator extends DatabaseBenchmark
{
    public Seeds $userSeeds;

    public function setUp(array $bindings = []): void
    {
        parent::setUp($bindings);

        $this->userSeeds = $this->getConfigurator()->getUserSeeds();
    }

    /**
     * @Subject
     * @Groups({"hydrate"})
     * @BeforeMethods("setUp")
     * @AfterMethods("tearDown")
     * @Revs(2000)
     * @Iterations(5)
     */
    public function hydrate(): void
    {
        $entityFactory = $this->getEntityFactory();
        $entity = new User();
        $entityFactory->hydrate($entity, $this->userSeeds->first());
    }

    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            new UserSchema()
        )->toArray();
    }
}
