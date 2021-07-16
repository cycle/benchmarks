<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Cycle\Benchmarks\Base\Entites\UserWithoutProfile;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Schema;
use Faker\Generator;

class UserWithoutProfileConfigurator extends AbstractConfigurator
{
    public function configure(): void
    {
        parent::configure();

        $this->getDriver()->createTable('user', [
            'id' => 'string', 'username' => 'string', 'email' => 'string'
        ]);

        $this->getFactory()->define(UserWithoutProfile::class, function (Generator $faker, array $attributes) {
            return [
                'id' => $faker->uuid,
                'username' => $faker->userName,
                'email' => $faker->email
            ];
        });

        $this->seedEntityData();
    }

    public function getSchema(): array
    {
        if (!class_exists('Cycle\ORM\Schema')) {
            return [];
        }

        return [
            UserWithoutProfile::class => [
                Schema::MAPPER => Mapper::class,
                Schema::DATABASE => 'default',
                Schema::TABLE => 'user',
                Schema::PRIMARY_KEY => 'uuid',
                Schema::COLUMNS => ['id', 'username', 'email'],
                Schema::TYPECAST => [],
                Schema::SCHEMA => [],
                Schema::RELATIONS => []
            ]
        ];
    }
}
