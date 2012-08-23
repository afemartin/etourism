<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Payment;
use PFCD\TourismBundle\Form\PaymentType;
use PFCD\TourismBundle\Entity\PaymentFilter;
use PFCD\TourismBundle\Form\PaymentFilterType;

/**
 * Payment controller
 */
class PaymentController extends Controller
{
    
    /**************************************************************************
     ***** BACK ACTIONS *******************************************************
     **************************************************************************/
    
    /**
     * Lists all Payment entities
     */
    public function backIndexAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the activies that belong to the logged organization
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $paymentFilter = new PaymentFilter();
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'GET')
        {
            $activityId = $request->get('activityId', null);
            
            if ($activityId)
            {
                $filter['id'] = $activityId;

                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
                }

                $em = $this->getDoctrine()->getEntityManager();

                $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

                if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');
                
                $paymentFilter->setActivity($activity);
            }
        }
        
        $form = $this->createForm(new PaymentFilterType(), $paymentFilter, $options);
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $id = $this->get('security.context')->getToken()->getUser()->getId();
                    
                    // verify that the reserved activity belong to the logged organization
                    if ($paymentFilter->getActivity()->getOrganization()->getId() != $id)
                    {
                        throw new AccessDeniedException();
                    }
                }
            }
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
        }
        else
        {
            $organization = null;
        }

        $activity = $paymentFilter->getActivity();
        $dateStart = $paymentFilter->getDateStart();
        $dateEnd = $paymentFilter->getDateEnd();
        $sessionDateStart = $paymentFilter->getSessionDateStart();
        $sessionDateEnd = $paymentFilter->getSessionDateEnd();
        $status = $paymentFilter->getStatus();

        $payments = $em->getRepository('PFCDTourismBundle:Payment')->findAllFiltered($organization, $activity, $dateStart, $dateEnd, $sessionDateStart, $sessionDateEnd, $status);

        return $this->render('PFCDTourismBundle:Back/Payment:index.html.twig', array(
            'payments' => $payments,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a Payment entity
     */
    public function backReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $payment = $em->getRepository('PFCDTourismBundle:Payment')->find($id);

        if (!$payment) throw $this->createNotFoundException('Unable to find Payment entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();

            // verify that the reserved activity belong to the logged organization
            if ($payment->getReservation()->getSession()->getActivity()->getOrganization()->getId() != $organization)
            {
                throw new AccessDeniedException();
            }
        }
        
        return $this->render('PFCDTourismBundle:Back/Payment:read.html.twig', array(
            'payment' => $payment
        ));
    }
    
    /**
     * Edits an existent Payment entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $payment = $em->getRepository('PFCDTourismBundle:Payment')->find($id);

        if (!$payment) throw $this->createNotFoundException('Unable to find Payment entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();

            // verify that the reserved activity belong to the logged organization
            if ($payment->getReservation()->getSession()->getActivity()->getOrganization()->getId() != $organization)
            {
                throw new AccessDeniedException();
            }
        }

        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        $options['supported_currencies'] = $this->container->getParameter('currencies');

        $editForm = $this->createForm(new PaymentType(), $payment, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($payment);
                $em->flush();

                return $this->redirect($this->generateUrl('back_payment_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Payment:update.html.twig', array(
            'payment'   => $payment,
            'edit_form' => $editForm->createView(),
        ));
    }


    /**************************************************************************
     ***** FRONT ACTIONS ******************************************************
     **************************************************************************/
    
    /**
     * Finds and displays a Payment entity
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontReadAction($id)
    {
        $user = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $payment = $em->getRepository('PFCDTourismBundle:Payment')->find($id);

        if (!$payment || $payment->getReservation()->getUser()->getId() != $user) throw $this->createNotFoundException('Unable to find Payment entity.');

        return $this->render('PFCDTourismBundle:Front/Payment:read.html.twig', array(
            'payment' => $payment
        ));
    }
    
    /**
     * Edits an existent Payment entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontUpdateAction($id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getEntityManager();

        $payment = $em->getRepository('PFCDTourismBundle:Payment')->find($id);

        if (!$payment || $payment->getReservation()->getUser()->getId() != $user->getId()) throw $this->createNotFoundException('Unable to find Payment entity.');

        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_UPDATE;
        $options['validation_groups'] = 'Front';
        
        $editForm = $this->createForm(new PaymentType(), $payment, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $comment = $editForm->get('comment')->getData();
                    
                $em->persist($payment);
                $em->flush();
                
                $organization = $payment->getReservation()->getSession()->getActivity()->getOrganization();
                
                if ($organization && $organization->getEmail())
                {
                    // when update the payment an email is sent automatically to the organization to solve the questions of the user
                    $template = $this->findLocalizedTemplate('PFCDTourismBundle:Mail:payment.updated.%s.txt.twig', $organization->getLocale());

                    $message = \Swift_Message::newInstance()
                            ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.paymentupdated.subject', array(), 'messages', $organization->getLocale()))
                            ->setFrom($user->getEmail())
                            ->setTo($organization->getEmail())
                            ->setBody($this->renderView($template, array('organization' => $organization, 'user' => $user, 'payment' => $payment, 'comment' => $comment)), 'text/html');

                    $this->get('mailer')->send($message);
                    
                    $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.paymentupdated'));
                }
                
                return $this->redirect($this->generateUrl('front_payment_read', array('id' => $id)));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Payment:update.html.twig', array(
            'payment'   => $payment,
            'edit_form' => $editForm->createView(),
        ));
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
