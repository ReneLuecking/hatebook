<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostFileRepository")
 */
class PostFile
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
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     * @var File|null
     */
    private $file;

    /**
     * PostFile constructor.
     */
    public function __construct()
    {
        $this->id = 0;
        $this->post = null;
        $this->file = null;
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
     * @return PostFile
     */
    public function setPost(Post $post): PostFile
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return PostFile
     */
    public function setFile(File $file): PostFile
    {
        $this->file = $file;

        return $this;
    }
}
