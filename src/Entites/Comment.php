<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class Comment
{
    private int $id;
    private string $text;
    private $user;

    public function setUser($user)
    {
        $this->user = $user;
    }
}
