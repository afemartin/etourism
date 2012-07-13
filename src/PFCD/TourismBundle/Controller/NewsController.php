<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\News;
use PFCD\TourismBundle\Form\NewsType;
use PFCD\TourismBundle\Form\MediaType;

use PFCD\TourismBundle\Entity\Image;

/**
 * News controller
 */
class NewsController extends Controller
{
    
    /**************************************************************************
     ***** BACK ACTIONS *******************************************************
     **************************************************************************/
    
    /**
     * Lists all News entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $news_list = $em->getRepository('PFCDTourismBundle:News')->findAll();
        }
        else
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            $news_list = $em->getRepository('PFCDTourismBundle:News')->findByOrganization($organization);
        }

        return $this->render('PFCDTourismBundle:Back/News:index.html.twig', array(
            'news_list' => $news_list
        ));
    }

    /**
     * Displays a form to create a new News entity and store it when the form is submitted and valid
     */
    public function backCreateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
        
        $news = new News();
        $form = $this->createForm(new NewsType(), $news, $options);
        
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
                    $news->setOrganization($organization);
                }

                $em->persist($news);
                $em->flush();

                return $this->redirect($this->generateUrl('back_news_read', array('id' => $news->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/News:create.html.twig', array(
            'news' => $news,
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a News entity
     */
    public function backReadAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $news = $em->getRepository('PFCDTourismBundle:News')->findOneBy($filter);

        if (!$news) throw $this->createNotFoundException('Unable to find News entity.');

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PFCDTourismBundle:Back/News:read.html.twig', array(
            'news'        => $news,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a News entity
     */
    public function backPreviewAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $news = $em->getRepository('PFCDTourismBundle:News')->findOneBy($filter);

        if (!$news) throw $this->createNotFoundException('Unable to find News entity.');
        
        $this->get('session')->setFlash('alert-info', $this->get('translator')->trans('alert.info.newspreview'));
        
        return $this->render('PFCDTourismBundle:Back/News:preview.html.twig', array(
            'news' => $news
        ));
    }
    
    /**
     * Edits an existent News entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $news = $em->getRepository('PFCDTourismBundle:News')->findOneBy($filter);

        if (!$news) throw $this->createNotFoundException('Unable to find News entity.');

        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        
        $editForm = $this->createForm(new NewsType(), $news, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $news->setImage();
                
                $em->persist($news);
                $em->flush();

                return $this->redirect($this->generateUrl('back_news_read', array('id' => $id)));
            }
            else
            {
                $news->setFile(null);
            }
        }    

        return $this->render('PFCDTourismBundle:Back/News:update.html.twig', array(
            'news'      => $news,
            'edit_form' => $editForm->createView(),
        ));
    }
    
    /**
     * Edits an existent News entity and store it when the form is submitted and valid
     */
    public function backMediaAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $news = $em->getRepository('PFCDTourismBundle:News')->findOneBy($filter);

        if (!$news) throw $this->createNotFoundException('Unable to find News entity.');

        $options['entity'] = Constants::NEWS;
        
        $editForm = $this->createForm(new MediaType(), $news, $options);
        
        // Create an array of the current Image objects in the database
        $originalGallery = array();
        foreach ($news->getGallery() as $image)
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
                foreach ($news->getGallery() as $image)
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
                
                $em->persist($news);
                $gallery = $news->getGallery();
                foreach ($gallery as $image)
                {
                    $image->setNews($news);
                    $em->persist($image);
                }

                $em->flush();

                return $this->redirect($this->generateUrl('back_news_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/News:media.html.twig', array(
            'news'  => $news,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a News entity
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
            $news = $em->getRepository('PFCDTourismBundle:News')->findOneBy($filter);

            if (!$news) throw $this->createNotFoundException('Unable to find News entity.');

            $news->setStatus(News::STATUS_DELETED);
            $em->persist($news);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_news_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT ACTIONS ******************************************************
     **************************************************************************/
   
    /**
     * Lists all News entities
     */
    public function frontIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $news_list = $em->getRepository('PFCDTourismBundle:News')->findByStatus(array(News::STATUS_ENABLED, News::STATUS_LOCKED));

        return $this->render('PFCDTourismBundle:Front/News:index.html.twig', array(
            'news_list' => $news_list
        ));
    }

    /**
     * Finds and displays a News entity
     */
    public function frontReadAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = array(News::STATUS_ENABLED, News::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $news = $em->getRepository('PFCDTourismBundle:News')->findOneBy($filter);

        if (!$news) throw $this->createNotFoundException('Unable to find News entity.');
        
        return $this->render('PFCDTourismBundle:Front/News:read.html.twig', array(
            'news' => $news
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
