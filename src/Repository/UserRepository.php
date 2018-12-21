<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (array_key_exists('email', $criteria)) {
            $criteria[ 'email' ] = strtolower($criteria[ 'email' ]);
        }

        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $search
     * @return mixed
     */
    public function search($search)
    {
        $query = $this->createQueryBuilder('u');

        $i = 0;

        foreach ($search as $s) {
            $s = '%'.$s.'%';

            $query->orWhere('LOWER(u.firstName) LIKE :s'.$i)
                ->orWhere('LOWER(u.lastName) LIKE :s'.$i)
                ->orWhere('LOWER(u.email) LIKE :s'.$i)
                ->setParameter('s'.$i, $s);

            $i++;
        }

        return $query->orderBy('u.firstName')
            ->addOrderBy('u.lastName')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
