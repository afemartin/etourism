<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Reservation;

class ResourceRepository extends EntityRepository
{

    public function findAllFiltered($organization = null, $resource = null, $dateStart = null, $dateEnd = null, $status = null)
    {
        $qb = $this->createQueryBuilder('r');
        
        $qb->select('r, a, s');
        
        $qb->innerJoin('r.activities', 'a');
        $qb->where('1 = 1');
        
        if ($resource !== null)
        {
            $qb->andWhere('r.id = :resource');
            $qb->setParameter('resource', $resource);
        }
        
        if ($organization !== null)
        {
            $qb->andWhere('a.organization = :organization');
            $qb->setParameter('organization', $organization);
        }
        
        $qb->innerJoin('a.sessions', 's');
        $qb->innerJoin('s.reservations', 'r2');
        $qb->andWhere('r2.status = :status');
        $qb->setParameter('status', Reservation::STATUS_ACCEPTED);
            
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
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('r.status IN (:status)');
            $qb->setParameter('status', $status);
        }

//        $qb->orderBy('r.updated', 'DESC');

        // var_dump($qb->getDQL());
        // var_dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }

}

?>
