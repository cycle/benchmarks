<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\EntityFactory;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;

abstract class BaseCycleOrmEntityFactory extends AbstractEntityFactory
{
    protected Transaction $transaction;

    public function __construct(protected ORMInterface $orm)
    {
        $this->createTransaction();

        $this->beforeCreation(function () {
            $this->createTransaction();
        });

        $this->afterCreation(function () {
            $this->commitTransaction();
        });
    }

    public function store(object $entity, array $options = []): void
    {
        $this->transaction->persist(
            $entity,
            $options['mode'] ?? Transaction::MODE_CASCADE
        );
    }

    public function hydrate(object $entity, array $data): object
    {
        return $this->orm->getMapper($entity)->hydrate($entity, $data);
    }

    private function createTransaction(): void
    {
        $this->transaction = new Transaction($this->orm);
    }

    private function commitTransaction(): void
    {
        $this->transaction->run();
    }
}
