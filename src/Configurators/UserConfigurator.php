<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Configurators;

use Butschster\EntityFaker\Factory;
use Butschster\EntityFaker\Seeds\Seeds;
use Cycle\Benchmarks\Base\Entites\Comment;
use Cycle\Benchmarks\Base\Entites\ProfileNested;
use Cycle\Benchmarks\Base\Entites\Tag;
use Cycle\Benchmarks\Base\Entites\TagContext;
use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\Benchmarks\Base\Entites\User;
use Cycle\Benchmarks\Base\Repositories\CommentRepository;
use Cycle\Benchmarks\Base\Repositories\TagRepository;
use Cycle\Benchmarks\Base\Repositories\UserProfileRepository;
use Cycle\Benchmarks\Base\Repositories\UserRepository;
use Cycle\ORM\Select\Repository;
use Faker\Generator;

class UserConfigurator extends AbstractConfigurator
{
    public function createTables(): void
    {
        $this->getDriver()
            ->createTable('user', columns: ['id' => 'primary', 'username' => 'string', 'email' => 'string'])
            ->createTable('tags', columns: ['id' => 'primary', 'name' => 'string'], )
            ->createTable('tag_user_map', columns: ['id' => 'primary', 'user_id' => 'integer', 'tag_id' => 'integer', 'as' => 'string,nullable'], )
            ->makeFK('tag_user_map', 'user_id', 'user', 'id')
            ->makeFK('tag_user_map', 'tag_id', 'tags', 'id')
            ->createTable('profile', columns: ['id' => 'primary', 'fullName' => 'string', 'user_id' => 'integer'], )
            ->makeFK('profile', 'user_id', 'user', 'id')
            ->createTable('nested', columns: ['id' => 'primary', 'label' => 'string', 'profile_id' => 'integer'], )
            ->makeFK('nested', 'profile_id', 'profile', 'id')
            ->createTable('comment', columns: ['id' => 'primary', 'text' => 'string', 'user_id' => 'integer'], )
            ->makeFK('comment', 'user_id', 'user', 'id');
    }

    public function defineEntities(Factory $factory): void
    {
        $factory->define(User::class, function (Generator $faker, array $attributes) {
            return [
                'username' => $faker->userName,
                'email' => $faker->email,
            ];
        });

        $factory->define(UserProfile::class, function (Generator $faker, array $attributes) {
            return [
                'fullName' => $faker->firstName . ' ' . $faker->lastName,
            ];
        });

        $factory->define(ProfileNested::class, function (Generator $faker, array $attributes) {
            return [
                'label' => $faker->word,
            ];
        });

        $this->getFactory()->define(Comment::class, function (Generator $faker, array $attributes) {
            return [
                'text' => $faker->text,
            ];
        });

        $this->getFactory()->define(Tag::class, function (Generator $faker, array $attributes) {
            return [
                'name' => $faker->word,
            ];
        });

        $this->getFactory()->define(TagContext::class, function (Generator $faker, array $attributes) {
            return [
                'as' => $faker->randomElement(['primary', 'secondary']),
            ];
        });
    }

    public function getUserRepository(string $role = User::class): UserRepository
    {
        return $this->getDriver()->getRepository($role);
    }

    public function getUserProfileRepository(string $role = UserProfile::class): UserProfileRepository
    {
        return $this->getDriver()->getRepository($role);
    }

    public function getProfileNestedRepository(string $role = ProfileNested::class): Repository
    {
        return $this->getDriver()->getRepository($role);
    }

    public function getCommentRepository(string $role = Comment::class): CommentRepository
    {
        return $this->getDriver()->getRepository($role);
    }

    public function geTagRepository(string $role = Tag::class): TagRepository
    {
        return $this->getDriver()->getRepository($role);
    }

    public function getTagSeeds(): Seeds
    {
        return $this->getSeeds()->get(Tag::class);
    }

    public function getTagContextSeeds(): Seeds
    {
        return $this->getSeeds()->get(TagContext::class);
    }

    public function getUserSeeds(): Seeds
    {
        return $this->getSeeds()->get(User::class);
    }

    public function getUserProfileSeeds(): Seeds
    {
        return $this->getSeeds()->get(UserProfile::class);
    }

    public function getProfileNestedSeeds(): Seeds
    {
        return $this->getSeeds()->get(ProfileNested::class);
    }

    public function getCommentSeeds(): Seeds
    {
        return $this->getSeeds()->get(Comment::class);
    }
}
