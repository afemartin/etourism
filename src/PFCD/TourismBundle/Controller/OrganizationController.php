<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Form\OrganizationType;
use PFCD\TourismBundle\Form\OrganizationProfileType;
use PFCD\TourismBundle\Form\OrganizationRegistrationType;

/**
 * Organization controller.
 *
 */
class OrganizationController extends Controller
{
    /**************************************************************************
     ***** ADMIN AREA *********************************************************
     **************************************************************************/
    
    /**
     * Lists all Organization entities.
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:Organization')->findAll();

        return $this->render('PFCDTourismBundle:Admin/Organization:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Organization entity.
     */
    public function adminShowAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Admin/Organization:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Organization entity.
     */
    public function adminNewAction()
    {
        $entity = new Organization();
        $form   = $this->createForm(new OrganizationRegistrationType(), $entity);

        return $this->render('PFCDTourismBundle:Admin/Organization:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Organization entity.
     */
    public function adminCreateAction()
    {
        $entity  = new Organization();
        $request = $this->getRequest();
        $form    = $this->createForm(new OrganizationRegistrationType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_organization_show', array('id' => $entity->getId())));
            
        }

        return $this->render('PFCDTourismBundle:Admin/Organization:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Organization entity.
     */
    public function adminEditAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $editForm = $this->createForm(new OrganizationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Admin/Organization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Organization entity.
     */
    public function adminUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $editForm   = $this->createForm(new OrganizationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_organization_index'));
        }

        return $this->render('PFCDTourismBundle:Admin/Organization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Organization entity.
     *
     */
    public function adminDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Organization entity.');
            }

            $entity->setStatus(Organization::STATUS_DELETED);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_organization_index'));
    }
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Finds and displays a Organization entity.
     */
    public function backShowAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        return $this->render('PFCDTourismBundle:Back/Organization:show.html.twig', array(
            'entity'      => $entity
        ));
    }
    
    /**
     * Displays a form to edit an existing Organization entity.
     */
    public function backEditAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $editForm = $this->createForm(new OrganizationProfileType(), $entity);

        return $this->render('PFCDTourismBundle:Back/Organization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
     * Edits an existing Organization entity.
     */
    public function backUpdateAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $editForm   = $this->createForm(new OrganizationProfileType(), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('back_organization_show'));
        }

        return $this->render('PFCDTourismBundle:Admin/Organization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**************************************************************************
     ***** FRONT AREA *********************************************************
     **************************************************************************/
   
    /**
     * Lists all Organization entities.
     */
    public function frontIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:Organization')->findAll();

        return $this->render('PFCDTourismBundle:Front/Organization:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Organization entity.
     *
     */
    public function frontShowAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        return $this->render('PFCDTourismBundle:Front/Organization:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
