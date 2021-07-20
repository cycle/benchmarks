<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Repositories;

use Cycle\Benchmarks\Base\Entites\User;
use Cycle\ORM\Select\Repository;

class UserRepository extends Repository
{
    public function findByPKWithProfile(int $id): User
    {
        return $this->select()->load('profile')->wherePK($id)->fetchOne();
    }
}
