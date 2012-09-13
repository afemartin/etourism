<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Category;
use PFCD\TourismBundle\Form\CategoryType;
use PFCD\TourismBundle\Entity\Resource;

/**
 * Category controller
 */
class CategoryController extends Controller
{
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Displays a form to create a new Category entity
     * Also displays a list with the current enabled categories
     */
    public function backCreateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        
        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category, $options);
        
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
                    $category->setOrganization($organization);
                }

                $em->persist($category);
                $em->flush();

                return $this->redirect($this->generateUrl('back_category_create'));
            }
        }    

        $em = $this->getDoctrine()->getEntityManager();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $categories = $em->getRepository('PFCDTourismBundle:Category')->findAll();
        }
        else
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
            $filter['status'] = Category::STATUS_ENABLED;
            $categories = $em->getRepository('PFCDTourismBundle:Category')->findBy($filter);
        }
        
        return $this->render('PFCDTourismBundle:Back/Category:create.html.twig', array(
            'categories' => $categories,
            'category'   => $category,
            'form'       => $form->createView()
        ));
    }
    
    /**
     * Finds and displays a Category entity
     */
    public function backReadAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $category = $em->getRepository('PFCDTourismBundle:Category')->findOneBy($filter);

        if (!$category) throw $this->createNotFoundException('Unable to find Category entity.');

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/Category:read.html.twig', array(
            'category'    => $category,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Deletes a Category entity
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
            $category = $em->getRepository('PFCDTourismBundle:Category')->findOneBy($filter);

            if (!$category) throw $this->createNotFoundException('Unable to find Resource entity.');
            
            // check that does not exist any enabled (or locked) resource
            unset($filter);
            $filter['category'] = $category->getId();
            $filter['status'] = Resource::STATUS_ENABLED;
            
            $resources = $em->getRepository('PFCDTourismBundle:Resource')->findBy($filter);
            
            if ($resources)
            {
                $error = $this->get('translator')->trans('alert.error.deletecategory') . ':';

                $error .= '<ul>';
                foreach ($resources as $resource) $error .= '<li>' . $resource->getName() . ' (' . $this->get('translator')->trans($resource->getStatusText()) . ')</li>';
                $error .= '</ul>';

                $this->get('session')->setFlash('alert-error', $error);

                return $this->redirect($this->generateUrl('back_category_read', array('id' => $id)));
            }
            
            $category->setStatus(Category::STATUS_DELETED);
            $em->persist($category);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('back_category_create'));
    }
    
    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }

}
