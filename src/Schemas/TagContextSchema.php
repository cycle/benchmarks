<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\TagContext;
use Cycle\ORM\Schema;

class TagContextSchema implements SchemaInterface
{
    protected array $schema = [
        Schema::ROLE => 'tag_context',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'tag_user_map',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'user_id', 'tag_id', 'as'],
        Schema::TYPECAST => ['id' => 'int', 'user_id' => 'int', 'tag_id' => 'int'],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(private string $key = TagContext::class)
    {
    }

    public function toArray(): array
    {
        return [
            $this->key => $this->schema,
        ];
    }

    public function setMapper(string $mapper): void
    {
        $this->schema[Schema::MAPPER] = $mapper;
    }
}
