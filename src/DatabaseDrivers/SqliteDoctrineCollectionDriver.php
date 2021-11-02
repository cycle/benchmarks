<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers;

use Cycle\ORM\Collection\CollectionFactoryInterface;
use Cycle\ORM\Collection\DoctrineCollectionFactory;

final class SqliteDoctrineCollectionDriver extends SqliteDriver
{
    protected function getCollectionFactory(): CollectionFactoryInterface
    {
        return new DoctrineCollectionFactory();
    }
}
