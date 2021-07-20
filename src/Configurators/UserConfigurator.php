<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Repositories\CommentRepository;
use Cycle\Benchmarks\Base\Repositories\UserProfileRepository;
use Cycle\Benchmarks\Base\Repositories\UserRepository;
use Cycle\Benchmarks\Base\Seeds\Seeds;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Generator;

class UserConfigurator extends AbstractConfigurator
{
    public function createTables(): void
    {
        $this->getDriver()->createTable('user', ['id' => 'integer', 'username' => 'string', 'email' => 'string']);
        $this->getDriver()->createTable('profile', ['id' => 'integer', 'fullName' => 'string', 'user_id' => 'integer']);
        $this->getDriver()->createTable('comment', ['id' => 'integer', 'text' => 'string', 'user_id' => 'integer']);
    }

    public function defineEntities(Factory $factory): void
    {
        $factory->define(User::class, function (Generator $faker, array $attributes) {
            return [
                'id' => $faker->numberBetween(1, 10000),
                'username' => $faker->userName,
                'email' => $faker->email,
                'comments' => new ArrayCollection()
            ];
        });

        $factory->define(UserProfile::class, function (Generator $faker, array $attributes) {
            return [
                'id' => $faker->numberBetween(1, 10000),
                'fullName' => $faker->firstName . ' ' . $faker->lastName
            ];
        });

        $this->getFactory()->define(Comment::class, function (Generator $faker, array $attributes) {
            return [
                'id' => $faker->numberBetween(1, 10000),
                'text' => $faker->text
            ];
        });
    }

    public function getUserRepository(): UserRepository
    {
        return $this->getDriver()->getRepository(User::class);
    }

    public function getUserProfileRepository(): UserProfileRepository
    {
        return $this->getDriver()->getRepository(UserProfile::class);
    }

    public function getCommentRepository(): CommentRepository
    {
        return $this->getDriver()->getRepository(Comment::class);
    }

    public function getUserSeeds(): Seeds
    {
        return $this->getSeeds()->get(User::class);
    }

    public function getUserProfileSeeds(): Seeds
    {
        return $this->getSeeds()->get(UserProfile::class);
    }

    public function getCommentSeeds(): Seeds
    {
        return $this->getSeeds()->get(Comment::class);
    }

    public function getSchema(): array
    {
        return [
            User::class => [
                Schema::MAPPER => Mapper::class,
                Schema::DATABASE => 'default',
                Schema::REPOSITORY => UserRepository::class,
                Schema::TABLE => 'user',
                Schema::PRIMARY_KEY => 'id',
                Schema::COLUMNS => ['id', 'username', 'email'],
                Schema::TYPECAST => [
                    'id' => 'int'
                ],
                Schema::SCHEMA => [],
                Schema::RELATIONS => [
                    'profile' => [
                        Relation::TYPE => Relation::HAS_ONE,
                        Relation::TARGET => UserProfile::class,
                        Relation::SCHEMA => [
                            Relation::CASCADE => true,
                            Relation::INNER_KEY => 'id',
                            Relation::OUTER_KEY => 'user_id',
                        ],
                    ],
                    'comments' => [
                        Relation::TYPE => Relation::HAS_MANY,
                        Relation::TARGET => Comment::class,
                        Relation::SCHEMA => [
                            Relation::CASCADE => true,
                            Relation::INNER_KEY => 'id',
                            Relation::OUTER_KEY => 'user_id',
                        ],
                    ]
                ],
            ],
            UserProfile::class => [
                Schema::MAPPER => Mapper::class,
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
                Schema::RELATIONS => [
                    'user' => [
                        Relation::TYPE => Relation::BELONGS_TO,
                        Relation::TARGET => User::class,
                        Relation::SCHEMA => [
                            Relation::CASCADE => true,
                            Relation::INNER_KEY => 'user_id',
                            Relation::OUTER_KEY => 'id',
                        ],
                    ]
                ]
            ],
            Comment::class => [
                Schema::MAPPER => Mapper::class,
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
                Schema::RELATIONS => [
                    'user' => [
                        Relation::TYPE => Relation::BELONGS_TO,
                        Relation::TARGET => User::class,
                        Relation::SCHEMA => [
                            Relation::CASCADE => true,
                            Relation::INNER_KEY => 'user_id',
                            Relation::OUTER_KEY => 'id',
                        ],
                    ]
                ]
            ]
        ];
    }
}
