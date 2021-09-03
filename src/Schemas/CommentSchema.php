<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Repositories\CommentRepository;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class CommentSchema implements SchemaInterface
{
    protected array $schema = [
        Schema::ROLE => 'comment',
        Schema::REPOSITORY => CommentRepository::class,
        Schema::DATABASE => 'default',
        Schema::TABLE => 'comment',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'text', 'user_id'],
        Schema::TYPECAST => [
            'id' => 'int',
            'user_id' => 'int',
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(private string $key = Comment::class)
    {
    }

    public function withUserRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['user'] = [
            Relation::TYPE => Relation::BELONGS_TO,
            Relation::TARGET => User::class,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::INNER_KEY => 'user_id',
                Relation::OUTER_KEY => 'id',
            ],
        ];

        return $this;
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
