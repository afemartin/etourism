<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Organization;

class OrganizationRepository extends EntityRepository
{

    public function findCountriesFront()
    {
        $qb = $this->createQueryBuilder('o');
        
        $qb->select('o.country');
        
        $qb->where('o.status IN (:status_org)');
        $qb->setParameter('status_org', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));
        
        $qb->groupBy('o.country');

        return $qb->getQuery()->getResult();
    }

}

?>
