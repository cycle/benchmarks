<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class UserProfile
{
    public $id;

    /** @var User */
    public $user = null;

    /** @var ProfileNested */
    public $nested;

    public string $fullName;

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setNested($nested)
    {
        $this->nested = $nested;
        $nested->setProfile($this);
    }
}
