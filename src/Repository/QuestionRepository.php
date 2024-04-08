<?php

namespace App\Repository;

use App\Entity\QuestionEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionEntity>
 *
 * @method QuestionEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionEntity[]    findAll()
 * @method QuestionEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionEntity::class);
    }

    public function getNewRandomQuestion(string $sessionID): ?QuestionEntity
    {
        $sql = <<<SQL
select q.*
from question q
left join question_result qr on q.id = qr.question_id and qr.session_id = :session_id
where qr.session_id is null
order by random()
limit 1;
SQL;

        $em = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata(QuestionEntity::class, 'q');

        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter('session_id', $sessionID);

        return $query->getOneOrNullResult();
    }
}
