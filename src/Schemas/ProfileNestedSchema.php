<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Schemas;

use Cycle\Benchmarks\Base\Entites\ProfileNested;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

class ProfileNestedSchema implements SchemaInterface
{
    protected array $schema = [
        Schema::ROLE => 'nested',
        Schema::DATABASE => 'default',
        Schema::TABLE => 'nested',
        Schema::PRIMARY_KEY => 'id',
        Schema::COLUMNS => ['id', 'profile_id', 'label'],
        Schema::TYPECAST => [
            'id' => 'int'
        ],
        Schema::SCHEMA => [],
        Schema::RELATIONS => [],
    ];

    public function __construct(private string $key = ProfileNested::class)
    {
    }

    public function withProfileRelation(bool $cascade = true): self
    {
        $this->schema[Schema::RELATIONS]['profile'] = [
            Relation::TYPE => Relation::BELONGS_TO,
            Relation::TARGET => UserProfile::class,
            Relation::SCHEMA => [
                Relation::CASCADE => $cascade,
                Relation::INNER_KEY => 'profile_id',
                Relation::OUTER_KEY => 'id',
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
