<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReservationRepository extends EntityRepository
{
    public function findByOrganization($organization, $status = null)
    {
        $qb = $this->createQueryBuilder('r')
                   ->select('r')
                   ->innerJoin('r.activity', 'a')
                   ->where('a.organization = :organization')
                   ->orderBy('r.status', 'ASC')
                   ->orderBy('r.updated', 'DESC')
                   ->setParameter('organization', $organization);

        if ($status !== null)
            $qb->andWhere('r.status IN (:status)')
               ->setParameter('status', $status);
        
        // var_dump($qb->getDQL());
        // var_dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }
}

?>
