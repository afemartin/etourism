<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    
    public function findAllFiltered($organization = null)
    {
        $qb = $this->createQueryBuilder('c');
        
        $qb->select('c');

        if ($organization !== null)
        {
            $qb->leftJoin('c.activity', 'act');
            $qb->where('act.organization = :organization_act');
            $qb->setParameter('organization_act', $organization);
            
            $qb->leftJoin('c.article', 'art');
            $qb->orWhere('art.organization = :organization_art');
            $qb->setParameter('organization_art', $organization);
        }

        return $qb->getQuery()->getResult();
    }
    
}

?>
