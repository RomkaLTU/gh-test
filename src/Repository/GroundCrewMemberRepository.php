<?php

namespace App\Repository;

use App\Entity\GroundCrewMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroundCrewMember>
 */
class GroundCrewMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroundCrewMember::class);
    }

    public function findOneAvailableMemberWithSkill(string $skillId): ?GroundCrewMember
    {
        return $this->createQueryBuilder('gcm')
            ->leftJoin('gcm.skills', 's')
            ->leftJoin('gcm.tasks', 't')
            ->where('s.id = :skillId')
            ->groupBy('gcm.id')
            ->orderBy('COUNT(t.id)', 'ASC')
            ->setParameter('skillId', $skillId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
