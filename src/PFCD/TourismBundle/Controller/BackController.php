<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Resource;
use PFCD\TourismBundle\Entity\Settings;
use PFCD\TourismBundle\Form\SettingsType;
use PFCD\TourismBundle\Entity\Enquiry;
use PFCD\TourismBundle\Form\EnquiryType;

use \DateTime;
use \DateInterval;

/**
 * Description of BackController
 */
class BackController extends Controller
{
    /**
     * Show the home page of the back-end administrator page
     */
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the activies that belong to the logged organization
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
        }
        else
        {
            $organization = null;
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $dateStart = new DateTime();
        $dateStart->setTime(0, 0, 0);
        
        $dateEnd = new DateTime();
        $dateEnd->add(new DateInterval('P14D'));
        $dateEnd->setTime(23, 59, 59);
        
        $status = array(Session::STATUS_ENABLED, Session::STATUS_LOCKED);
                
        $sessions = $em->getRepository('PFCDTourismBundle:Session')->findAllFiltered($organization, null, $dateStart, $dateEnd, null, null, $status);
        
        $status = Resource::STATUS_ENABLED;
        
        $resources = $em->getRepository('PFCDTourismBundle:Resource')->findAllFiltered($organization, null, $dateStart, $dateEnd, $status);
        
        $dateStart = new DateTime();
        $dateStart->sub(new DateInterval('P14D'));
        $dateStart->setTime(0, 0, 0);
        
        $dateEnd = new DateTime();
        $dateEnd->setTime(23, 59, 59);
        
        $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findUpdatedReservations($organization, $dateStart, $dateEnd, null);
        
        $payments = $em->getRepository('PFCDTourismBundle:Payment')->findUpdatedPayments($organization, $dateStart, $dateEnd, null);

        return $this->render('PFCDTourismBundle:Back/Home:index.html.twig', array(
            'sessions'     => $sessions,
            'resources'    => $resources,
            'reservations' => $reservations,
            'payments'     => $payments,
        ));
    }
    
    /**
     * Show the main statistics information at the back-end administrator page
     */
    public function statisticAction()
    {
        return $this->render('PFCDTourismBundle:Back/Home:statistic.html.twig');
    }
    
    /**
     * Show the system settings information at the back-end administrator page
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function settingsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        // setting table will have only one row with id=1
        $settings = $em->getRepository('PFCDTourismBundle:Settings')->find(1);

        // if there is non existent settings, create it
        if (!$settings) $settings = new Settings();
        
        $options['language'] = $this->get('session')->getLocale();
        
        $editForm = $this->createForm(new SettingsType(), $settings, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($settings);
                $em->flush();
                
                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.updatesettings'));

                return $this->redirect($this->generateUrl('back_settings'));
            }
        }
       
        return $this->render('PFCDTourismBundle:Back/Home:settings.html.twig', array(
            'settings'  => $settings,
            'edit_form' => $editForm->createView(),
        ));
    }
    
    /**
     * Show the support information ans contact form at the back-end administrator page
     * 
     * @Secure(roles="ROLE_ORGANIZATION")
     */
    public function supportAction()
    {
        $enquiry = new Enquiry();
        
        $options['type'] = Constants::ENQUIRY_FULL;
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $options['type'] = Constants::ENQUIRY_MINI;
            
            $organization = $this->get('security.context')->getToken()->getUser();
            $enquiry->setName($organization->getName());
            $enquiry->setEmail($organization->getEmail());
        }
        
        $form = $this->createForm(new EnquiryType(), $enquiry, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $message = \Swift_Message::newInstance()
                        ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.supportenquiry.subject', array('%name%' => $enquiry->getName())))
                        ->setFrom($this->container->getParameter('pfcd_tourism.emails.outgoing_email'))
                        ->setTo($this->container->getParameter('pfcd_tourism.emails.incoming_email'))
                        ->setBody($this->renderView('PFCDTourismBundle:Mail:contact.txt.twig', array('enquiry' => $enquiry)));
                
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.supportenquiry'));

                return $this->redirect($this->generateUrl('front_contact'));
            }
        }

        return $this->render('PFCDTourismBundle:Back/Home:support.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * Show the login page of the administrator page for organizations and admin
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION')) {
            
            $locale = $this->container->get('security.context')->getToken()->getUser()->getLocale();
            
            if ($locale)
            {
                return $this->redirect($this->generateUrl('back_index', array('_locale' => $locale)));
            }
            else
            {
                return $this->redirect($this->generateUrl('back_index'));
            }
        }

        return $this->render('PFCDTourismBundle:Back/Home:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
}

?>
