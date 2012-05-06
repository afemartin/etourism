<?php

namespace PFCD\TourismBundle\Controller;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Form\OrganizationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of BackController
 */
class BackMainController extends Controller
{
    /**
     * Show the home page of the back-end administrator page
     */
    public function homeAction()
    {
        return $this->render('PFCDTourismBundle:Back/Main:home.html.twig');
    }
}

?>
