<?php

namespace Arcadia\Bundle\AuthorizationBundle\Repository;

use Arcadia\Bundle\AuthorizationBundle\Entity\TokenUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenUser[]    findAll()
 * @method TokenUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenUser::class);
    }
}
