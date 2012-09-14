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
use PFCD\TourismBundle\Entity\Session;

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
                    if ($reservationFilter->getActivity() && $reservationFilter->getActivity()->getOrganization()->getId() != $id)
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
     * 
     * @Secure(roles="ROLE_ORGANIZATION")
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::BACK;
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

                if (!$session)
                {
                    return $this->render('PFCDTourismBundle:Back/Reservation:create.html.twig', array(
                        'reservation' => $reservation,
                        'form'        => $form->createView(),
                        'error'       => 'alert.error.reservation.invalidsession'
                    ));
                }
                
                $reservation->setSession($session);
                
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $id = $this->get('security.context')->getToken()->getUser()->getId();
                    
                    // verify that the reserved activity belong to the logged organization
                    if ($reservation->getSession()->getActivity()->getOrganization()->getId() != $id)
                    {
                        throw new AccessDeniedException();
                    }
                }
                
                $em->persist($reservation);
                $em->flush();
                
                return $this->redirect($this->generateUrl('back_reservation_accept', array('id' => $reservation->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Reservation:create.html.twig', array(
            'reservation' => $reservation,
            'form'        => $form->createView(),
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
        
        $rejectFormView = ($reservation->getStatus() == Reservation::STATUS_REQUESTED) ? $this->createChangeStatusForm($id, Reservation::STATUS_REJECTED)->createView() : null;        
        $cancelFormView = ($reservation->getStatus() == Reservation::STATUS_ACCEPTED) ? $this->createChangeStatusForm($id, Reservation::STATUS_CANCELED)->createView() : null;

        return $this->render('PFCDTourismBundle:Back/Reservation:read.html.twig', array(
            'reservation' => $reservation,
            'reject_form' => $rejectFormView,
            'cancel_form' => $cancelFormView,
        ));
    }
    
    /**
     * Confirm a requested reservation and choose the selected resources
     */
    public function backAcceptAction($id)
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
        
        // forbid accept a reservation that was previously accepted, rejected or canceled
        if ($reservation->getStatus() != Reservation::STATUS_REQUESTED) throw new AccessDeniedException();
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        $options['organization'] = $reservation->getSession()->getActivity()->getOrganization()->getId();
        $options['activity'] = $reservation->getSession()->getActivity()->getId();
        $options['session_start'] = $reservation->getSession()->getStartDatetime();
        $options['session_end'] = $reservation->getSession()->getEndDatetime();
        
        $editForm = $this->createForm(new ReservationType(), $reservation, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                // check that the reservation created by the organization have the correct amount of resources
                
                $categories = array();
                
                foreach ($reservation->getSession()->getActivity()->getCategories() as $category)
                {
                    $categories[$category->getId()] = false;
                }
                
                foreach ($reservation->getResources() as $resource)
                {
                    $categories[$resource->getCategory()->getId()] = true;
                }
                
                foreach ($categories as $isSelected)
                {
                    if (!$isSelected)
                    {
                        return $this->render('PFCDTourismBundle:Back/Reservation:accept.html.twig', array(
                            'reservation' => $reservation,
                            'edit_form'   => $editForm->createView(),
                            'error'       => 'alert.error.reservation.insufficientresources'
                        ));
                    }
                }
                
                // check that each selected resource (with check of conflicts) does not have several reservations (with the same assigned resource) that overlaps
                
                $dateStart = $reservation->getSession()->getStartDatetime();
                $dateEnd = $reservation->getSession()->getEndDatetime();
                
                $error = $this->get('translator')->trans('alert.error.reservation.resourcesconflict') . ': <ul>';
                
                $errorsFound = 0;
                
                foreach ($reservation->getResources() as $resource)
                {
                    // do not check conflicts for resources that did not activate that option
                    if ($resource->getConflict())
                    {
                        $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findConflictsWithResources($resource->getId(), $dateStart, $dateEnd, array(Reservation::STATUS_ACCEPTED), 5);
                        
                        $errorsFound += count($reservations);
                        
                        foreach ($reservations as $reservation) $error .= '<li>'.  $this->get('translator')->trans('Resource') . ' [ ' . $resource->getCategory()->getName() . ' - ' . $resource->getName() . ' ] - ' . $this->get('translator')->trans('Activity') . ': ' . $reservation->getSession()->getActivity()->getTitle() .' - '. $this->get('translator')->trans('Session') . ' [ ' . $reservation->getSession()->getDate()->format('d/m/Y') . ' - ' . $reservation->getSession()->getTime()->format('H:i') . ' ] - ' . $this->get('translator')->trans('Reservation') . ' [ ' . ($reservation->getUser() ? ($reservation->getUser()->getFullname() . ' ; ') : '') . $reservation->getPersons() . ' ' . $this->get('translator')->trans('Persons') . ' ] (' . $this->get('translator')->trans($reservation->getStatusText()) . ')</li>';
                        $error .= (count($reservations) == 5) ? '<li>...</li>' : '';
                    }
                }
                
                $error .= '</u>'; 
                
                if ($errorsFound > 0)
                {
                    return $this->render('PFCDTourismBundle:Back/Reservation:accept.html.twig', array(
                            'reservation' => $reservation,
                            'edit_form'   => $editForm->createView(),
                            'error'       => $error
                        ));
                }
                
                $reservation->setStatus(Reservation::STATUS_ACCEPTED);
                $em->persist($reservation);
                $em->flush();
                
                if ($reservation->getPayment() == null && $reservation->getSession()->getActivity()->getPrice())
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
                        // after accept the reservation and create the payment an email is sent automatically to the user to proceed with the payment
                        $template = $this->findLocalizedTemplate('PFCDTourismBundle:Mail:reservation.accepted.%s.txt.twig', $user->getLocale());
                    
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

        return $this->render('PFCDTourismBundle:Back/Reservation:accept.html.twig', array(
            'reservation' => $reservation,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Changes the status of the Reservation entity
     */
    public function backStatusAction($id, $status)
    {
        $form = $this->createChangeStatusForm($id, $status);
        
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
            
            switch ($status)
            {
                case Reservation::STATUS_REJECTED:
                    if ($reservation->getStatus() != Reservation::STATUS_REQUESTED)
                    {
                        throw new AccessDeniedException();
                    }
                    break;
                    
                case Reservation::STATUS_CANCELED:
                    if ($reservation->getStatus() != Reservation::STATUS_ACCEPTED)
                    {
                        throw new AccessDeniedException();
                    }
                    break;
                    
                default:
                    throw new AccessDeniedException();
                    break;
            }
        
            $reservation->setStatus($status);
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
    public function frontCreateAction($sessionId)
    {
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_CREATE;
        $options['validation_groups'] = 'Front';
        
        $reservation = new Reservation();
        $form = $this->createForm(new ReservationType(), $reservation, $options);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $filter['id'] = $sessionId;
        $filter['status'] = array(Session::STATUS_ENABLED);
        
        $session = $em->getRepository('PFCDTourismBundle:Session')->findOneBy($filter);

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
                    
                    $comment = $form->get('comment')->getData();
                    
                    $organization = $session->getActivity()->getOrganization();
                
                    if ($organization && $organization->getEmail())
                    {
                        // when create the reservation an email is sent automatically to the organization to accept or reject the reservation
                        $template = $this->findLocalizedTemplate('PFCDTourismBundle:Mail:reservation.created.%s.txt.twig', $organization->getLocale());
                    
                        $message = \Swift_Message::newInstance()
                                ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.reservationcreated.subject', array(), 'messages', $organization->getLocale()))
                                ->setFrom($user->getEmail())
                                ->setTo($organization->getEmail())
                                ->setBody($this->renderView($template, array('organization' => $organization, 'user' => $user, 'reservation' => $reservation, 'comment' => $comment)), 'text/html');

                        $this->get('mailer')->send($message);
                    }
                    
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
    
    private function createChangeStatusForm($id, $status)
    {
        return $this->createFormBuilder(array('id' => $id, 'status' => $status))->add('id', 'hidden')->add('status', 'hidden')->getForm();
    }
    
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
