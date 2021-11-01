<?php

declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Entites;

class User
{
    public $id;
    public string $username;
    public string $email;
    public $profile = null;
    public iterable $comments;
    public iterable $tags;

    public function __construct(iterable $comments = [], iterable $tags = [])
    {
        $this->comments = $comments;
        $this->tags = $tags;
    }

    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
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
