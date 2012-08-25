<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Comment;
use PFCD\TourismBundle\Form\CommentType;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Article;

/**
 * Comment controller
 */
class CommentController extends Controller
{
    
    /**************************************************************************
     ***** BACK ACTIONS *******************************************************
     **************************************************************************/
    
    /**
     * Lists all Comment entities
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $comments = $em->getRepository('PFCDTourismBundle:Comment')->findAll();
        }
        else
        {
            $organization = $this->get('security.context')->getToken()->getUser()->getId();
            $comments = $em->getRepository('PFCDTourismBundle:Comment')->findAllFiltered($organization);
        }

        return $this->render('PFCDTourismBundle:Back/Comment:index.html.twig', array(
            'comments' => $comments
        ));
    }

    /**
     * Finds and displays all Comments entities related to the Activity and also displays
     * a form to create a new Comment entity and store it when the form is submitted and valid
     */
    public function backActivityAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');
        
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $comment->setOrganization($this->get('security.context')->getToken()->getUser());
                }
                
                $comment->setActivity($activity);
                
                $em->persist($comment);
                $em->flush();
                
                return $this->redirect($this->generateUrl('back_comment_activity', array('id' => $id)));
            }
        }

        $comments = $activity->getComments();
        
        return $this->render('PFCDTourismBundle:Back/Comment:activity.html.twig', array(
            'activity' => $activity,
            'comments' => $comments,
            'form'     => $form->createView(),
        ));
    }

    /**
     * Finds and displays all Comments entities related to the Article and also displays
     * a form to create a new Comment entity and store it when the form is submitted and valid
     */
    public function backArticleAction($id)
    {
        $filter['id'] = $id;
                
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $filter['organization'] = $this->get('security.context')->getToken()->getUser()->getId();
        }
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');
        
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
                {
                    $comment->setOrganization($this->get('security.context')->getToken()->getUser());
                }
                
                $comment->setArticle($article);
                
                $em->persist($comment);
                $em->flush();
                
                return $this->redirect($this->generateUrl('back_comment_article', array('id' => $id)));
            }
        }

        $comments = $article->getComments();
        
        return $this->render('PFCDTourismBundle:Back/Comment:article.html.twig', array(
            'article'  => $article,
            'comments' => $comments,
            'form'     => $form->createView(),
        ));
    }
    
    /**
     * Edits an existent Comment entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $comment = $em->getRepository('PFCDTourismBundle:Comment')->find($id);

        if (!$comment) throw $this->createNotFoundException('Unable to find Comment entity.');

        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
        {
            $organization = $this->get('security.context')->getToken()->getUser();

            // verify that the comment belong to the logged organization and the author it is not the administrator
            if (($comment->getActivity() && $comment->getActivity()->getOrganization()->getId() != $organization->getId()) ||
                ($comment->getArticle() && $comment->getArticle()->getOrganization()->getId() != $organization->getId()) ||
                !($comment->getUser() || $comment->getOrganization()))
            {
                throw new AccessDeniedException();
            }
        }
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
                
        $editForm = $this->createForm(new CommentType(), $comment, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($comment);
                $em->flush();

                return $this->redirect($this->generateUrl('back_comment_index'));
            }
        }    

        return $this->render('PFCDTourismBundle:Back/Comment:update.html.twig', array(
            'comment'   => $comment,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Comment entity
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $comment = $em->getRepository('PFCDTourismBundle:Comment')->find($id);

            if (!$comment) throw $this->createNotFoundException('Unable to find Comment entity.');
            
            if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION'))
            {
                $organization = $this->get('security.context')->getToken()->getUser();

                // verify that the comment belong to the logged organization and the author it is not the administrator
                if (($comment->getActivity() && $comment->getActivity()->getOrganization()->getId() != $organization->getId()) ||
                    ($comment->getArticle() && $comment->getArticle()->getOrganization()->getId() != $organization->getId()) ||
                    !($comment->getUser() || $comment->getOrganization()))
                {
                    throw new AccessDeniedException();
                }
            }

            $comment->setStatus(Comment::STATUS_DELETED);
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_comment_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT ACTIONS ******************************************************
     **************************************************************************/
   
    /**
     * Create a new Comment entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontActivityAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = array(Activity::STATUS_ENABLED, Activity::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $activity = $em->getRepository('PFCDTourismBundle:Activity')->findOneBy($filter);

        if (!$activity) throw $this->createNotFoundException('Unable to find Activity entity.');
        
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $comment->setUser($this->get('security.context')->getToken()->getUser());
                $comment->setActivity($activity);
                
                $em->persist($comment);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('front_activity_read', array('id' => $id)));
    }
   
    /**
     * Create a new Comment entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function frontArticleAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = array(Article::STATUS_ENABLED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $article = $em->getRepository('PFCDTourismBundle:Article')->findOneBy($filter);

        if (!$article) throw $this->createNotFoundException('Unable to find Article entity.');
        
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $comment->setUser($this->get('security.context')->getToken()->getUser());
                $comment->setArticle($article);
                
                $em->persist($comment);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('front_article_read', array('id' => $id)));
    }
    
    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
    
}
