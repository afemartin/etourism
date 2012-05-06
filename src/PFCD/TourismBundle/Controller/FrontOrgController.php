<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Organization controller.
 */
class FrontOrgController extends Controller
{
    /**
     * Show the list of organizations
     */
    public function homeAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $orgs = $em->createQueryBuilder()
                   ->select('o')
                   ->from('PFCDTourismBundle:Organization',  'o')
                   ->addOrderBy('o.created', 'DESC')
                   ->getQuery()
                   ->getResult();

        return $this->render('PFCDTourismBundle:Front/Org:home.html.twig', array(
            'orgs'      => $orgs,
        ));
    }
    
    /**
     * Show a organization
     */
    public function showAction($name)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $org = $em->getRepository('PFCDTourismBundle:Organization')->findBy(array('username' => $name));

        if (!$org) {
            throw $this->createNotFoundException('Unable to find Organization requested.');
        }

        return $this->render('PFCDTourismBundle:Front/Org:show.html.twig', array(
            'org'      => $org[0],
        ));
    }
}

?>
