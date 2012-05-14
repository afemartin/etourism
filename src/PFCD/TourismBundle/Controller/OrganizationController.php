<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Form\OrganizationType;

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
        $form   = $this->createForm(new OrganizationType(), $entity);

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
        $form    = $this->createForm(new OrganizationType(), $entity);
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

            return $this->redirect($this->generateUrl('admin_organization_edit', array('id' => $id)));
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

            $em->remove($entity);
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

        $editForm = $this->createForm(new OrganizationType(), $entity);

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

        $editForm   = $this->createForm(new OrganizationType(), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('back_organization_edit'));
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
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:Organization')->findAll();

        return $this->render('PFCDTourismBundle:Organization:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Organization entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Organization:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Organization entity.
     *
     */
    public function newAction()
    {
        $entity = new Organization();
        $form   = $this->createForm(new OrganizationType(), $entity);

        return $this->render('PFCDTourismBundle:Organization:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Organization entity.
     *
     */
    public function createAction()
    {
        $entity  = new Organization();
        $request = $this->getRequest();
        $form    = $this->createForm(new OrganizationType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('organization_show', array('id' => $entity->getId())));
            
        }

        return $this->render('PFCDTourismBundle:Organization:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Organization entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $editForm = $this->createForm(new OrganizationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Organization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Organization entity.
     *
     */
    public function updateAction($id)
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

            return $this->redirect($this->generateUrl('organization_edit', array('id' => $id)));
        }

        return $this->render('PFCDTourismBundle:Organization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Organization entity.
     *
     */
    public function deleteAction($id)
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

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('organization'));
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
