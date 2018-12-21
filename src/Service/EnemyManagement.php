<?php

namespace App\Service;

use App\Repository\EnemyRepository;

class EnemyManagement
{
    /**
     * @var EnemyRepository
     */
    private $enemyRepo;

    /**
     * EnemyManagement constructor.
     * @param EnemyRepository $enemyRepository
     */
    public function __construct(EnemyRepository $enemyRepository)
    {
        $this->enemyRepo = $enemyRepository;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getEnemies(int $userId)
    {
        return $this->enemyRepo->findEnemiesByUser($userId);
    }
}