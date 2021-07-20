<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

use Doctrine\Common\Collections\Collection;

class User
{
    private int $id;
    private string $username;
    private string $email;
    public $profile = null;
    public Collection $comments;

    public function setProfile($profile)
    {
        $this->profile = $profile;
        $profile->setUser($this);
    }

    public function addComment($comment)
    {
        $this->comments[] = $comment;
        $comment->setUser($this);
    }
}
