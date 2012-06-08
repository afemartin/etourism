<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Reservation;
use PFCD\TourismBundle\Form\ReservationType;

/**
 * Reservation controller
 */
class ReservationController extends Controller
{
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all Reservation entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findAll();
        }
        else
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findByOrganization($organization);
        }

        return $this->render('PFCDTourismBundle:Back/Reservation:index.html.twig', array(
            'reservations' => $reservations
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

                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $id = $this->get('security.context')->getToken()->getUser()->getId();
                    
                    // verify that the reserved activity belong to the logged organization
                    if ($reservation->getActivity()->getOrganization()->getId() != $id)
                    {
                        throw new AccessDeniedException();
                    }
                }

                $em->persist($reservation);
                $em->flush();

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
            if ($reservation->getActivity()->getOrganization()->getId() != $organization)
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
            if ($reservation->getActivity()->getOrganization()->getId() != $organization)
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
                $em->persist($reservation);
                $em->flush();

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
                if ($reservation->getActivity()->getOrganization()->getId() != $id)
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
     ***** FRONT AREA *********************************************************
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
    public function frontCreateAction($activityId)
    {
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_CREATE;
        $options['validation_groups'] = 'Front';
        
        $reservation = new Reservation();
        $form = $this->createForm(new ReservationType(), $reservation, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                
                $user = $this->get('security.context')->getToken()->getUser()->getId();
                
                $user = $em->getRepository('PFCDTourismBundle:User')->find($user);
                
                if (!$user) throw $this->createNotFoundException('Unable to find User entity.');
                
                $activity = $em->getRepository('PFCDTourismBundle:Activity')->find($activityId);
                
                if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');
                
                $reservation->setUser($user);
                $reservation->setActivity($activity);
                $em->persist($reservation);
                $em->flush();

                return $this->redirect($this->generateUrl('front_reservation_read', array('id' => $reservation->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Front/Reservation:create.html.twig', array(
            'reservation' => $reservation,
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
}
