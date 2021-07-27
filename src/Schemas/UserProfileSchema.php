<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\ProfileNested;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Repositories\UserProfileRepository;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class UserProfileSchema implements SchemaInterface
{
    protected array $schema = [
        Schema::ROLE => 'user_profile',
        Schema::REPOSITORY => UserProfileRepository::class,
        Schema::DATABASE => 'default',
        Schema::TABLE => 'profile',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'fullName', 'user_id'],
        Schema::TYPECAST => [
            'id' => 'int',
            'user_id' => 'int'
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(private string $key = UserProfile::class)
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

    public function withNestedRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['nested'] = [
            Relation::TYPE => Relation::HAS_ONE,
            Relation::TARGET => ProfileNested::class,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::INNER_KEY => 'id',
                Relation::OUTER_KEY => 'profile_id',
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
