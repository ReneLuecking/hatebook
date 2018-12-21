<?php

namespace App\Repository;

use App\Entity\Enemy;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EnemyRepository extends ServiceEntityRepository
{
    const STATUS_EMPTY = 0;
    const STATUS_OPEN = 1;
    const STATUS_SEND = 2;
    const STATUS_ACCEPTED = 3;

    /**
     * EnemyRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Enemy::class);
    }

    /**
     * @param int $userId
     * @param bool $accepted
     * @return array
     */
    public function findEnemiesByUser(int $userId, bool $accepted = true)
    {
        $enemies = array();

        try {
            $user = $this->getEntityManager()->find(User::class, $userId);
        } catch (\Exception $e) {
            return $enemies;
        }

        $recipients = $this->findBy(
            array(
                'initiator' => $user,
                'isAccepted' => $accepted,
            )
        );

        /** @var Enemy $recipient */
        foreach ($recipients as $recipient) {
            $enemies[] = $recipient->getRecipient();
        }

        $initiators = $this->findBy(
            array(
                'recipient' => $user,
                'isAccepted' => $accepted,
            )
        );

        /** @var Enemy $initiator */
        foreach ($initiators as $initiator) {
            $enemies[] = $initiator->getInitiator();
        }

        return $enemies;
    }

    /**
     * @param User $a_own_user
     * @param User $a_other_user
     * @return int
     */
    public function get_enemy_status($a_own_user, $a_other_user)
    {
        $send = $this->findBy(
            array(
                'initiator' => $a_own_user,
                'recipient' => $a_other_user,
            )
        );

        $received = $this->findBy(
            array(
                'initiator' => $a_other_user,
                'recipient' => $a_own_user,
            )
        );

        /** @var Enemy $enemy */
        foreach ($received as $enemy) {
            if ($enemy->isAccepted()) {
                return self::STATUS_ACCEPTED;
            } else {
                return self::STATUS_OPEN;
            }
        }

        foreach ($send as $enemy) {
            if ($enemy->isAccepted()) {
                return self::STATUS_ACCEPTED;
            } else {
                return self::STATUS_SEND;
            }
        }

        return self::STATUS_EMPTY;
    }
}
