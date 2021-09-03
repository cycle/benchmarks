<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\Tag;
use Cycle\Benchmarks\Base\Entites\TagContext;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Repositories\UserRepository;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class UserSchema implements SchemaInterface
{
    protected array $schema = [
        Schema::ROLE => 'user',
        Schema::DATABASE => 'default',
        Schema::REPOSITORY => UserRepository::class,
        Schema::TABLE => 'user',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'username', 'email'],
        Schema::TYPECAST => [
            'id' => 'int',
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(private string $key = User::class)
    {
    }

    public function withProfileRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['profile'] = [
            Relation::TYPE => Relation::HAS_ONE,
            Relation::TARGET => UserProfile::class,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'user_id',
            ],
        ];

        return $this;
    }

    public function withCommentsRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['comments'] = [
            Relation::TYPE => Relation::HAS_MANY,
            Relation::TARGET => Comment::class,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'user_id',
            ],
        ];

        return $this;
    }

    public function withTagsRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['tags'] = [
            Relation::TYPE => Relation::MANY_TO_MANY,
            Relation::TARGET => Tag::class,
            // Relation::LOAD => Relation::LOAD_PROMISE,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::THROUGH_ENTITY => TagContext::class,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'id',
                Relation::THROUGH_INNER_KEY => 'user_id',
                Relation::THROUGH_OUTER_KEY => 'tag_id',
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
