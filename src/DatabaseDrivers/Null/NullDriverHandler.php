<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers\Null;

use Cycle\Database\Driver\Handler;
use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractTable;

class NullDriverHandler extends Handler
{
    public function getTableNames(): array
    {
        return [];
    }

    public function hasTable(string $table): bool
    {
        return true;
    }

    public function getSchema(string $table, string $prefix = null): AbstractTable
    {
        return new NullTable($this->driver, $table, $prefix ?? '');
    }

    public function eraseTable(AbstractTable $table): void
    {
        // TODO: Implement eraseTable() method.
    }

    public function alterColumn(AbstractTable $table, AbstractColumn $initial, AbstractColumn $column): void
    {
        // TODO: Implement alterColumn() method.
    }
}
