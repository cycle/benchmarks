<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers\Null;

use Spiral\Database\StatementInterface;

class NullStatement implements StatementInterface
{

    public function getQueryString(): string
    {
        return '';
    }

    public function fetch(int $mode = self::FETCH_ASSOC)
    {
        // TODO: Implement fetch() method.
    }

    public function fetchColumn(int $columnNumber = null)
    {
        // TODO: Implement fetchColumn() method.
    }

    public function fetchAll(int $mode = self::FETCH_ASSOC): array
    {
        return [];
    }

    public function rowCount(): int
    {
        return 0;
    }

    public function columnCount(): int
    {
        return 0;
    }

    public function close()
    {
        // TODO: Implement close() method.
    }
}
