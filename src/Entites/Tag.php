<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class Tag
{
    public $id;
    public $name;
    public iterable $users;

    public function __construct(iterable $users = [])
    {
        $this->users = $users;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }
}
