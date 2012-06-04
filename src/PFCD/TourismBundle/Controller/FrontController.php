<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use PFCD\TourismBundle\Entity\User;
use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Enquiry;
use PFCD\TourismBundle\Form\EnquiryType;


class FrontController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findByStatus(Activity::STATUS_ENABLED);

        return $this->render('PFCDTourismBundle:Front/Home:index.html.twig', array(
            'activities' => $activities
        ));
    }

    /**
     * Show the login page of the webpage for users
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

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
            
            if ($locale)
            {
                return $this->redirect($this->generateUrl('front_index', array('_locale' => $locale)));
            }
            else
            {
                return $this->redirect($this->generateUrl('front_index'));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
    
    /**
     * Check that the activationKey is valid and enable the user account
     */
    public function activateUserAction($id, $key)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('PFCDTourismBundle:User')->findOneBy(array('id' => $id, 'status' => User::STATUS_PENDING));

        if (!$user || $user->getActivationKey() !== $key)
        {
            $this->get('session')->setFlash('alert-error', $this->get('translator')->trans('alert.error.activateuser'));
            
            return $this->redirect($this->generateUrl('front_index'));
        }
        else
        {
            $user->setStatus(User::STATUS_ENABLED);
            $em->persist($user);
            $em->flush();

            $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.activateuser'));
            
            return $this->redirect($this->generateUrl('front_login'));
        }
    }

    /**
     * Send an email to the user with a link to reset his/her password
     */
    public function rememberPasswordAction()
    {
        $user = new User();
        
        $form = $this->createFormBuilder($user, array('validation_groups' => array('Remember')))
                ->add('email', 'email')
                ->getForm();

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();

                $user = $em->getRepository('PFCDTourismBundle:User')->findOneByEmail($user->getEmail());

                if (!$user)
                {
                    $this->get('session')->setFlash('alert-error', $this->get('translator')->trans('alert.error.rememberpassword'));
                }
                else
                {
                    $template = $user->getLocale() == 'es' ? 'remember.es.txt.twig' : 'remember.en.txt.twig';
                    
                    $message = \Swift_Message::newInstance()
                            ->setSubject('[CooperationTourism] ' . $this->get('translator')->trans('Remember password', array(), 'messages', $user->getLocale()))
                            ->setFrom($this->container->getParameter('pfcd_tourism.emails.no_reply_email'))
                            ->setTo($user->getEmail())
                            ->setBody($this->renderView('PFCDTourismBundle:Mail:' . $template, array('user' => $user)));
                    
                    $this->get('mailer')->send($message);

                    $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.rememberpassword'));
                    
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

        $user = $em->getRepository('PFCDTourismBundle:User')->find($id);

        $form = $this->createFormBuilder($user, array('validation_groups' => array('Reset')))
                ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'password.match.error', 'first_name' => 'New password', 'second_name' => 'Repeat password'))
                ->getForm();

        if (!$user || $user->getResetKey() !== $key)
        {
            $this->get('session')->setFlash('alert-error', $this->get('translator')->trans('alert.error.resetpassword'));
            
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
                    $em->persist($user);
                    $em->flush();

                    $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.resetpassword'));
                    
                    return $this->redirect($this->generateUrl('front_index'));
                }
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:resetPassword.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Display a contact form and send a enquiry to the webmaster when the form is submitted and valid
     */
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

                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.contactenquiry'));

                return $this->redirect($this->generateUrl('front_contact'));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Home:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Display About page
     */
    public function aboutAction()
    {
        return $this->render('PFCDTourismBundle:Front/Home:about.html.twig');
    }
    
    /**
     * Called from the template sidebar.html.yml to retrieve the list of most recent activities and news
     */
    public function sidebarAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findByStatus(Activity::STATUS_ENABLED);

        return $this->render('PFCDTourismBundle:Front/Home:sidebar.html.twig', array(
            'activities' => $activities
        ));
    }

}

?>
