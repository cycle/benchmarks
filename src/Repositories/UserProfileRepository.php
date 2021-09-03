<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Repositories;

use Cycle\Benchmarks\Base\Entites\UserProfile;
use Cycle\ORM\Select\JoinableLoader;
use Cycle\ORM\Select\Repository;

class UserProfileRepository extends Repository
{
    public function findByPKWithUser(int $id, bool $eager = false, int $loader = JoinableLoader::POSTLOAD): ?UserProfile
    {
        $select = $this->select()->wherePK($id);
        if ($eager) {
            $select->load('user', ['method' => $loader]);
        }

        return $select->fetchOne();
    }

    public function findByPKWithUserAndNested(int $id, bool $eager = false, int $loader = JoinableLoader::POSTLOAD): ?UserProfile
    {
        $select = $this->select()->wherePK($id);
        if ($eager) {
            $select->load('user', ['method' => $loader]);
            $select->load('nested', ['method' => $loader]);
        }

        return $select->fetchOne();
    }
}
