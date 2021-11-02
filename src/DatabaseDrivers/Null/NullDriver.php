<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\DatabaseDrivers\Null;

use Faker\Generator;
use Cycle\Database\Driver\Driver;
use Cycle\Database\Driver\SQLite\SQLiteCompiler;
use Cycle\Database\Exception\StatementException;
use Cycle\Database\Query\QueryBuilder;
use Cycle\Database\StatementInterface;
use Throwable;

class NullDriver extends Driver
{
    private Generator $faker;

    public function __construct(array $options)
    {
        parent::__construct(
            $options,
            new NullDriverHandler(),
            new SQLiteCompiler('""'),
            QueryBuilder::defaultBuilder()
        );

        $this->faker = \Faker\Factory::create();
    }

    public function isConnected(): bool
    {
        return true;
    }

    public function connect(): void
    {
    }

    public function disconnect(): void
    {
    }

    public function quote($value, int $type = \PDO::PARAM_STR): string
    {
        return $value;
    }

    protected function mapException(Throwable $exception, string $query): StatementException
    {
        throw $exception;
    }

    public function getType(): string
    {
        // TODO: Implement getType() method.
    }

    public function lastInsertID(string $sequence = null)
    {
        return $this->faker->uuid;
    }

    public function beginTransaction(string $isolationLevel = null): bool
    {
        return true;
    }

    public function commitTransaction(): bool
    {
        return true;
    }

    public function rollbackTransaction(): bool
    {
        return true;
    }

    protected function statement(
        string $query,
        iterable $parameters = [],
        bool $retry = true
    ): StatementInterface {
        return new NullStatement();
    }
}
