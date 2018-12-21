<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ChatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
     * @param User $sender
     * @param User $recipient
     * @param int $offset
     * @return mixed
     */
    public function findMessagesByUsers(User $sender, User $recipient, int $offset = 0)
    {
        return $this->createQueryBuilder('c')
            ->where('c.sender = :sender AND c.recipient = :recipient')
            ->orWhere('c.sender = :recipient AND c.recipient = :sender')
            ->setParameter('sender', $sender)
            ->setParameter('recipient', $recipient)
            ->orderBy('c.datetime', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $sender
     * @param User $recipient
     * @param \DateTime $dateTime
     * @return mixed
     */
    public function findNewMessagesByUsersAndDate(User $sender, User $recipient, \DateTime $dateTime)
    {
        return $this->createQueryBuilder('c')
            ->where('c.sender = :sender')
            ->andWhere('c.recipient = :recipient')
            ->andWhere('c.datetime > :date')
            ->setParameter('sender', $sender)
            ->setParameter('recipient', $recipient)
            ->setParameter('date', $dateTime)
            ->orderBy('c.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
