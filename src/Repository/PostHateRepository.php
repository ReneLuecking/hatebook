<?php

namespace App\Repository;

use App\Entity\PostHate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PostHateRepository extends ServiceEntityRepository
{
    /**
     * PostHateRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PostHate::class);
    }

    /**
     * @param int $a_post_id
     * @return bool
     */
    public function countByPost($a_post_id)
    {
        $sql = 'SELECT COUNT(*) AS i FROM post_hate WHERE post_id = :post_id';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindParam('post_id', $a_post_id);
        $stmt->execute();
        $ds = $stmt->fetch(\PDO::FETCH_ASSOC);

        return isset($ds[ 'i' ]) ? $ds[ 'i' ] : false;
    }

    /**
     * @param int $a_post_id
     * @param int $a_user_id
     * @return bool
     */
    public function exists($a_post_id, $a_user_id)
    {
        $sql = 'SELECT COUNT(*) AS i FROM post_hate WHERE post_id = :post_id AND user_id = :user_id';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindParam('post_id', $a_post_id);
        $stmt->bindParam('user_id', $a_user_id);
        $stmt->execute();
        $ds = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (!empty($ds[ 'i' ]) && $ds[ 'i' ] > 0) ? true : false;
    }
}
