<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Form\OrganizationType;

/**
 * Organization controller.
 *
 */
class OrganizationController extends Controller
{
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all Organization entities
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organizations = $em->getRepository('PFCDTourismBundle:Organization')->findAll();

        return $this->render('PFCDTourismBundle:Back/Organization:index.html.twig', array(
            'organizations' => $organizations
        ));
    }

    /**
     * Displays a form to create a new Organization entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::ADMIN;
        $options['type'] = Constants::FORM_CREATE;
        
        $organization  = new Organization();
        $form = $this->createForm(new OrganizationType(), $organization, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($organization);
                $em->flush();

                return $this->redirect($this->generateUrl('back_organization_read', array('id' => $organization->getId())));
            }
        }

        return $this->render('PFCDTourismBundle:Back/Organization:create.html.twig', array(
            'organization' => $organization,
            'form'         => $form->createView()
        ));
    }
    
    /**
     * Finds and displays a Organization entity
     */
    public function backReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Organization:read.html.twig', array(
            'organization' => $organization,
            'delete_form'  => $deleteForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Organization entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException();
        }
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        
        $editForm = $this->createForm(new OrganizationType(), $organization, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $organization->setLogo();

                $em->persist($organization);
                $em->flush();

                return $this->redirect($this->generateUrl('back_organization_read', array('id' => $id)));
            }
            else
            {
                $organization->setFile(null);
            }
        }
       
        return $this->render('PFCDTourismBundle:Back/Organization:update.html.twig', array(
            'organization' => $organization,
            'edit_form'    => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Organization entity
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

            if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

            if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
            {
                throw new AccessDeniedException();
            }

            $organization->setStatus(Organization::STATUS_DELETED);
            $em->persist($organization);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_organization_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT AREA *********************************************************
     **************************************************************************/
   
    /**
     * Lists all Organization entities
     */
    public function frontIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organizations = $em->getRepository('PFCDTourismBundle:Organization')->findByStatus(array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED));

        return $this->render('PFCDTourismBundle:Front/Organization:index.html.twig', array(
            'organizations' => $organizations
        ));
    }

    /**
     * Finds and displays a Organization entity
     */
    public function frontReadAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');
        
        unset($filter);
        $filter['organization'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findBy($filter);

        return $this->render('PFCDTourismBundle:Front/Organization:read.html.twig', array(
            'organization' => $organization,
            'activities'   => $activities,
        ));
    }

    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
    
}
