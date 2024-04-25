<?php

namespace App\Repository;

use App\Entity\AnswerEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnswerEntity>
 *
 * @method AnswerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnswerEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnswerEntity[]    findAll()
 * @method AnswerEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnswerEntity::class);
    }
}
