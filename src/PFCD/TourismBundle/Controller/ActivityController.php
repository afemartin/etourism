<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Form\ActivityType;

/**
 * Activity controller.
 *
 */
class ActivityController extends Controller
{
    /**************************************************************************
     ***** ADMIN AREA *********************************************************
     **************************************************************************/
    
    /**
     * Lists all Activity entities.
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:Activity')->findBy(array('status' => Activity::STATUS_ENABLED));

        return $this->render('PFCDTourismBundle:Admin/Activity:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Activity entity.
     */
    public function adminShowAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Admin/Activity:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Activity entity.
     */
    public function adminNewAction()
    {
        $entity = new Activity();
        $form   = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_CREATE));

        return $this->render('PFCDTourismBundle:Admin/Activity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Activity entity.
     */
    public function adminCreateAction()
    {
        $entity  = new Activity();
        $request = $this->getRequest();
        $form    = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_CREATE));
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_activity_show', array('id' => $entity->getId())));
            
        }

        return $this->render('PFCDTourismBundle:Admin/Activity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Activity entity.
     */
    public function adminEditAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        $editForm = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Admin/Activity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Activity entity.
     */
    public function adminUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        $editForm   = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            
            $entity->setImage();
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_activity_show', array('id' => $id)));
        } else {
            $entity->setFile(null);
        }

        return $this->render('PFCDTourismBundle:Admin/Activity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Activity entity.
     */
    public function adminDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Activity entity.');
            }

            $entity->setStatus(Activity::STATUS_DELETED);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_activity_index'));
    }
    
    /**************************************************************************
     ***** BACK AREA *********************************************************
     **************************************************************************/
    
    /**
     * Lists all Activity entities.
     */
    public function backIndexAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:Activity')->findBy(array('status' => Activity::STATUS_ENABLED, 'organization' => $id));

        return $this->render('PFCDTourismBundle:Back/Activity:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Activity entity.
     */
    public function backShowAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Activity:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Activity entity.
     */
    public function backNewAction()
    {
        $entity = new Activity();
        $form   = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE));

        return $this->render('PFCDTourismBundle:Back/Activity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Activity entity.
     */
    public function backCreateAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $entity  = new Activity();
        $request = $this->getRequest();
        $form    = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE));
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            
            $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);
            $entity->setOrganization($organization);
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('back_activity_show', array('id' => $entity->getId())));
        }

        return $this->render('PFCDTourismBundle:Back/Activity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Activity entity.
     */
    public function backEditAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        $editForm = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::BACK, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Activity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Activity entity.
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        $editForm   = $this->createForm(new ActivityType(), $entity, array('domain' => Constants::BACK, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            
            $entity->setImage();
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('back_activity_show', array('id' => $id)));
        } else {
            $entity->setFile(null);
        }

        return $this->render('PFCDTourismBundle:Back/Activity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Activity entity.
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Activity entity.');
            }

            $entity->setStatus(Activity::STATUS_DELETED);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_activity_index'));
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

        $entities = $em->getRepository('PFCDTourismBundle:Activity')->findBy(array('status' => Activity::STATUS_ENABLED));

        return $this->render('PFCDTourismBundle:Front/Activity:index.html.twig', array(
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

        $entity = $em->getRepository('PFCDTourismBundle:Activity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Activity entity.');
        }

        return $this->render('PFCDTourismBundle:Front/Activity:show.html.twig', array(
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
