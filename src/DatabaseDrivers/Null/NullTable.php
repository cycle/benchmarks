<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers\Null;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractForeignKey;
use Cycle\Database\Schema\AbstractIndex;
use Cycle\Database\Schema\AbstractTable;

class NullTable extends AbstractTable
{

    protected function fetchColumns(): array
    {
        return [];
    }

    protected function fetchIndexes(): array
    {
        return [];
    }

    protected function fetchReferences(): array
    {
        return [];
    }

    protected function fetchPrimaryKeys(): array
    {
        return [];
    }

    protected function createColumn(string $name): AbstractColumn
    {
        // TODO: Implement createColumn() method.
    }

    protected function createIndex(string $name): AbstractIndex
    {
        // TODO: Implement createIndex() method.
    }

    protected function createForeign(string $name): AbstractForeignKey
    {
        // TODO: Implement createForeign() method.
    }
}
