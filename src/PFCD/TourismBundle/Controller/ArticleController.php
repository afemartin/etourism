<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Article;
use PFCD\TourismBundle\Form\ArticleType;
use PFCD\TourismBundle\Form\MediaType;

use PFCD\TourismBundle\Entity\OrganizationFilter;
use PFCD\TourismBundle\Form\OrganizationFilterType;

use PFCD\TourismBundle\Entity\Image;

/**
 * Article controller
 */
class ArticleController extends Controller
{
    
    /**************************************************************************
     ***** BACK ACTIONS *******************************************************
     **************************************************************************/
    
    /**
     * Lists all Article entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $articles = $em->getRepository('PFCDTourismBundle:Article')->findAll();
        }
        else
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            $articles = $em->getRepository('PFCDTourismBundle:Article')->findByOrganization($organization);
        }

        return $this->render('PFCDTourismBundle:Back/Article:index.html.twig', array(
            'articles' => $articles
        ));
    }

    /**
     * Displays a form to create a new Article entity and store it when the form is submitted and valid
     */
    public function backCreateAction()
    {
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_CREATE;
        $options['language'] = $this->get('session')->getLocale();
        
        $article = new Article();
        $form = $this->createForm(new ArticleType(), $article, $options);
        
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
                    $article->setOrganization($organization);
                }

                $em->persist($article);
                $em->flush();

                return $this->redirect($this->generateUrl('back_article_read', array('id' => $article->getId())));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Article:create.html.twig', array(
            'article' => $article,
            'form'    => $form->createView()
        ));
    }

    /**
     * Finds and displays a Article entity
     */
    public function backReadAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');

        $deleteForm = $this->createDeleteForm($id);

        $translations = $em->getRepository('StofDoctrineExtensionsBundle:Translation')->findTranslations($article);
        
        return $this->render('PFCDTourismBundle:Back/Article:read.html.twig', array(
            'article'      => $article,
            'translations' => $translations,
            'delete_form'  => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a Article entity
     */
    public function backPreviewAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');
        
        $this->get('session')->setFlash('alert-info', $this->get('translator')->trans('alert.info.articlepreview'));
        
        return $this->render('PFCDTourismBundle:Back/Article:preview.html.twig', array(
            'article' => $article
        ));
    }
    
    /**
     * Edits an existent Article entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');

        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        $options['language'] = $this->get('session')->getLocale();
        
        $editForm = $this->createForm(new ArticleType(), $article, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $article->setImage();
                
                $em->persist($article);
                $em->flush();

                return $this->redirect($this->generateUrl('back_article_read', array('id' => $id)));
            }
            else
            {
                $article->setFile(null);
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Article:update.html.twig', array(
            'article'   => $article,
            'edit_form' => $editForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Article entity and store it when the form is submitted and valid
     */
    public function backMediaAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');

        $options['entity'] = Constants::ARTICLE;
        $options['language'] = $this->get('session')->getLocale();
        
        $editForm = $this->createForm(new MediaType(), $article, $options);
        
        // Create an array of the current Image objects in the database
        $originalGallery = array();
        foreach ($article->getGallery() as $image)
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
                foreach ($article->getGallery() as $image)
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
                
                $em->persist($article);
                $gallery = $article->getGallery();
                foreach ($gallery as $image)
                {
                    $image->setArticle($article);
                    $em->persist($image);
                }

                $em->flush();

                return $this->redirect($this->generateUrl('back_article_read', array('id' => $id)));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Article:media.html.twig', array(
            'article'   => $article,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Article entity
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
            $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

            if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');

            $article->setStatus(Article::STATUS_DELETED);
            $em->persist($article);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_article_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT ACTIONS ******************************************************
     **************************************************************************/
   
    /**
     * Lists all Article entities
     */
    public function frontIndexAction()
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $countries = $em->getRepository('PFCDTourismBundle:Article')->findCountriesFront();
        
        // the country filter should be displayed only if there are 2 or more different countries
        $countryfilter = count($countries) > 1;
        
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

        $articles = $em->getRepository('PFCDTourismBundle:Article')->findListFront(null, $country);

        return $this->render('PFCDTourismBundle:Front/Article:index.html.twig', array(
            'articles'      => $articles,
            'countryfilter' => $countryfilter,
            'form'          => $form->createView()
        ));
    }

    /**
     * Finds and displays a Article entity
     */
    public function frontReadAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
        $filter['status'] = array(Article::STATUS_ENABLED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');
        
        return $this->render('PFCDTourismBundle:Front/Article:read.html.twig', array(
            'article' => $article
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
