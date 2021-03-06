<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository
{
    
    public function findAllFiltered($organization = null, $activity = null, $dateStart = null, $dateEnd = null, $sessionDateStart = null, $sessionDateEnd = null, $status = null)
    {
        $qb = $this->createQueryBuilder('p');
        
        $qb->select('p');
        
        if ($organization !== null || $activity !== null || $sessionDateStart !== null || $sessionDateEnd !== null)
        {
            $qb->innerJoin('p.reservation', 'r');
            $qb->innerJoin('r.session', 's');
        }
        
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
        
        if ($sessionDateStart !== null)
        {
            $qb->andWhere('s.date >= :session_date_start');
            $qb->setParameter('session_date_start', $sessionDateStart);
        }
        
        if ($sessionDateEnd !== null)
        {
            $qb->andWhere('s.date <= :session_date_end');
            $qb->setParameter('session_date_end', $sessionDateEnd);
        }
        
        if ($dateStart !== null)
        {
            $qb->andWhere('p.created >= :date_start');
            $qb->setParameter('date_start', $dateStart);
        }
        
        if ($dateEnd !== null)
        {
            $qb->andWhere('p.created <= :date_end');
            $qb->setParameter('date_end', $dateEnd);
        }
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('p.status IN (:status)');
            $qb->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
    
    
    public function findUpdatedPayments($organization = null, $dateStart = null, $dateEnd = null, $status = null)
    {
        $qb = $this->createQueryBuilder('p');
        
        $qb->select('p');
        
        if ($organization !== null)
        {
            $qb->innerJoin('p.reservation', 'r');
            $qb->innerJoin('r.session', 's');
            $qb->innerJoin('s.activity', 'a');
            $qb->andWhere('a.organization = :organization');
            $qb->setParameter('organization', $organization);
        }
        
        if ($dateStart !== null)
        {
            $qb->andWhere('p.updated >= :date_start');
            $qb->setParameter('date_start', $dateStart);
        }
        
        if ($dateEnd !== null)
        {
            $qb->andWhere('p.updated <= :date_end');
            $qb->setParameter('date_end', $dateEnd);
        }
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('p.status IN (:status)');
            $qb->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
    
}

?>
