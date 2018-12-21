<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnemyRepository")
 */
class Enemy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(nullable=false, onDelete="cascade")
	 * @var User|null
	 */
	private $initiator;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(nullable=false, onDelete="cascade")
	 * @var User|null
	 */
	private $recipient;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	private $isAccepted;

    /**
     * Enemy constructor.
     */
    public function __construct()
    {
        $this->id = 0;
        $this->initiator = null;
        $this->recipient = null;
        $this->isAccepted = false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getInitiator(): ?User
    {
        return $this->initiator;
    }

    /**
     * @param User $initiator
     * @return Enemy
     */
    public function setInitiator(User $initiator): Enemy
    {
        $this->initiator = $initiator;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    /**
     * @param User $recipient
     * @return Enemy
     */
    public function setRecipient(User $recipient): Enemy
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->isAccepted;
    }

    /**
     * @param bool $isAccepted
     * @return Enemy
     */
    public function setIsAccepted(bool $isAccepted): Enemy
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }
}
