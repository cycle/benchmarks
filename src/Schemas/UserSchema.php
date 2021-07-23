<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Repositories\UserRepository;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class UserSchema
{
    protected array $schema = [
        Schema::ROLE => 'user',
        Schema::DATABASE => 'default',
        Schema::REPOSITORY => UserRepository::class,
        Schema::TABLE => 'user',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'username', 'email'],
        Schema::TYPECAST => [
            'id' => 'int'
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(string $mapper, private string $key = User::class)
    {
        $this->schema[Schema::MAPPER] = $mapper;
    }

    public function withProfileRelation(): self
    {
        $this->schema[Schema::RELATIONS]['profile'] = [
            Relation::TYPE => Relation::HAS_ONE,
            Relation::TARGET => UserProfile::class,
            Relation::SCHEMA => [
                Relation::CASCADE => true,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'user_id',
            ],
        ];

        return $this;
    }

    public function withCommentsRelation(): self
    {
        $this->schema[Schema::RELATIONS]['comments'] = [
            Relation::TYPE => Relation::HAS_MANY,
            Relation::TARGET => Comment::class,
            Relation::SCHEMA => [
                Relation::CASCADE => true,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'user_id',
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
}
