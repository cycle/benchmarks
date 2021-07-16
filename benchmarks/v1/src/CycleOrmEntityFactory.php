<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\v1;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\BaseCycleOrmEntityFactory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;

class CycleOrmEntityFactory extends BaseCycleOrmEntityFactory
{
    public function create(string $class): object
    {
        return $this->orm->getMapper($class)->init([])[0];
    }
}
