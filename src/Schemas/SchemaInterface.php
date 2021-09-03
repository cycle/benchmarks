<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

interface SchemaInterface
{
    public function toArray(): array;
    public function setMapper(string $mapper): void;
}
