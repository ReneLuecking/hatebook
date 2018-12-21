<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostHateRepository")
 * @ORM\Table(name="post_hate",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="post_hate_unique_0",
 *            columns={"post_id", "user_id"})
 *    }
 * )
 */
class PostHate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     * @var Post|null
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     * @var User|null
     */
    private $user;

    /**
     * PostHate constructor.
     */
    public function __construct()
    {
        $this->id = 0;
        $this->post = 0;
        $this->user = 0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return PostHate
     */
    public function setPost(Post $post): PostHate
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return PostHate
     */
    public function setUser(User $user): PostHate
    {
        $this->user = $user;

        return $this;
    }
}
