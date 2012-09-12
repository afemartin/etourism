<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Resource;
use PFCD\TourismBundle\Form\ResourceType;
use PFCD\TourismBundle\Entity\Session;

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
     */
    public function backCreateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            // parameter used to filter and show only the resources that belong to the logged organization
            $options['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $resource = new Resource();
        $form = $this->createForm(new ResourceType(), $resource, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                
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
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $resource = $em->getRepository('PFCDTourismBundle:Resource')->findOneBy($filter);

        if (!$resource) throw $this->createNotFoundException('Unable to find Resource entity.');
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        
        $editForm = $this->createForm(new ResourceType(), $resource, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                
                // TODO: Check for conflict in case the locked date range is modified
                
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
            
            // check that does not exist any enabled (or locked) session that require this resource
            // since there can be a lot of existing old sessions we will only search the recent and future sessions
            // we can find too many sessions to display so we will only display the 10 first sessions found
            $sessions = $em->getRepository('PFCDTourismBundle:Session')->findRecentAndFuture(null, $resource->getId(), array(Session::STATUS_ENABLED, Session::STATUS_LOCKED), 10);

            if ($sessions)
            {
                $error = $this->get('translator')->trans('alert.error.deleteresource') . ':';

                $error .= '<ul>';
                foreach ($sessions as $session) $error .= '<li>' .  $this->get('translator')->trans('Activity') . ': ' . $session->getActivity()->getTitle() .' - '.  $this->get('translator')->trans('Session') . ' [ ' . $session->getDate()->format('d/m/Y') . ' - ' . $session->getTime()->format('H:i') . ' ] (' . $this->get('translator')->trans($session->getStatusText()) . ')</li>';
                $error .= (count($sessions) == 10) ? '<li>...</li></ul>' : '</ul>';

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
