<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\User;
use PFCD\TourismBundle\Form\UserType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    
    /**************************************************************************
     ***** ADMIN AREA *********************************************************
     **************************************************************************/
    
    /**
     * Lists all User entities.
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:User')->findAll();

        return $this->render('PFCDTourismBundle:Admin/User:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    /**
     * Finds and displays a User entity.
     */
    public function adminShowAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Admin/User:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new User entity.
     */
    public function adminNewAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_CREATE));

        return $this->render('PFCDTourismBundle:Admin/User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new User entity.
     */
    public function adminCreateAction()
    {
        $entity  = new User();
        $request = $this->getRequest();
        $form    = $this->createForm(new UserType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_CREATE));
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $entity->getId())));
        }

        return $this->render('PFCDTourismBundle:Admin/User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Displays a form to edit an existing User entity.
     */
    public function adminEditAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Admin/User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing User entity.
     */
    public function adminUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm   = $this->createForm(new UserType(), $entity, array('domain' => Constants::ADMIN, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $id)));
        }

        return $this->render('PFCDTourismBundle:Admin/User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     */
    public function adminDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $entity->setStatus(User::STATUS_DELETED);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_user_index'));
    }
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    
    
    /**************************************************************************
     ***** FRONT AREA *********************************************************
     **************************************************************************/
   
    /**
     * Finds and displays a User entity.
     */
    public function frontShowAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Front/User:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new User entity.
     */
    public function frontNewAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, array('domain' => Constants::FRONT, 'type' => Constants::FORM_CREATE));

        return $this->render('PFCDTourismBundle:Front/User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new User entity.
     */
    public function frontCreateAction()
    {
        $entity  = new User();
        $request = $this->getRequest();
        $form    = $this->createForm(new UserType(), $entity, array('domain' => Constants::FRONT, 'type' => Constants::FORM_CREATE));
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            $message = \Swift_Message::newInstance()
                        ->setSubject('[CooperationTourism] Activation of your account')
                        ->setFrom($this->container->getParameter('pfcd_tourism.emails.no_reply_email'))
                        ->setTo($entity->getEmail())
                        ->setBody($this->renderView('PFCDTourismBundle:Mail:activation.txt.twig', array('user' => $entity)));
            $this->get('mailer')->send($message);

            $this->get('session')->setFlash('alert-success', 'You have register your account successfully. Before to login you should check your email inbox to follow the instructions in order to activate the account. Thank you!');
            return $this->redirect($this->generateUrl('front_index'));
        }

        return $this->render('PFCDTourismBundle:Front/User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function frontEditAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity, array('domain' => Constants::FRONT, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Front/User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing User entity.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function frontUpdateAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm   = $this->createForm(new UserType(), $entity, array('domain' => Constants::FRONT, 'type' => Constants::FORM_UPDATE));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('alert-success', 'Your changes have been saved successfully');
            return $this->redirect($this->generateUrl('front_user_show', array('id'=>$id)));
        }

        return $this->render('PFCDTourismBundle:Front/User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function frontDeleteAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PFCDTourismBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $entity->setStatus(User::STATUS_DELETED);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('front_user_edit'));
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
