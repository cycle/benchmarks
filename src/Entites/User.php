<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

use Cycle\ORM\Collection\Pivoted\PivotedCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class User
{
    public $id;
    public string $username;
    public string $email;
    public $profile = null;
    public Collection $comments;
    public Collection $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
        $tag->addUser($this);
    }

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
