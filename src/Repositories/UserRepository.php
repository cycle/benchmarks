<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Repositories;

use Cycle\Benchmarks\Base\Entites\User;
use Cycle\ORM\Select\JoinableLoader;
use Cycle\ORM\Select\Repository;

class UserRepository extends Repository
{
    public function findByPKWithProfile(int $id, bool $eager = false, int $loader = JoinableLoader::POSTLOAD): User
    {
        $select = $this->select()->wherePK($id);
        if ($eager) {
            $select->load('profile', ['method' => $loader]);
        }

        return $select->fetchOne();
    }

    public function findByPKWithComments(int $id, bool $eager = false, int $loader = JoinableLoader::POSTLOAD): User
    {
        $select = $this->select()->wherePK($id);
        if ($eager) {
            $select->load('comments', ['method' => $loader]);
        }

        return $select->fetchOne();
    }

    public function findByPKWithTags(int $id, bool $eager = false, int $loader = JoinableLoader::POSTLOAD): User
    {
        $select = $this->select()->wherePK($id);
        if ($eager) {
            $select->load('tags', ['method' => $loader]);
        }

        return $select->fetchOne();
    }
}
