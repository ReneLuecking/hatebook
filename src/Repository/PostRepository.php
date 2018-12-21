<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class PostRepository extends ServiceEntityRepository
{
    /**
     * PostRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Gibt die neuesten Post-IDs für die Startseite eines Nutzers zurück
     *
     * @param int $a_user_id User-ID
     * @param int $a_offset Offset
     * @param int $a_limit Limit
     * @param string $search Search
     *
     * @return mixed
     */
    public function findNewest($a_user_id, $a_offset = 0, $a_limit = 9999, $search = '')
    {
        $sql = <<<SQL
SELECT id FROM post WHERE ( user_id IN (
    SELECT initiator_id FROM enemy WHERE recipient_id = :user_id AND is_accepted = 1
	UNION
	SELECT recipient_id FROM enemy WHERE initiator_id = :user_id AND is_accepted = 1
)
OR user_id = :user_id )
ORDER BY datetime DESC
LIMIT :limit OFFSET :offset
SQL;
        if (!empty($search)) {
            $s = ' AND text LIKE :search ORDER BY';
            $sql = str_replace('ORDER BY', $s, $sql);
        }

        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindParam('user_id', $a_user_id);

        if (!empty($search)) {
            $search = '%'.$search.'%';
            $stmt->bindParam('search', $search);
        }

        $stmt->bindParam('offset', $a_offset);
        $stmt->bindParam('limit', $a_limit);
        $stmt->execute();
        $result = [];
        while ($ds = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = (int)$ds[ 'id' ];
        }

        return $result;
    }
}
