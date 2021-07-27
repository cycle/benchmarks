<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Repositories;

use Cycle\Benchmarks\Base\Entites\User;
use Cycle\ORM\Select\JoinableLoader;
use Cycle\ORM\Select\Repository;

class TagRepository extends Repository
{
    public function findAllForUser(int $id, bool $eager = false): array
    {
        $select = $this->select()->where('users.id', $id);
        if ($eager) {
            $select->load('user');
        }

        return $select
            ->fetchAll();
    }
}
