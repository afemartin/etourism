<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

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
        return $this->render('PFCDTourismBundle:Back/Home:index.html.twig');
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
            'error'         => $error,
        ));
    }
}

?>
