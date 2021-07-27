<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\Tag;
use Cycle\Benchmarks\Base\Entites\TagContext;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Repositories\TagRepository;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class TagSchema implements SchemaInterface
{
    protected array $schema = [
        Schema::ROLE => 'tag',
        Schema::DATABASE => 'default',
        Schema::REPOSITORY => TagRepository::class,
        Schema::TABLE => 'tag',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'name'],
        Schema::TYPECAST => [
            'id' => 'int'
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(private string $key = Tag::class)
    {
    }

    public function withUsersRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['users'] = [
            Relation::TYPE => Relation::MANY_TO_MANY,
            Relation::TARGET => User::class,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::THROUGH_ENTITY => TagContext::class,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'id',
                Relation::THROUGH_OUTER_KEY => 'user_id',
                Relation::THROUGH_INNER_KEY => 'tag_id',
            ],
        ];

        return $this;
    }

    public function toArray(): array
    {
        return [
            $this->key => $this->schema
        ];
    }

    public function setMapper(string $mapper): void
    {
        $this->schema[Schema::MAPPER] = $mapper;
    }
}
