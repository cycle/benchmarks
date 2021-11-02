<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Collection\CollectionFactoryInterface;

final class SqliteArrayCollectionDriver extends SqliteDriver
{
    protected function getCollectionFactory(): CollectionFactoryInterface
    {
        return new ArrayCollectionFactory();
    }
}
