<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class ProfileNested
{
    public $label;
    /** @var UserProfile */
    public $profile;

    public function setProfile($profile)
    {
        $this->profile = $profile;
    }
}
