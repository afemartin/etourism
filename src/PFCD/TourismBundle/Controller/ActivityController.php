<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Form\ActivityType;
use PFCD\TourismBundle\Form\MediaType;
use PFCD\TourismBundle\Entity\Comment;
use PFCD\TourismBundle\Form\CommentType;
use PFCD\TourismBundle\Entity\Session;

use PFCD\TourismBundle\Entity\OrganizationFilter;
use PFCD\TourismBundle\Form\OrganizationFilterType;

use PFCD\TourismBundle\Entity\Image;

/**
 * Activity controller
 */
class ActivityController extends Controller
{

    /**************************************************************************
     ***** BACK ACTIONS *******************************************************
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
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
            $filter['status'] = array(Activity::STATUS_PENDING, Activity::STATUS_ENABLED, Activity::STATUS_LOCKED);
            $activities = $em->getRepository('PFCDTourismBundle:Activity')->findBy($filter);
        }

        return $this->render('PFCDTourismBundle:Back/Activity:index.html.twig', array(
            'activities' => $activities
        ));
    }

    /**
     * Displays a form to create a new Activity entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_ORGANIZATION")
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
        $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        $options['language'] = $this->get('session')->getLocale();
        $options['supported_languages'] = $this->container->getParameter('locales');
        $options['supported_currencies'] = $this->container->getParameter('currencies');
        
        $activity = new Activity();
        $form = $this->createForm(new ActivityType(), $activity, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();

                $id = $this->get('security.context')->getToken()->getUser()->getId();
                $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);
                $activity->setOrganization($organization);

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

        $enableFormView = ($activity->getStatus() == Activity::STATUS_PENDING || $activity->getStatus() == Activity::STATUS_LOCKED) ? $this->createChangeStatusForm($id, Activity::STATUS_ENABLED)->createView() : null;        
        $lockFormView = ($activity->getStatus() == Activity::STATUS_ENABLED) ? $this->createChangeStatusForm($id, Activity::STATUS_LOCKED)->createView() : null;
        $deleteFormView = ($activity->getStatus() != Activity::STATUS_DELETED) ? $this->createChangeStatusForm($id, Activity::STATUS_DELETED)->createView() : null;

        $translations = $em->getRepository('StofDoctrineExtensionsBundle:Translation')->findTranslations($activity);
        
        return $this->render('PFCDTourismBundle:Back/Activity:read.html.twig', array(
            'activity'     => $activity,
            'translations' => $translations,
            'enable_form'  => $enableFormView,
            'lock_form'    => $lockFormView,
            'delete_form'  => $deleteFormView,
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
        
        if ($activity->getStatus() == Activity::STATUS_DELETED) throw new AccessDeniedException();
                
        $this->get('session')->setFlash('alert-info', $this->get('translator')->trans('alert.info.activitypreview'));
        
        return $this->render('PFCDTourismBundle:Back/Activity:preview.html.twig', array(
            'activity' => $activity
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

        if ($activity->getStatus() == Activity::STATUS_DELETED) throw new AccessDeniedException();
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        $options['status'] = $activity->getStatus();
        $options['organization'] = $activity->getOrganization()->getId();
        $options['language'] = $this->get('session')->getLocale();
        $options['supported_languages'] = $this->container->getParameter('locales');
        $options['supported_currencies'] = $this->container->getParameter('currencies');
        
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
        
        if ($activity->getStatus() == Activity::STATUS_DELETED) throw new AccessDeniedException();

        $options['entity'] = Constants::ACTIVITY;
        $options['language'] = $this->get('session')->getLocale();
        
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
     * Changes the status of the Activity entity
     */
    public function backStatusAction($id, $status)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $form = $this->createChangeStatusForm($id, $status);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

            if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');

            switch ($status)
            {
                case Activity::STATUS_ENABLED:
                    if ($activity->getStatus() != Activity::STATUS_PENDING && $activity->getStatus() != Activity::STATUS_LOCKED)
                    {
                        throw new AccessDeniedException();
                    }
                    break;
                    
                case Activity::STATUS_LOCKED:
                    if ($activity->getStatus() != Activity::STATUS_ENABLED)
                    {
                        throw new AccessDeniedException();
                    }
                    break;
                    
                case Activity::STATUS_DELETED:
                    if ($activity->getStatus() == Activity::STATUS_DELETED)
                    {
                        throw new AccessDeniedException();
                    }
                    
                    // since there can be a lot of existing old sessions we will only search the recent and future sessions
                    // we can find too many sessions to display so we will only display the 10 first sessions found
                    $sessions = $em->getRepository('PFCDTourismBundle:Session')->findRecentAndFuture($activity->getId(), array(Session::STATUS_ENABLED, Session::STATUS_LOCKED), 10);
                    
                    if ($sessions)
                    {
                        $error = $this->get('translator')->trans('alert.error.deleteactivity') . ':';
                        
                        $error .= '<ul>';
                        foreach ($sessions as $session) $error .= '<li>' .  $this->get('translator')->trans('Session') . ' [ ' . $session->getDate()->format('d/m/Y') . ' - ' . $session->getTime()->format('H:i') . ' ] (' . $this->get('translator')->trans($session->getStatusText()) . ')</li>';
                        $error .= (count($sessions) == 10) ? '<li>...</li></ul>' : '</ul>';
                            
                        $this->get('session')->setFlash('alert-error', $error);
                        
                        return $this->redirect($this->generateUrl('back_activity_read', array('id' => $id)));
                    }
                    break;
                    
                default:
                    throw new AccessDeniedException();
                    break;
            }
            
            $activity->setStatus($status);
            $em->persist($activity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_activity_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT ACTIONS ******************************************************
     **************************************************************************/
   
    /**
     * Lists all Activity entities
     */
    public function frontIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $locale = $this->get('session')->getLocale();

        $countries = $em->getRepository('PFCDTourismBundle:Activity')->findCountriesFront($locale);
        
        // the country filter should be displayed only if there are 2 or more different countries
        $countryfilter = count($countries) > 1;
        
        $options = array();
        
        foreach ($countries as $country)
        {
            $options['countries'][$country['country']] = $country['country'];
        }
        
        $organizationFilter = new OrganizationFilter();
        
        $form = $this->createForm(new OrganizationFilterType(), $organizationFilter, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
        }
        
        $country = $organizationFilter->getCountry() ?: null;

        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findListFront($locale, null, $country, null, null, 'a.created', 'DESC');

        return $this->render('PFCDTourismBundle:Front/Activity:index.html.twig', array(
            'activities'    => $activities,
            'countryfilter' => $countryfilter,
            'form'          => $form->createView()
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
        
        unset($filter);
        $filter['activity'] = $id;
        $filter['status'] = array(Comment::STATUS_ENABLED);
        
        $comments = $em->getRepository('PFCDTourismBundle:Comment')->findBy($filter);
        
        $comment = new Comment();
        
        $form = $this->createForm(new CommentType(), $comment);
        
        return $this->render('PFCDTourismBundle:Front/Activity:read.html.twig', array(
            'activity'     => $activity,
            'comments'     => $comments,
            'comment_form' => $form->createView(),
        ));
    }
        
    
    /**************************************************************************
     ***** COMMON ACTIONS *****************************************************
     **************************************************************************/
    
    /**
     * Finds and displays a Activity calendar of sessions
     */
    public function calendarAction()
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
        
        return $this->render('PFCDTourismBundle:Ajax:activity_calendar.html.twig', array(
            'year'     => $year,
            'month'    => $month,
            'prev'     => $prev,
            'next'     => $next,
            'today'    => $today,
            'activity' => $activity,
            'calendar' => $calendar
        ));
    }
    
    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createChangeStatusForm($id, $status)
    {
        return $this->createFormBuilder(array('id' => $id, 'status' => $status))->add('id', 'hidden')->add('status', 'hidden')->getForm();
    }
    
}
