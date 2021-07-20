<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Repositories;

use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\ORM\Select\Repository;

class UserProfileRepository extends Repository
{
    public function findByPKWithUser(int $id): ?UserProfile
    {
        return $this->select()->load('user')->wherePK($id)->fetchOne();
    }
}
