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
        
        $qb->andWhere('a.status IN (:status_art)');
        $qb->setParameter('status_art', array(Article::STATUS_ENABLED, Article::STATUS_LOCKED));
        
        $qb->andWhere('o.status IN (:status_org)');
        $qb->setParameter('status_org', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));

        return $qb->getQuery()->getResult();
    }
    
    public function findCountriesFront()
    {
        $qb = $this->createQueryBuilder('a');
        
        $qb->select('o.country');
        $qb->innerJoin('a.organization', 'o');
                
        $qb->where('a.status IN (:status_art)');
        $qb->setParameter('status_art', array(Article::STATUS_ENABLED, Article::STATUS_LOCKED));
        
        $qb->andWhere('o.status IN (:status_org)');
        $qb->setParameter('status_org', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));
        
        $qb->groupBy('o.country');

        return $qb->getQuery()->getResult();
    }

}

?>
