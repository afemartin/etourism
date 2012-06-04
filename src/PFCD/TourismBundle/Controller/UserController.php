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
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all User entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $users = $em->getRepository('PFCDTourismBundle:User')->findAll();

        return $this->render('PFCDTourismBundle:Back/User:index.html.twig', array(
            'users' => $users
        ));
    }
    
    /**
     * Displays a form to create a new User entity and store it when the form is submitted and valid
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::ADMIN;
        $options['type'] = Constants::FORM_CREATE;
        
        $user = new User();
        $form = $this->createForm(new UserType(), $user, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                // No confimation email will be send to this users manually created
                // Validation should be different here
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('back_user_read', array('id' => $user->getId())));
            }
        }

        return $this->render('PFCDTourismBundle:Back/User:create.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }
    
    /**
     * Finds and displays a User entity
     */
    public function backReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');
        
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/User:read.html.twig', array(
            'user'         => $user,
            'delete_form'  => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existent User entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');

        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        
        $editForm   = $this->createForm(new UserType(), $user, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('back_user_read', array('id' => $id)));
            }
        }

        return $this->render('PFCDTourismBundle:Back/User:update.html.twig', array(
            'user'      => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a User entity
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

            if (!$user) throw $this->createNotFoundException('Unable to find User entity.');

            $user->setStatus(User::STATUS_DELETED);
            $em->persist($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_user_index'));
    }
 
        
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
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
}
