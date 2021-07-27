<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class Comment
{
    public $id;
    public string $text;
    public $user;

    public function setUser($user)
    {
        $this->user = $user;
    }
}
