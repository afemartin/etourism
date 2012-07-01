<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
{
    public function findAllFiltered($organization = null, $activity = null, $dateStart = null, $dateEnd = null, $daysWeek = null, $status = null)
    {
        $qb = $this->createQueryBuilder('s');
        
        $qb->select('s');
        
        if ($organization !== null)
        {
            $qb->innerJoin('s.activity', 'a');
            $qb->where('a.organization = :organization');
            $qb->setParameter('organization', $organization);
        }
        else
        {
            $qb->where('1 = 1');
        }
        
        if ($activity !== null)
        {
            $qb->andWhere('s.activity = :activity');
            $qb->setParameter('activity', $activity);
        }
        
        if ($dateStart !== null)
        {
            $qb->andWhere('s.date >= :start_date');
            $qb->setParameter('start_date', $dateStart);
        }
        
        if ($dateEnd !== null)
        {
            $qb->andWhere('s.date <= :start_end');
            $qb->setParameter('start_end', $dateEnd);
        }
        
        if ($daysWeek !== null && !empty($daysWeek))
        {
            $qb->andWhere('s.dayOfWeek IN (:days_week)');
            $qb->setParameter('days_week', $daysWeek);
        }
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('s.status IN (:status)');
            $qb->setParameter('status', $status);
        }

        $qb->orderBy('s.date', 'ASC');
        $qb->addOrderBy('s.time', 'ASC');

        // var_dump($qb->getDQL());
        // var_dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }
    
}

?>
