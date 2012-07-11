<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Form\ActivityType;
use PFCD\TourismBundle\Form\MediaType;

use PFCD\TourismBundle\Entity\Image;

/**
 * Activity controller
 */
class ActivityController extends Controller
{

    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all Activity entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $activities = $em->getRepository('PFCDTourismBundle:Activity')->findAll();
        }
        else
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            $activities = $em->getRepository('PFCDTourismBundle:Activity')->findByOrganization($organization);
        }

        return $this->render('PFCDTourismBundle:Back/Activity:index.html.twig', array(
            'activities' => $activities
        ));
    }

    /**
     * Displays a form to create a new Activity entity and store it when the form is submitted and valid
     */
    public function backCreateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
        
        $activity = new Activity();
        $form = $this->createForm(new ActivityType(), $activity, $options);
        
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
                    $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);
                    $activity->setOrganization($organization);
                }

                $em->persist($activity);
                $em->flush();

                return $this->redirect($this->generateUrl('back_activity_read', array('id' => $activity->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Activity:create.html.twig', array(
            'activity' => $activity,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a Activity entity
     */
    public function backReadAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Activity:read.html.twig', array(
            'activity'    => $activity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a Activity entity
     */
    public function backPreviewAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');

                $dateStart = $activity->getDateStart();
        $capacity = $activity->getCapacity();
        
        $daysweek[1] = $activity->getMonday();
        $daysweek[2] = $activity->getTuesday();
        $daysweek[3] = $activity->getWednesday();
        $daysweek[4] = $activity->getThursday();
        $daysweek[5] = $activity->getFriday();
        $daysweek[6] = $activity->getSaturday();
        $daysweek[7] = $activity->getSunday();
        
        $calendar = $em->getRepository('PFCDTourismBundle:Activity')->findAvailability($dateStart, $capacity, $daysweek);
        
        $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findByActivity($id);
        
        foreach ($reservations as $reservation)
        {
            $date = $reservation->getDate()->format('Y-m-d');
            
            if (isset($calendar['dates'][$date])) 
            {
                $calendar['dates'][$date]['capacity'] -= $reservation->getAdults() + $reservation->getChildrens();
            }
        }
        
        return $this->render('PFCDTourismBundle:Back/Activity:preview.html.twig', array(
            'activity' => $activity,
            'calendar' => $calendar
        ));
    }
    
    /**
     * Finds and displays a Activity calendar of sessions
     */
    public function backCalendarAction()
    {
        $id = $this->get('request')->query->get('id');
        $year = $this->get('request')->query->get('year');
        $month = $this->get('request')->query->get('month');
        
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');
        
        $prev = array();
        $prev['year'] = $month == 1 ? $year - 1 : $year;
        $prev['month'] = $month == 1 ? 12 : $month - 1;
        
        $next = array();
        $next['year'] = $month == 12 ? $year + 1 : $year;
        $next['month'] = $month == 12 ? 1 : $month + 1;

        $calendar = $em->getRepository('PFCDTourismBundle:Session')->findAvailability($id, $year, $month);
        
        $today = new \DateTime();
        $today->setTime(0,0,0);
        
        return $this->render('PFCDTourismBundle:Back/Activity:ajax_calendar.html.twig', array(
            'year'     => $year,
            'month'    => $month,
            'prev'     => $prev,
            'next'     => $next,
            'today'    => $today,
            'activity' => $activity,
            'calendar' => $calendar
        ));
    }
    
    /**
     * Edits an existent Activity entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');

        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        $options['organization'] = $activity->getOrganization()->getId();
        
        $editForm = $this->createForm(new ActivityType(), $activity, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $activity->setImage();
                
                $em->persist($activity);
                $em->flush();

                return $this->redirect($this->generateUrl('back_activity_read', array('id' => $id)));
            }
            else
            {
                $activity->setFile(null);
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Activity:update.html.twig', array(
            'activity'  => $activity,
            'edit_form' => $editForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Activity entity and store it when the form is submitted and valid
     */
    public function backMediaAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');

        $options['entity'] = Constants::ACTIVITY;
        
        $editForm = $this->createForm(new MediaType(), $activity, $options);
        
        // Create an array of the current Image objects in the database
        $originalGallery = array();
        foreach ($activity->getGallery() as $image)
        {
            $originalGallery[] = $image;
        }

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                // Filter $originalImages to contain images removed by the user
                foreach ($activity->getGallery() as $image)
                {
                    foreach ($originalGallery as $key => $toDel)
                    {
                        if ($toDel->getId() === $image->getId())
                        {
                            unset($originalGallery[$key]);
                        }
                    }
                }

                // Delete from the DDBB the images removed previously
                foreach ($originalGallery as $image)
                {
                    $em->remove($image);
                }
                
                $em->persist($activity);
                $gallery = $activity->getGallery();
                foreach ($gallery as $image)
                {
                    $image->setActivity($activity);
                    $em->persist($image);
                }

                $em->flush();

                return $this->redirect($this->generateUrl('back_activity_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Activity:media.html.twig', array(
            'activity'  => $activity,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Activity entity
     */
    public function backDeleteAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

            if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');

            $activity->setStatus(Activity::STATUS_DELETED);
            $em->persist($activity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_activity_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT AREA *********************************************************
     **************************************************************************/
   
    /**
     * Lists all Activity entities
     */
    public function frontIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findByStatus(array(Activity::STATUS_ENABLED, Activity::STATUS_LOCKED));

        return $this->render('PFCDTourismBundle:Front/Activity:index.html.twig', array(
            'activities' => $activities
        ));
    }

    /**
     * Finds and displays a Activity entity
     */
    public function frontReadAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = array(Activity::STATUS_ENABLED, Activity::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');
        
        return $this->render('PFCDTourismBundle:Front/Activity:read.html.twig', array(
            'activity' => $activity
        ));
    }
    
    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
    
}
