<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Reservation;

class ResourceRepository extends EntityRepository
{

    public function findAllRequired($organization = null, $dateStart = null, $dateEnd = null, $status = null)
    {
        $qb = $this->createQueryBuilder('r');
        
        $qb->select('r, c, re, s');
        
        $qb->innerJoin('r.category', 'c');
        $qb->where('1 = 1');
        
        if ($organization !== null)
        {
            $qb->andWhere('c.organization = :organization');
            $qb->setParameter('organization', $organization);
        }
        
        $qb->innerJoin('r.reservations', 're');
        $qb->andWhere('re.status = :status');
        $qb->setParameter('status', Reservation::STATUS_ACCEPTED);
        
        $qb->leftJoin('re.session', 's');
        
        if ($dateStart !== null)
        {
            $qb->andWhere('s.endDatetime > :start_date');
            $qb->setParameter('start_date', $dateStart);
        }
        
        if ($dateEnd !== null)
        {
            $qb->andWhere('s.startDatetime < :end_date');
            $qb->setParameter('end_date', $dateEnd);
        }
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('r.status IN (:status)');
            $qb->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
    
    public function findByOrganization($organization, $status = null)
    {
        $qb = $this->createQueryBuilder('r');
        
        $qb->select('r, c');
        
        $qb->innerJoin('r.category', 'c');
        $qb->andWhere('c.organization = :organization');
        $qb->setParameter('organization', $organization);
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('r.status IN (:status)');
            $qb->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

}

?>
