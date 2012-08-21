<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Reservation;
use PFCD\TourismBundle\Form\ReservationType;
use PFCD\TourismBundle\Entity\ReservationFilter;
use PFCD\TourismBundle\Form\ReservationFilterType;

use PFCD\TourismBundle\Entity\Payment;

/**
 * Reservation controller
 */
class ReservationController extends Controller
{
    
    /**************************************************************************
     ***** BACK ACTIONS *******************************************************
     **************************************************************************/
    
    /**
     * Lists all Reservation entities
     */
    public function backIndexAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the activies that belong to the logged organization
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $reservationFilter = new ReservationFilter();
        
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
                
                $reservationFilter->setActivity($activity);
            }
        }
        
        $form = $this->createForm(new ReservationFilterType(), $reservationFilter, $options);
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $id = $this->get('security.context')->getToken()->getUser()->getId();
                    
                    // verify that the reserved activity belong to the logged organization
                    if ($reservationFilter->getActivity()->getOrganization()->getId() != $id)
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

        $activity = $reservationFilter->getActivity();
        $dateStart = $reservationFilter->getDateStart();
        $dateEnd = $reservationFilter->getDateEnd();
        $sessionDateStart = $reservationFilter->getSessionDateStart();
        $sessionDateEnd = $reservationFilter->getSessionDateEnd();
        $status = $reservationFilter->getStatus();

        $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findAllFiltered($organization, $activity, $dateStart, $dateEnd, $sessionDateStart, $sessionDateEnd, $status);

        return $this->render('PFCDTourismBundle:Back/Reservation:index.html.twig', array(
            'reservations' => $reservations,
            'form'         => $form->createView()
        ));
    }

    /**
     * Displays a form to create a new Reservation entity and store it when the form is submitted and valid
     */
    public function backCreateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the activies that belong to the logged organization
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $reservation = new Reservation();
        $form = $this->createForm(new ReservationType(), $reservation, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                
                $sessionId = $form->get('session')->getData();
                
                $session = $em->getRepository('PFCDTourismBundle:Session')->find($sessionId);

                if (!$session) throw $this->createNotFoundException('Unable to find Session entity.');
                
                $reservation->setSession($session);
                
                // if the reservation is made by organization administrator it is automatically accepted
                $reservation->setStatus(Reservation::STATUS_ACCEPTED);
                
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $id = $this->get('security.context')->getToken()->getUser()->getId();
                    
                    // verify that the reserved activity belong to the logged organization
                    if ($reservation->getSession()->getActivity()->getOrganization()->getId() != $id)
                    {
                        throw new AccessDeniedException();
                    }
                }

                // we have to save payment in 2 steps since we can not relate them until inversed-side entity exist at the DDBB
                
                $em->persist($reservation);
                $em->flush();
                
                // we create the payment only if the activity has a price
                
                if ($reservation->getSession()->getActivity()->getPrice())
                {
                    $payment = new Payment();
                    $payment->setPrice($session->getActivity()->getPrice() * $reservation->getPersons());
                    $payment->setCurrency($session->getActivity()->getCurrency());
                    $payment->setReservation($reservation);

                    $em->persist($payment);
                    $em->flush(); 
                }

                return $this->redirect($this->generateUrl('back_reservation_read', array('id' => $reservation->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Reservation:create.html.twig', array(
            'reservation' => $reservation,
            'form'        => $form->createView()
        ));
    }

    /**
     * Finds and displays a Reservation entity
     */
    public function backReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $reservation = $em->getRepository('PFCDTourismBundle:Reservation')->find($id);

        if (!$reservation) throw $this->createNotFoundException('Unable to find Reservation entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();

            // verify that the reserved activity belong to the logged organization
            if ($reservation->getSession()->getActivity()->getOrganization()->getId() != $organization)
            {
                throw new AccessDeniedException();
            }
        }
        
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Reservation:read.html.twig', array(
            'reservation' => $reservation,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Reservation entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $reservation = $em->getRepository('PFCDTourismBundle:Reservation')->find($id);

        if (!$reservation) throw $this->createNotFoundException('Unable to find Reservation entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();

            // verify that the reserved activity belong to the logged organization
            if ($reservation->getSession()->getActivity()->getOrganization()->getId() != $organization)
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

        $editForm = $this->createForm(new ReservationType(), $reservation, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $sessionId = $editForm->get('session')->getData();
                
                if ($sessionId)
                {
                    $session = $em->getRepository('PFCDTourismBundle:Session')->find($sessionId);

                    if (!$session) throw $this->createNotFoundException('Unable to find Session entity.');

                    $reservation->setSession($session);
                }
                
                $nextStatus = $reservation->getStatus();
                
                $em->persist($reservation);
                $em->flush();
                
                if ($nextStatus == Reservation::STATUS_ACCEPTED && $reservation->getPayment() == null && $reservation->getSession()->getActivity()->getPrice())
                {
                    // we have to save payment in 2 steps since we can not relate them until inversed-side entity exist at the DDBB
                    
                    $payment = new Payment();
                    $payment->setPrice($reservation->getSession()->getActivity()->getPrice() * $reservation->getPersons());
                    $payment->setCurrency($reservation->getSession()->getActivity()->getCurrency());
                    $payment->setReservation($reservation);
                
                    $em->persist($payment);
                    $em->flush();
                    
                    $user = $reservation->getUser();
                    
                    if ($user && $user->getEmail())
                    {
                        // after accept the reservation and create the payment a email is sent automatically to the user to proceed with the payment
                        $template = $this->findLocalizedTemplate('PFCDTourismBundle:Mail:reservation.%s.txt.twig', $user->getLocale());
                    
                        $message = \Swift_Message::newInstance()
                                ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.reservationaccepted.subject', array(), 'messages', $user->getLocale()))
                                ->setFrom($this->container->getParameter('pfcd_tourism.emails.no_reply_email'))
                                ->setTo($user->getEmail())
                                ->setBody($this->renderView($template, array('user' => $user, 'reservation' => $reservation, 'payment' => $payment)), 'text/html');

                        $this->get('mailer')->send($message);

                        $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.reservationaccepted'));
                    }
                }

                return $this->redirect($this->generateUrl('back_reservation_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Reservation:update.html.twig', array(
            'reservation' => $reservation,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Reservation entity
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $reservation = $em->getRepository('PFCDTourismBundle:Reservation')->find($id);

            if (!$reservation) throw $this->createNotFoundException('Unable to find Reservation entity.');

            if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
            {
                $id = $this->get('security.context')->getToken()->getUser()->getId();

                // verify that the reserved activity belong to the logged organization
                if ($reservation->getSession()->getActivity()->getOrganization()->getId() != $id)
                {
                    throw new AccessDeniedException();
                }
            }
        
            $reservation->setStatus(Reservation::STATUS_REJECTED);
            $em->persist($reservation);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_reservation_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT ACTIONS ******************************************************
     **************************************************************************/
    
    /**
     * Lists all Reservation entities
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $user = $this->get('security.context')->getToken()->getUser()->getId();
        
        $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findByUser($user);

        return $this->render('PFCDTourismBundle:Front/Reservation:index.html.twig', array(
            'reservations' => $reservations
        ));
    }

    /**
     * Displays a form to create a new Reservation entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontCreateAction($activityId, $sessionId)
    {
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_CREATE;
        $options['validation_groups'] = 'Front';
        
        $reservation = new Reservation();
        $form = $this->createForm(new ReservationType(), $reservation, $options);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $session = $em->getRepository('PFCDTourismBundle:Session')->find($sessionId);

        if (!$session) throw $this->createNotFoundException('Unable to find Session entity.');
        
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user) throw $this->createNotFoundException('Unable to find User entity.');
        
        $capacity = $session->getActivity()->getCapacity();
        
        foreach ($session->getReservations() as $prev_reservation)
        {
            if ($prev_reservation->getStatus() == Reservation::STATUS_REQUESTED || $prev_reservation->getStatus() == Reservation::STATUS_ACCEPTED)
            {
                $capacity -= $prev_reservation->getPersons();
            }
        }
        
        $error = false;

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                if ($reservation->getPersons() <= $capacity)
                {
                    $reservation->setUser($user);
                    $reservation->setSession($session);
                    $em->persist($reservation);
                    $em->flush();
                
                    return $this->redirect($this->generateUrl('front_reservation_read', array('id' => $reservation->getId())));
                }
                else
                {
                    $error = true;
                }
            }
        }    

        return $this->render('PFCDTourismBundle:Front/Reservation:create.html.twig', array(
            'reservation' => $reservation,
            'session'     => $session,
            'capacity'    => $capacity,
            'user'        => $user,
            'error'       => $error,
            'form'        => $form->createView()
        ));
    }

    /**
     * Finds and displays a Reservation entity
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontReadAction($id)
    {
        $filter['id'] = $id;
        $filter['user'] = $this->get('security.context')->getToken()->getUser()->getId();
        
        $em = $this->getDoctrine()->getEntityManager();

        $reservation = $em->getRepository('PFCDTourismBundle:Reservation')->findOneBy($filter);

        if (!$reservation) throw $this->createNotFoundException('Unable to find Reservation entity.');

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Front/Reservation:read.html.twig', array(
            'reservation' => $reservation,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Reservation entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontUpdateAction($id)
    {
        $filter['id'] = $id;
        $filter['user'] = $this->get('security.context')->getToken()->getUser()->getId();
                
        $em = $this->getDoctrine()->getEntityManager();

        $reservation = $em->getRepository('PFCDTourismBundle:Reservation')->findOneBy($filter);

        if (!$reservation) throw $this->createNotFoundException('Unable to find Reservation entity.');
        
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_UPDATE;
        $options['validation_groups'] = 'Front';
        
        $editForm = $this->createForm(new ReservationType(), $reservation, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($reservation);
                $em->flush();
                
                return $this->redirect($this->generateUrl('front_reservation_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Front/Reservation:update.html.twig', array(
            'reservation' => $reservation,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Reservation entity
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontDeleteAction($id)
    {
        $filter['id'] = $id;
        $filter['user'] = $this->get('security.context')->getToken()->getUser()->getId();
        
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $reservation = $em->getRepository('PFCDTourismBundle:Reservation')->findOneBy($filter);

            if (!$reservation) throw $this->createNotFoundException('Unable to find Reservation entity.');

            $reservation->setStatus(Reservation::STATUS_CANCELED);
            $em->persist($reservation);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('front_reservation_index'));
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
