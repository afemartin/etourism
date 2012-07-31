<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Form\SessionType;
use PFCD\TourismBundle\Entity\SessionGenerator;
use PFCD\TourismBundle\Form\SessionGeneratorType;
use PFCD\TourismBundle\Entity\SessionFilter;
use PFCD\TourismBundle\Form\SessionFilterType;

/**
 * Session controller
 */
class SessionController extends Controller
{
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all Session entities
     */
    public function backIndexAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the activies that belong to the logged organization
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $sessionFilter = new SessionFilter();
        
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
                
                $sessionFilter->setActivity($activity);
            }
        }
        
        $form = $this->createForm(new SessionFilterType(), $sessionFilter, $options);
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $id = $this->get('security.context')->getToken()->getUser()->getId();
                    
                    // verify that the reserved activity belong to the logged organization
                    if ($sessionFilter->getActivity()->getOrganization()->getId() != $id)
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

        $activity = $sessionFilter->getActivity();
        $dateStart = $sessionFilter->getDateStart();
        $dateEnd = $sessionFilter->getDateEnd();
        $time = $sessionFilter->getStartTime();
        $daysWeek = $sessionFilter->getDaysWeek();
        $status = $sessionFilter->getStatus();

        $sessions = $em->getRepository('PFCDTourismBundle:Session')->findAllFiltered($organization, $activity, $dateStart, $dateEnd, $time, $daysWeek, $status);

        return $this->render('PFCDTourismBundle:Back/Session:index.html.twig', array(
            'sessions' => $sessions,
            'form'     => $form->createView()
        ));
    }

    /**
     * Displays a form to create a new Session entity and store it when the form is submitted and valid
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
        
        $session = new Session();
        $form = $this->createForm(new SessionType(), $session, $options);
        
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
                    if ($session->getActivity()->getOrganization()->getId() != $id)
                    {
                        throw new AccessDeniedException();
                    }
                }

                $em->persist($session);
                $em->flush();

                return $this->redirect($this->generateUrl('back_session_read', array('id' => $session->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Session:create.html.twig', array(
            'session' => $session,
            'form'    => $form->createView()
        ));
    }

    /**
     * Displays a form to create a group of Session entities and store them when the form is submitted and valid
     */
    public function backGenerateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the activies that belong to the logged organization
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $sessionGenerator = new SessionGenerator();
        $form = $this->createForm(new SessionGeneratorType(), $sessionGenerator, $options);
        
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
                    if ($sessionGenerator->getActivity()->getOrganization()->getId() != $id)
                    {
                        throw new AccessDeniedException();
                    }
                }
                
                // generate the array of sessions based on the given parameters
                $sessions = array();
                
                foreach ($sessionGenerator->getDatesRange() as $date)
                {
                    if ($sessionGenerator->getStartTimes())
                    {
                        foreach ($sessionGenerator->getStartTimes() as $time)
                        {
                            $session = new Session();
                            $session->setActivity($sessionGenerator->getActivity());
                            $session->setDate($date);
                            $session->setTime($time);
                            $session->setStatus($sessionGenerator->getStatus());
                            $sessions[] = $session;
                        }
                    }
                    else
                    {
                        $session = new Session();
                        $session->setActivity($sessionGenerator->getActivity());
                        $session->setDate($date);
                        $session->setStatus($sessionGenerator->getStatus());
                        $sessions[] = $session;
                    }
                }
                
                foreach ($sessions as $session)
                {
                    $em->persist($session);
                }
                
                $em->flush();
                
                return $this->redirect($this->generateUrl('back_session_index'));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Session:generate.html.twig', array(
            'session_generator' => $sessionGenerator,
            'form'              => $form->createView()
        ));
    }

    /**
     * Finds and displays a Session entity
     */
    public function backReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $session = $em->getRepository('PFCDTourismBundle:Session')->find($id);

        if (!$session) throw $this->createNotFoundException('Unable to find Session entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();

            // verify that the reserved activity belong to the logged organization
            if ($session->getActivity()->getOrganization()->getId() != $organization)
            {
                throw new AccessDeniedException();
            }
        }
        
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Session:read.html.twig', array(
            'session'     => $session,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Session entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $session = $em->getRepository('PFCDTourismBundle:Session')->find($id);

        if (!$session) throw $this->createNotFoundException('Unable to find Session entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();

            // verify that the reserved activity belong to the logged organization
            if ($session->getActivity()->getOrganization()->getId() != $organization)
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

        $editForm = $this->createForm(new SessionType(), $session, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($session);
                $em->flush();

                return $this->redirect($this->generateUrl('back_session_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Session:update.html.twig', array(
            'session'   => $session,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Session entity
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $session = $em->getRepository('PFCDTourismBundle:Session')->find($id);

            if (!$session) throw $this->createNotFoundException('Unable to find Session entity.');

            if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
            {
                $id = $this->get('security.context')->getToken()->getUser()->getId();

                // verify that the reserved activity belong to the logged organization
                if ($session->getActivity()->getOrganization()->getId() != $id)
                {
                    throw new AccessDeniedException();
                }
            }
        
            $session->setStatus(Session::STATUS_DELETED);
            $em->persist($session);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_session_index'));
    }
    

    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
}
