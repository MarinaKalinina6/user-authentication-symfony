<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByValue($accessToken): User|null
    {
        return $this->findOneBy(['token' => $accessToken]);
    }

    public function isValidToken(string $accessToken): bool
    {
        $token = $this->findOneByValue($accessToken)->getCreatedToken()->format("Y-m-d H:i:s");
        $endActionToken = date('Y-m-d H:i:s', strtotime($token) + 3600);
        $currentTime = date("Y-m-d H:i:s");

        if (strtotime($endActionToken) > strtotime($currentTime)) {
            return true;
        }

        return false;
    }
}