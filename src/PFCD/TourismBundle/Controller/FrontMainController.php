<?php

namespace PFCD\TourismBundle\Controller;

use PFCD\TourismBundle\Entity\Enquiry;
use PFCD\TourismBundle\Form\EnquiryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontMainController extends Controller
{
    public function homeAction()
    {
        return $this->render('PFCDTourismBundle:Front/Main:home.html.twig');
    }
    
    public function aboutAction()
    {
        return $this->render('PFCDTourismBundle:Front/Main:about.html.twig');
    }
    
    public function contactAction()
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

//                $message = \Swift_Message::newInstance()
//                    ->setSubject('Contact enquiry from CooperationTourism.com')
//                    ->setFrom('enquiry@cooperationtourism.com')
//                    ->setTo($this->container->getParameter('pfcd_tourism.emails.contact_email'))
//                    ->setBody($this->renderView('PFCDTourismBundle:Main:contactEmail.txt.twig', array('enquiry' => $enquiry)));
//                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('tourism-notice', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('PFCDTourismBundle_front_contact'));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Main:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }
}

?>
