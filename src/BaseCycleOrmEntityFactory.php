<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base;

use Butschster\EntityFaker\EntityFactoryInterface;
use Closure;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;

abstract class BaseCycleOrmEntityFactory implements EntityFactoryInterface
{
    private array $afterCreation = [];
    private array $beforeCreation = [];

    protected ORMInterface $orm;
    protected Transaction $transaction;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
        $this->transaction = new Transaction($this->orm);

        $this->beforeCreation(function () {
            $this->transaction = new Transaction($this->orm);
        });

        $this->afterCreation(function () {
            $this->transaction->run();
        });
    }

    public function store(object $entity): void
    {
        $this->transaction->persist($entity);
    }

    public function hydrate(object $entity, array $data): object
    {
        return $this->orm->getMapper($entity)->hydrate($entity, $data);
    }

    /**
     * Add a callback to run after creating an entity or array of entities.
     * @param callable $callback
     */
    public function afterCreation(callable $callback): void
    {
        $this->afterCreation[] = $callback;
    }

    public function afterCreationCallbacks(): array
    {
        return $this->afterCreation;
    }

    /**
     * Add a callback to run before creating an entity or array of entities.
     * @param callable $callback
     */
    public function beforeCreation(callable $callback): void
    {
        $this->beforeCreation[] = $callback;
    }

    public function beforeCreationCallbacks(): array
    {
        return $this->beforeCreation;
    }
}
