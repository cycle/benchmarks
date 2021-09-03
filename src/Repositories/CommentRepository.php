<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Repositories;

use Cycle\ORM\Select\Repository;

class CommentRepository extends Repository
{
    public function findAllForUser(int $id, bool $eager = false): array
    {
        $select = $this->select()->where('user.id', $id);
        if ($eager) {
            $select->load('user');
        }

        return $select
            ->fetchAll();
    }
}
