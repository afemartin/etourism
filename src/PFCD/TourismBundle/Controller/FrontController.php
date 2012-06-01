<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use PFCD\TourismBundle\Entity\User;

use PFCD\TourismBundle\Entity\Enquiry;
use PFCD\TourismBundle\Form\EnquiryType;

use PFCD\TourismBundle\Entity\Activity;

class FrontController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('PFCDTourismBundle:Activity')->findBy(array('status' => Activity::STATUS_ENABLED));

        return $this->render('PFCDTourismBundle:Front/Home:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Show the login page of the webpage for users
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            
            $locale = $this->container->get('security.context')->getToken()->getUser()->getLocale();
            
            if ($locale) {
                return $this->redirect($this->generateUrl('front_index', array('_locale' => $locale)));
            } else {
                return $this->redirect($this->generateUrl('front_index'));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:login.html.twig', array(
                    'error' => $error,
                ));
    }

    /**
     * Send an email to the user with a link to reset his/her password
     */
    public function rememberPasswordAction()
    {
        $entity = new User();
        $form = $this->createFormBuilder($entity, array('validation_groups' => array('Remember')))
                ->add('email', 'email')
                ->getForm();

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();

                $user = $em->getRepository('PFCDTourismBundle:User')->findOneBy(array('email' => $entity->getEmail()));

                // Check if exist any user with that email and send him a link with the sha1(salt) as identification_key
                if (!$user)
                {
                    $this->get('session')->setFlash('alert-error', 'There is no user with that email in our database. Sorry');
                }
                else
                {
                    $message = \Swift_Message::newInstance()
                            ->setSubject('[CooperationTourism] Remember password')
                            ->setFrom($this->container->getParameter('pfcd_tourism.emails.no_reply_email'))
                            ->setTo($entity->getEmail())
                            ->setBody($this->renderView('PFCDTourismBundle:Mail:remember.txt.twig', array('user' => $users)));
                    $this->get('mailer')->send($message);

                    $this->get('session')->setFlash('alert-success', 'We send you an email with a reset link for your password. Please check your email inbox!');
                    return $this->redirect($this->generateUrl('front_index'));
                }
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:rememberPassword.html.twig', array(
                    'form' => $form->createView()
                ));
    }

    /**
     * Check that the resetKey is valid and show the user a form to create a new password
     */
    public function resetPasswordAction($id, $key)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->findOneBy(array('id' => $id));

        $form = $this->createFormBuilder($entity, array('validation_groups' => array('Reset')))
                ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'The password fields must match', 'first_name' => 'New Password', 'second_name' => 'Repeatpassword'))
                ->getForm();

        if (!$entity || $entity->getResetKey() !== $key)
        {
            $this->get('session')->setFlash('alert-error', 'The link received to reset the password it is not valid or you already reset your password before. Please contact us if you continue having problems to reset your password');
            return $this->redirect($this->generateUrl('front_index'));
        }
        else
        {
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST')
            {
                $form->bindRequest($request);

                if ($form->isValid())
                {
                    $em->persist($entity);
                    $em->flush();

                    $this->get('session')->setFlash('alert-success', 'Your password has been reset successfully. Use your new password to login into the system');
                    return $this->redirect($this->generateUrl('front_index'));
                }
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:resetPassword.html.twig', array(
                    'form' => $form->createView()
                ));
    }

    /**
     * Check that the activationKey is valid and enable the user account
     */
    public function activateUserAction($id, $key)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PFCDTourismBundle:User')->findOneBy(array('id' => $id));

        if (!$entity || $entity->getActivationKey() !== $key)
        {
            $this->get('session')->setFlash('alert-error', 'The link received to activate your account it is not valid or you already used before. Please contact us if you can not activate your account with the link provided');
            return $this->redirect($this->generateUrl('front_index'));
        }
        else
        {
            $entity->setStatus(User::STATUS_ENABLED);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('alert-success', 'Your account has been activated successfully. Use your email and password to login into the system');
            return $this->redirect($this->generateUrl('front_login'));
        }
    }

    public function aboutAction()
    {
        return $this->render('PFCDTourismBundle:Front/Home:about.html.twig');
    }

    public function contactAction()
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $message = \Swift_Message::newInstance()
                        ->setSubject('[CooperationTourism] Contact enquiry from ' . $enquiry->getName())
                        ->setFrom($this->container->getParameter('pfcd_tourism.emails.outgoing_email'))
                        ->setTo($this->container->getParameter('pfcd_tourism.emails.incoming_email'))
                        ->setBody($this->renderView('PFCDTourismBundle:Mail:contact.txt.twig', array('enquiry' => $enquiry)));
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('alert-success', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('front_contact'));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:contact.html.twig', array(
                    'form' => $form->createView()
                ));
    }
    
    public function sidebarAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findBy(array('status' => Activity::STATUS_ENABLED));

        return $this->render('PFCDTourismBundle:Front/Home:sidebar.html.twig', array(
            'activities' => $activities
        ));
    }

}

?>
