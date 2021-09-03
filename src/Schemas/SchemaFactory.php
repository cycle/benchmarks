<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

class SchemaFactory implements SchemaInterface
{
    private array $schemas;

    public static function create(string $mapper, SchemaInterface ...$schemas): self
    {
        return new self($mapper, ...$schemas);
    }

    public function __construct(string $mapper, SchemaInterface ...$schemas)
    {
        $this->schemas = $schemas;
        $this->setMapper($mapper);
    }

    public function toArray(): array
    {
        $schema = [];

        foreach ($this->schemas as $s) {
            $schema = array_merge($schema, $s->toArray());
        }

        return $schema;
    }

    public function setMapper(string $mapper): void
    {
        foreach ($this->schemas as $schema) {
            $schema->setMapper($mapper);
        }
    }
}
