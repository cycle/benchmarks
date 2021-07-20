<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Repositories\CommentRepository;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class CommentSchema
{
    protected array $schema = [
        Schema::REPOSITORY => CommentRepository::class,
        Schema::DATABASE => 'default',
        Schema::TABLE => 'comment',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'text', 'user_id'],
        Schema::TYPECAST => [
            'id' => 'int',
            'user_id' => 'int'
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(string $mapper)
    {
        $this->schema[Schema::MAPPER] = $mapper;
    }

    public function withUserRelation(): self
    {
        $this->schema[Schema::RELATIONS]['user'] = [
            Relation::TYPE => Relation::BELONGS_TO,
            Relation::TARGET => User::class,
            Relation::SCHEMA => [
                Relation::CASCADE => true,
                Relation::INNER_KEY => 'user_id',
                Relation::OUTER_KEY => 'id',
            ],
        ];

        return $this;
    }

    public function toArray(): array
    {
        return [
            Comment::class => $this->schema
        ];
    }
}
