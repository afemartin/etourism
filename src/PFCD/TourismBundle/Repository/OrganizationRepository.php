<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Organization;

class OrganizationRepository extends EntityRepository
{
    public function findListFront($locale = null, $country = null)
    {
        $qb = $this->createQueryBuilder('o');
        
        $qb->select('o');
        
        $qb->where('o.status IN (:status)');
        $qb->setParameter('status', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));
        
        if ($country !== null)
        {
            $qb->andWhere('o.country = :country');
            $qb->setParameter('country', $country);
        }
        
        if ($locale !== null)
        {
            $qb->andWhere('o.languages LIKE :locale');
            $qb->setParameter('locale', '%'.$locale.'%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findCountriesFront($locale = null)
    {
        $qb = $this->createQueryBuilder('o');
        
        $qb->select('o.country');
        
        $qb->where('o.status IN (:status)');
        $qb->setParameter('status', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));
        
        if ($locale !== null)
        {
            $qb->andWhere('o.languages LIKE :locale');
            $qb->setParameter('locale', '%'.$locale.'%');
        }
        
        $qb->groupBy('o.country');

        return $qb->getQuery()->getResult();
    }

}

?>
