<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentHateRepository")
 */
class CommentHate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comment")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     * @var Comment|null
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     * @var User|null
     */
    private $user;

    /**
     * CommentHate constructor.
     */
    public function __construct()
    {
        $this->id = 0;
        $this->comment = null;
        $this->user = null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Comment|null
     */
    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     * @return CommentHate
     */
    public function setComment(Comment $comment): CommentHate
    {
        $this->comment = $comment;

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
     * @return CommentHate
     */
    public function setUser(User $user): CommentHate
    {
        $this->user = $user;

        return $this;
    }
}
