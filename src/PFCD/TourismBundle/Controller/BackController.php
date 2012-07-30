<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Resource;

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

        return $this->render('PFCDTourismBundle:Back/Home:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
}

?>
