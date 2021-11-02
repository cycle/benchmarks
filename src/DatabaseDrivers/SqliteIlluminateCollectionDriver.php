<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Cycle\ORM\Collection\CollectionFactoryInterface;
use Cycle\ORM\Collection\IlluminateCollectionFactory;

final class SqliteIlluminateCollectionDriver extends SqliteDriver
{
    protected function getCollectionFactory(): CollectionFactoryInterface
    {
        return new IlluminateCollectionFactory();
    }
}
