<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

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
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::ADMIN;
        $options['type'] = Constants::FORM_CREATE;
        $options['supported_languages'] = $this->container->getParameter('locales');
        
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
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');

        $options['domain'] = Constants::ADMIN;
        $options['type'] = Constants::FORM_UPDATE;
        $options['supported_languages'] = $this->container->getParameter('locales');
        
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
     * Displays a form to create a new User entity and store it when the form is submitted and valid
     */
    public function frontCreateAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_CREATE;
        
        $user = new User();
        $form = $this->createForm(new UserType(), $user, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em->persist($user);
                $em->flush();

                $template = $this->findLocalizedTemplate('PFCDTourismBundle:Mail:activation.%s.txt.twig', $user->getLocale());

                $message = \Swift_Message::newInstance()
                        ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.accountactivation.subject', array(), 'messages', $user->getLocale()))
                        ->setFrom($this->container->getParameter('pfcd_tourism.emails.no_reply_email'))
                        ->setTo($user->getEmail())
                        ->setBody($this->renderView($template, array('user' => $user)), 'text/html');
                
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.createuser'));
                
                return $this->redirect($this->generateUrl('front_index'));
            }
        }
        
        // load the template with the legal stuff in the proper language
        $legal = $this->renderView($this->findLocalizedTemplate('PFCDTourismBundle:Front/User:legal.%s.txt.twig', $this->get('session')->getLocale()));

        return $this->render('PFCDTourismBundle:Front/User:create.html.twig', array(
            'user'  => $user,
            'legal' => $legal,
            'form'  => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Front/User:read.html.twig', array(
            'user'        => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existent User entity and store it when the form is submitted and valid
     *
     * @Secure(roles="ROLE_USER")
     */
    public function frontUpdateAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');
        
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_UPDATE;
        $options['supported_languages'] = $this->container->getParameter('locales');
        
        $editForm = $this->createForm(new UserType(), $user, $options);
        
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($user);
                $em->flush();

                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.updateuser'));
                
                return $this->redirect($this->generateUrl('front_user_read', array('id'=>$id)));
            }
        }

        return $this->render('PFCDTourismBundle:Front/User:update.html.twig', array(
            'user'      => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Display a form to change the current password
     *
     * @Secure(roles="ROLE_USER")
     */
    public function frontSecurityAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');

        $form = $this->createFormBuilder($user, array('validation_groups' => array('Change')))
                ->add('old_password', 'password', array('property_path' => false, 'label' => 'Current password'))
                ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'password.match.error', 'first_name' => 'New password', 'second_name' => 'Repeat password'))
                ->getForm();

        $request = $this->getRequest();
        
        $error = false;
                
        if ($request->getMethod() == 'POST')
        {
            $current_password = $user->getPassword();

            $form->bindRequest($request);

            if ($form->isValid())
            {
                $encoder = new MessageDigestPasswordEncoder('sha1', false, 1);
                $old_password = $encoder->encodePassword($form->get('old_password')->getData(), $user->getSalt());
                
                if ($current_password == $old_password)
                {
                    $em->persist($user);
                    $em->flush();
                    
                    $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.changepassword'));
                    
                    return $this->redirect($this->generateUrl('front_user_read', array('id'=>$id)));
                }
                else
                {
                    $error = true;
                }    
            }
        }

        return $this->render('PFCDTourismBundle:Front/User:security.html.twig', array(
            'user'  => $user,
            'error' => $error,
            'form'  => $form->createView()
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

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

            if (!$user) throw $this->createNotFoundException('Unable to find User entity.');

            $user->setStatus(User::STATUS_DELETED);
            $em->persist($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('front_user_read'));
    }

    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
      
    /**
     * Given the route of a template and the wanted locale, it find if the template exists
     * if not it return the fallback template ('en' english language) 
     * 
     * @param string $template route of the template with a "%s" representing the locale
     * @param string $locale the locale 2-digits code
     * @return string route of the found template
     */
    private function findLocalizedTemplate($template, $locale)
    {
        $template_localized = sprintf($template, $locale);
        
        if (!$this->get('templating')->exists($template_localized))
        {
            $template_localized = sprintf($template, 'en');
        }
        
        return $template_localized;
    }
}
