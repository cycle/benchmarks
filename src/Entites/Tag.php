<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Tag
{
    public $id;
    public $name;
    public Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUser(User $user)
    {
        $this->users->add($user);
    }
}
