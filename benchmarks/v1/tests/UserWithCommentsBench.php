<?php
declare(strict_types=1);

namespace Benchmarks\v1;

use Butschster\EntityFaker\EntityFactoryInterface;
use Cycle\Benchmarks\Base\DatabaseDrivers\CycleOrmDriver;
use Cycle\Benchmarks\Base\DatabaseDrivers\DriverInterface;
use Cycle\Benchmarks\v1\CycleOrmEntityFactory;

class UserWithCommentsBench extends \Cycle\Benchmarks\Base\Benchmarks\UserWithCommentsBench
{
    public function setUp(): void
    {
        $this->getContainer()->bind(DriverInterface::class, CycleOrmDriver::class);
        $this->getContainer()->bind(EntityFactoryInterface::class, CycleOrmEntityFactory::class);

        parent::setUp();
    }
}
