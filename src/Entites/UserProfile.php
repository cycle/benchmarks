<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class UserProfile
{
    private int $id;
    private $user = null;
    private string $fullName;

    public function setUser($user)
    {
        $this->user = $user;
    }
}
