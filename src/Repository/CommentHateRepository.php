<?php

namespace App\Repository;

use App\Entity\CommentHate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentHateRepository extends ServiceEntityRepository
{
    /**
     * CommentHateRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentHate::class);
    }

    /**
     * @param int $a_comment_id
     * @return bool
     */
    public function countByComment($a_comment_id)
    {
        $sql = 'SELECT COUNT(*) AS i FROM comment_hate WHERE comment_id = :comment_id';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindParam('comment_id', $a_comment_id);
        $stmt->execute();
        $ds = $stmt->fetch(\PDO::FETCH_ASSOC);

        return isset($ds[ 'i' ]) ? $ds[ 'i' ] : false;
    }

    /**
     * @param int $a_comment_id
     * @param int $a_user_id
     * @return bool
     */
    public function exists($a_comment_id, $a_user_id)
    {
        $sql = 'SELECT COUNT(*) AS i FROM comment_hate WHERE comment_id = :comment_id AND user_id = :user_id';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindParam('comment_id', $a_comment_id);
        $stmt->bindParam('user_id', $a_user_id);
        $stmt->execute();
        $ds = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (!empty($ds[ 'i' ]) && $ds[ 'i' ] > 0) ? true : false;
    }

}
