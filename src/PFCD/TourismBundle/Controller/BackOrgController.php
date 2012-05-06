<?php

namespace PFCD\TourismBundle\Controller;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Form\OrganizationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of BackController
 */
class BackOrgController extends Controller
{
    
    /**
     * Show the list of organizations
     */
    public function showAllAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $orgs = $em->createQueryBuilder()
                   ->select('o')
                   ->from('PFCDTourismBundle:Organization',  'o')
                   ->addOrderBy('o.created', 'DESC')
                   ->getQuery()
                   ->getResult();

        return $this->render('PFCDTourismBundle:Back/Org:showAll.html.twig', array(
            'orgs'      => $orgs,
        ));
    }
    
    /**
     * Add a organization
     */
    public function createAction()
    {
        $organization = new Organization();
        $form = $this->createForm(new OrganizationType(), $organization);
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $em = $this->getDoctrine()
                        ->getEntityManager();
                $em->persist($organization);
                $em->flush();

                $this->get('session')->setFlash('back-notice', 'Your organization was added to the system. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
//                return $this->redirect($this->generateUrl('PFCDTourismBundle_back_organizations'));
            }
        }

        return $this->render('PFCDTourismBundle:Back/Org:create.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * Edit a organization
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);
        $form = $this->createForm(new OrganizationType(), $organization);
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $em->persist($organization);
                $em->flush();

                $this->get('session')->setFlash('back-notice', 'Your organization was modified successfully into the system. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
//                return $this->redirect($this->generateUrl('PFCDTourismBundle_back_organizations'));
            }
        }

        return $this->render('PFCDTourismBundle:Back/Org:update.html.twig', array(
            'org'      => $organization,
            'form'     => $form->createView(),
        ));
    }
    
    /**
     * Delete a organization
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $org = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$org) {
            throw $this->createNotFoundException('Unable to find Organization requested.');
        }

        return $this->render('PFCDTourismBundle:Back/Org:delete.html.twig', array(
            'org'      => $org,
        ));
    }
}

?>
