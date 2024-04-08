<?php

namespace App\Repository;

use App\Entity\QuestionResultEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionResultEntity>
 *
 * @method QuestionResultEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionResultEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionResultEntity[]    findAll()
 * @method QuestionResultEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionResultEntity::class);
    }
}
