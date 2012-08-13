<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Article;
use PFCD\TourismBundle\Entity\Organization;

class ArticleRepository extends EntityRepository
{

    public function findListFront($organization = null, $country = null)
    {
        $qb = $this->createQueryBuilder('a');
        
        $qb->select('a');
        $qb->innerJoin('a.organization', 'o');
                
        if ($organization !== null)
        {
            $qb->where('a.organization = :organization');
            $qb->setParameter('organization', $organization);
        }
        
        if ($country !== null)
        {
            $qb->andWhere('o.country = :country');
            $qb->setParameter('country', $country);
        }
        
        $qb->andWhere('a.status IN (:status_act)');
        $qb->setParameter('status_act', array(Article::STATUS_ENABLED, Article::STATUS_LOCKED));
        
        $qb->andWhere('o.status IN (:status_org)');
        $qb->setParameter('status_org', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));

        return $qb->getQuery()->getResult();
    }

}

?>
