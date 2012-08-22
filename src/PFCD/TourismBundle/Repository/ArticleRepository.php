<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Article;
use PFCD\TourismBundle\Entity\Organization;

class ArticleRepository extends EntityRepository
{

    public function findListFront($locale = null, $organization = null, $country = null, $status_art = null, $status_org = null, $sort = null, $order = null, $limit = null)
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
        
        if ($locale !== null)
        {
            $qb->andWhere('a.languages LIKE :locale');
            $qb->setParameter('locale', '%'.$locale.'%');
        }
        
        $qb->andWhere('a.status IN (:status_art)');
        $qb->setParameter('status_art', $status_art ? $status_art : array(Article::STATUS_ENABLED));
        
        $qb->andWhere('o.status IN (:status_org)');
        $qb->setParameter('status_org', $status_org ? $status_org : array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));
        
        if ($sort) $qb->orderBy($sort, $order);
        
        if ($limit) $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
    
    public function findCountriesFront($locale = null)
    {
        $qb = $this->createQueryBuilder('a');
        
        $qb->select('o.country');
        $qb->innerJoin('a.organization', 'o');
                
        $qb->where('a.status IN (:status_art)');
        $qb->setParameter('status_art', array(Article::STATUS_ENABLED));
        
        $qb->andWhere('o.status IN (:status_org)');
        $qb->setParameter('status_org', array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));
        
        if ($locale !== null)
        {
            $qb->andWhere('a.languages LIKE :locale');
            $qb->setParameter('locale', '%'.$locale.'%');
        }
        
        $qb->groupBy('o.country');

        return $qb->getQuery()->getResult();
    }

}

?>
