<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Cycle\Benchmarks\Base\BaseCycleOrmEntityFactory;
use Cycle\Benchmarks\Base\Entites\UserWithoutProfile;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Faker\Generator;

class UserWithoutProfileConfigurator extends AbstractConfigurator
{
    private Factory $factory;
    private BaseCycleOrmEntityFactory $entityFactory;

    public function __construct(ORMInterface $orm, string $entityFactory)
    {
        parent::__construct(
            $orm,
            new Schema(
                [
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
                ]
            )
        );

        $this->entityFactory = new $entityFactory($this->getOrm());

        $this->factory = new Factory(
            $this->entityFactory,
            \Faker\Factory::create()
        );
    }

    public function configure(): void
    {
        $this->makeTable('user', [
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

    public function getFactory(): Factory
    {
        return $this->factory;
    }
}
