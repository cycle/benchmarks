<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\EntityFactory;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;

abstract class BaseCycleOrmEntityFactory extends AbstractEntityFactory
{
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
}
