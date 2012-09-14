<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Resource;
use PFCD\TourismBundle\Form\ResourceType;
use PFCD\TourismBundle\Entity\Reservation;

/**
 * Resource controller
 */
class ResourceController extends Controller
{
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all Resource entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $resources = $em->getRepository('PFCDTourismBundle:Resource')->findAll();
        }
        else
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            $resources = $em->getRepository('PFCDTourismBundle:Resource')->findByOrganization($organization, array(Resource::STATUS_ENABLED));
        }

        return $this->render('PFCDTourismBundle:Back/Resource:index.html.twig', array(
            'resources' => $resources
        ));
    }

    /**
     * Displays a form to create a new Resource entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_ORGANIZATION")
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
                
        // parameter used to filter and show only the resources that belong to the logged organization
        $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        
        $resource = new Resource();
        $form = $this->createForm(new ResourceType(), $resource, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                
                $dateStartLock = $resource->getDateStartLock();
                $dateEndLock = $resource->getDateEndLock();
                
                // it is supposed that a locked period takes full days
                if ($dateStartLock) $dateStartLock->setTime(0, 0, 0);
                if ($dateEndLock) $dateEndLock->setTime(23, 59, 59);
                
                $em->persist($resource);
                $em->flush();

                return $this->redirect($this->generateUrl('back_resource_read', array('id' => $resource->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Resource:create.html.twig', array(
            'resource' => $resource,
            'form'     => $form->createView()
        ));
    }
    
    /**
     * Finds and displays a Resource entity
     */
    public function backReadAction($id)
    {
        $filter['id'] = $id;
                
        $em = $this->getDoctrine()->getEntityManager();

        $resource = $em->getRepository('PFCDTourismBundle:Resource')->findOneBy($filter);

        if (!$resource) throw $this->createNotFoundException('Unable to find Resource entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            
            // verify that the selected resource belong to the logged organization
            if ($resource->getCategory() && $resource->getCategory()->getOrganization()->getId() != $organization)
            {
                throw new AccessDeniedException();
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Resource:read.html.twig', array(
            'resource'    => $resource,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Resource entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = Resource::STATUS_ENABLED;
                
        $em = $this->getDoctrine()->getEntityManager();

        $resource = $em->getRepository('PFCDTourismBundle:Resource')->findOneBy($filter);

        if (!$resource) throw $this->createNotFoundException('Unable to find Resource entity.');
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        
        $editForm = $this->createForm(new ResourceType(), $resource, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            // retrieve the original values for the start and end lock dates
            $prevDateStartLock = $resource->getDateStartLock();
            $prevDateEndLock = $resource->getDateEndLock();
            
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $organization = $this->get('security.context')->getToken()->getUser()->getId();

                    // verify that the selected resource belong to the logged organization
                    if ($resource->getCategory() && $resource->getCategory()->getOrganization()->getId() != $organization)
                    {
                        throw new AccessDeniedException();
                    }
                }
                
                // check that the resource updated has no been assignated to any
                // reservation (accepted) that overlap with the new lock period
                // it does not get affected by the check conflicts flag
                
                $dateStartLock = $resource->getDateStartLock();
                $dateEndLock = $resource->getDateEndLock();
                
                // it is supposed that a locked period takes full days
                if ($dateStartLock) $dateStartLock->setTime(0, 0, 0);
                if ($dateEndLock) $dateEndLock->setTime(23, 59, 59);
                
                // it works fine filtering everything properly
                if (($dateStartLock || $dateEndLock) && ($prevDateStartLock != $dateStartLock || $prevDateEndLock != $dateEndLock))
                {
                    $dateNow = new \DateTime();
                    $dateNow->setTime(0, 0, 0);
                    
                    if ((($dateStartLock && $dateEndLock) && ($dateStartLock > $dateEndLock)) || ($dateEndLock && ($dateNow > $dateEndLock)))
                    {
                        $this->get('session')->setFlash('alert-error', 'alert.error.daterange');
                        return $this->redirect($this->generateUrl('back_resource_update', array('id' => $id)));
                    }
                    
                    if ($dateStartLock == null || $dateStartLock < $dateNow) $dateStartLock = $dateNow;
                    
                    // now we finally have a proper data range to use it inside the query
                    $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findConflictsWithResources($resource->getId(), $dateStartLock, $dateEndLock, array(Reservation::STATUS_ACCEPTED), 10);
                    
                    if ($reservations)
                    {
                        $error = $this->get('translator')->trans('alert.error.updatelockperiod') . ':';

                        $error .= '<ul>';
                        foreach ($reservations as $reservation) $error .= '<li>' .  $this->get('translator')->trans('Activity') . ': ' . $reservation->getSession()->getActivity()->getTitle() .' - '.  $this->get('translator')->trans('Session') . ' [ ' . $reservation->getSession()->getDate()->format('d/m/Y') . ' - ' . $reservation->getSession()->getTime()->format('H:i') . ' ] (' . $this->get('translator')->trans($reservation->getStatusText()) . ')</li>';
                        $error .= (count($reservations) == 10) ? '<li>...</li></ul>' : '</ul>';

                        $this->get('session')->setFlash('alert-error', $error);

                        return $this->redirect($this->generateUrl('back_resource_update', array('id' => $id)));
                    }
                }
                
                $em->persist($resource);
                $em->flush();

                return $this->redirect($this->generateUrl('back_resource_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Resource:update.html.twig', array(
            'resource'  => $resource,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Resource entity
     */
    public function backDeleteAction($id)
    {
        $filter['id'] = $id;
                
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $resource = $em->getRepository('PFCDTourismBundle:Resource')->findOneBy($filter);

            if (!$resource) throw $this->createNotFoundException('Unable to find Resource entity.');
            
            if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
            {
                $organization = $this->get('security.context')->getToken()->getUser()->getId();

                // verify that the selected resource belong to the logged organization
                if ($resource->getCategory() && $resource->getCategory()->getOrganization()->getId() != $organization)
                {
                    throw new AccessDeniedException();
                }
            }
            
            // check that does not exist any enabled (or locked) reservation that require this resource
            // since there can be a lot of existing old reservations we will only search the recent and future reservations
            // we can find too many reservations to display so we will only display the 10 first reservations found
            $reservations = $em->getRepository('PFCDTourismBundle:Reservation')->findRecentAndFuture($resource->getId(), array(Reservation::STATUS_ACCEPTED), 10);

            if ($reservations)
            {
                $error = $this->get('translator')->trans('alert.error.deleteresource') . ':';

                $error .= '<ul>';
                foreach ($reservations as $reservation) $error .= '<li>' .  $this->get('translator')->trans('Activity') . ': ' . $reservation->getSession()->getActivity()->getTitle() .' - '.  $this->get('translator')->trans('Session') . ' [ ' . $reservation->getSession()->getDate()->format('d/m/Y') . ' - ' . $reservation->getSession()->getTime()->format('H:i') . ' ] (' . $this->get('translator')->trans($reservation->getStatusText()) . ')</li>';
                $error .= (count($reservations) == 10) ? '<li>...</li></ul>' : '</ul>';

                $this->get('session')->setFlash('alert-error', $error);

                return $this->redirect($this->generateUrl('back_resource_read', array('id' => $id)));
            }

            $resource->setStatus(Resource::STATUS_DELETED);
            $em->persist($resource);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_resource_index'));
    }
    
    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }

}
