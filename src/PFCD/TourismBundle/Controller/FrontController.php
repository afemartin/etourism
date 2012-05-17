<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Entity\Enquiry;
use PFCD\TourismBundle\Form\EnquiryType;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('PFCDTourismBundle:Front/Home:index.html.twig');
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
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $message = \Swift_Message::newInstance()
                    ->setSubject('[CooperationTourism] Contact enquiry from ' . $enquiry->getName())
                    ->setFrom($this->container->getParameter('pfcd_tourism.emails.outgoing_email'))
                    ->setTo($this->container->getParameter('pfcd_tourism.emails.incoming_email'))
                    ->setBody($this->renderView('PFCDTourismBundle:Front/Home:contactEmail.txt.twig', array('enquiry' => $enquiry)));
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
}

?>
