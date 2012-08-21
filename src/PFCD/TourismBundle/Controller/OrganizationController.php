<?php

namespace PFCD\TourismBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Form\OrganizationType;
use PFCD\TourismBundle\Form\MediaType;

use PFCD\TourismBundle\Entity\OrganizationFilter;
use PFCD\TourismBundle\Form\OrganizationFilterType;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Article;

use PFCD\TourismBundle\Entity\Enquiry;
use PFCD\TourismBundle\Form\EnquiryType;

use PFCD\TourismBundle\Entity\Image;

/**
 * Organization controller
 */
class OrganizationController extends Controller
{
    
    /**************************************************************************
     ***** BACK AREA **********************************************************
     **************************************************************************/
    
    /**
     * Lists all Organization entities
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backIndexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organizations = $em->getRepository('PFCDTourismBundle:Organization')->findAll();

        return $this->render('PFCDTourismBundle:Back/Organization:index.html.twig', array(
            'organizations' => $organizations
        ));
    }

    /**
     * Displays a form to create a new Organization entity and store it when the form is submitted and valid
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backCreateAction()
    {
        $options['domain'] = Constants::ADMIN;
        $options['type'] = Constants::FORM_CREATE;
        $options['language'] = $this->get('session')->getLocale();
        $options['supported_languages'] = $this->container->getParameter('locales');
        
        $organization  = new Organization();
        $form = $this->createForm(new OrganizationType(), $organization, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($organization);
                $em->flush();

                return $this->redirect($this->generateUrl('back_organization_read', array('id' => $organization->getId())));
            }
        }

        return $this->render('PFCDTourismBundle:Back/Organization:create.html.twig', array(
            'organization' => $organization,
            'form'         => $form->createView()
        ));
    }
    
    /**
     * Finds and displays a Organization entity
     */
    public function backReadAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($id);

        $translations = $em->getRepository('StofDoctrineExtensionsBundle:Translation')->findTranslations($organization);
        
        return $this->render('PFCDTourismBundle:Back/Organization:read.html.twig', array(
            'organization' => $organization,
            'translations' => $translations,
            'delete_form'  => $deleteForm->createView(),
        ));
    }
    
    /**
     * Finds and displays a Organization entity
     */
    public function backPreviewAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');
        
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException();
        }
        
        unset($filter);
        $filter['organization'] = $id;
        $filter['status'] = Activity::STATUS_ENABLED;
        
        $orderBy['created'] = 'DESC';
        
        $limit = 2;
        
        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findBy($filter, $orderBy, $limit);
        
        unset($filter);
        $filter['organization'] = $id;
        $filter['status'] = Article::STATUS_ENABLED;
        
        $orderBy['created'] = 'DESC';
        
        $articles = $em->getRepository('PFCDTourismBundle:Article')->findBy($filter, $orderBy, $limit);
        
        $this->get('session')->setFlash('alert-info', $this->get('translator')->trans('alert.info.organizationpreview'));
        
        return $this->render('PFCDTourismBundle:Back/Organization:preview.html.twig', array(
            'organization' => $organization,
            'activities'   => $activities,
            'articles'     => $articles,
        ));
    }
    
    /**
     * Edits an existent Organization entity and store it when the form is submitted and valid
     */
    public function backUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException();
        }
        
        $options['domain'] = $this->get('security.context')->isGranted('ROLE_ADMIN') ? Constants::ADMIN : Constants::BACK;
        $options['type'] = Constants::FORM_UPDATE;
        $options['language'] = $this->get('session')->getLocale();
        $options['supported_languages'] = $this->container->getParameter('locales');
        
        $editForm = $this->createForm(new OrganizationType(), $organization, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $organization->setLogo();

                $em->persist($organization);
                $em->flush();

                return $this->redirect($this->generateUrl('back_organization_read', array('id' => $id)));
            }
            else
            {
                $organization->setFile(null);
            }
        }
       
        return $this->render('PFCDTourismBundle:Back/Organization:update.html.twig', array(
            'organization' => $organization,
            'edit_form'    => $editForm->createView(),
        ));
    }
    
    /**
     * Edits an existent Organization entity and store it when the form is submitted and valid
     */
    public function backMediaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
        {
            throw new AccessDeniedException();
        }
        
        $options['entity'] = Constants::ORGANIZATION;
        $options['language'] = $this->get('session')->getLocale();
        
        $editForm = $this->createForm(new MediaType(), $organization, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bindRequest($request);

            if ($editForm->isValid())
            {
                $em->persist($organization);
                $em->flush();

                return $this->redirect($this->generateUrl('back_organization_read', array('id' => $id)));
            }
        }
       
        return $this->render('PFCDTourismBundle:Back/Organization:media.html.twig', array(
            'organization' => $organization,
            'edit_form'    => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Organization entity
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function backDeleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $organization = $em->getRepository('PFCDTourismBundle:Organization')->find($id);

            if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

            if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') && $id != $this->get('security.context')->getToken()->getUser()->getId())
            {
                throw new AccessDeniedException();
            }

            $organization->setStatus(Organization::STATUS_DELETED);
            $em->persist($organization);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_organization_index'));
    }
    
    
    /**************************************************************************
     ***** FRONT AREA *********************************************************
     **************************************************************************/
   
    /**
     * Lists all Organization entities
     */
    public function frontIndexAction()
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $em = $this->getDoctrine()->getEntityManager();

        $countries = $em->getRepository('PFCDTourismBundle:Organization')->findCountriesFront();
        
        // the country filter should be displayed only if there are 2 or more different countries
        $countryfilter = count($countries) > 1;
        
        foreach ($countries as $country) $options['countries'][$country['country']] = $country['country'];
        
        $organizationFilter = new OrganizationFilter();
        
        $form = $this->createForm(new OrganizationFilterType(), $organizationFilter, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
        }
        
        $country = $organizationFilter->getCountry();
        
        if ($country) $filter['country'] = $country;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $organizations = $em->getRepository('PFCDTourismBundle:Organization')->findBy($filter);

        return $this->render('PFCDTourismBundle:Front/Organization:index.html.twig', array(
            'organizations' => $organizations,
            'countryfilter' => $countryfilter,
            'form'          => $form->createView()
        ));
    }
    
    /**
     * Displays a form to create a new Organization entity and store it when the form is submitted and valid
     */
    public function frontCreateAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $options['domain'] = Constants::FRONT;
        $options['type'] = Constants::FORM_CREATE;
        
        $organization = new Organization();
        $form = $this->createForm(new OrganizationType(), $organization, $options);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em->persist($organization);
                $em->flush();

                $template = 'registration.txt.twig';
                $message = \Swift_Message::newInstance()
                        ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.registration.subject'))
                        ->setFrom($this->container->getParameter('pfcd_tourism.emails.no_reply_email'))
                        ->setTo($this->container->getParameter('pfcd_tourism.emails.incoming_email'))
                        ->setBody($this->renderView('PFCDTourismBundle:Mail:' . $template, array('organization' => $organization)));
                
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.createorganization'));
                
                return $this->redirect($this->generateUrl('front_index'));
            }
        }
        
        // load the template with the legal stuff in the proper language
        $legal = $this->renderView($this->findLocalizedTemplate('PFCDTourismBundle:Front/Organization:legal.%s.txt.twig', $this->get('session')->getLocale()));

        return $this->render('PFCDTourismBundle:Front/Organization:create.html.twig', array(
            'organization' => $organization,
            'legal'        => $legal,
            'form'         => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Organization entity
     */
    public function frontReadAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);

        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');
        
        unset($filter);
        $filter['organization'] = $id;
        $filter['status'] = Activity::STATUS_ENABLED;
        
        $orderBy['created'] = 'DESC';
        
        $limit = 2;
        
        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findBy($filter, $orderBy, $limit);
        
        unset($filter);
        $filter['organization'] = $id;
        $filter['status'] = Article::STATUS_ENABLED;
        
        $orderBy['created'] = 'DESC';
        
        $articles = $em->getRepository('PFCDTourismBundle:Article')->findBy($filter, $orderBy, $limit);

        return $this->render('PFCDTourismBundle:Front/Organization:read.html.twig', array(
            'organization' => $organization,
            'activities'   => $activities,
            'articles'     => $articles,
        ));
    }
    
    /**
     * Lists all Activities of the Organization
     */
    public function frontActivitiesAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);
        
        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

        $activities = $em->getRepository('PFCDTourismBundle:Activity')->findListFront($id);
        
        return $this->render('PFCDTourismBundle:Front/Organization:activities.html.twig', array(
            'organization' => $organization,
            'activities' => $activities
        ));
    }
    
    /**
     * Lists all Articles of the Organization
     */
    public function frontArticlesAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);
        
        if (!$organization) throw $this->createNotFoundException('Unable to find Organization entity.');

        $articles = $em->getRepository('PFCDTourismBundle:Article')->findListFront($id);
        
        return $this->render('PFCDTourismBundle:Front/Organization:articles.html.twig', array(
            'organization' => $organization,
            'articles' => $articles
        ));
    }
    
    /**
     * Finds and displays a Organization donate information
     */
    public function frontDonateAction($id)
    {
        // force translation fallback to display something and not just an empty text
        $this->container->get('stof_doctrine_extensions.listener.translatable')->setTranslationFallback(true);
        
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);
        
        if (!$organization || !$organization->getDonate()) throw $this->createNotFoundException('Unable to find Organization entity.');
        
        return $this->render('PFCDTourismBundle:Front/Organization:donate.html.twig', array(
            'organization' => $organization
        ));
    }
    
    /**
     * Display a contact form and send a enquiry to the Organization administrator when the form is submitted and valid
     */
    public function frontContactAction($id)
    {
        $filter['id'] = $id;
        $filter['status'] = array(Organization::STATUS_ENABLED, Organization::STATUS_LOCKED);
        
        $em = $this->getDoctrine()->getEntityManager();

        $organization = $em->getRepository('PFCDTourismBundle:Organization')->findOneBy($filter);
        
        if (!$organization || !$organization->getEmail()) throw $this->createNotFoundException('Unable to find Organization entity.');
                
        $enquiry = new Enquiry();
        
        $options['type'] = Constants::ENQUIRY_FULL;
        
        if ($this->get('security.context')->isGranted('ROLE_USER'))
        {
            $options['type'] = Constants::ENQUIRY_MINI;
            
            $user = $this->get('security.context')->getToken()->getUser();
            $enquiry->setName($user->getFirstname() . ' ' . $user->getLastname());
            $enquiry->setEmail($user->getEmail());
        }
        
        $form = $this->createForm(new EnquiryType(), $enquiry, $options);

        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $message = \Swift_Message::newInstance()
                        ->setSubject('[' . $this->container->getParameter('pfcd_tourism.domain_name') . '] ' . $this->get('translator')->trans('email.contactenquiry.subject', array('%name%' => $enquiry->getName())))
                        ->setFrom($this->container->getParameter('pfcd_tourism.emails.outgoing_email'))
                        ->setTo($organization->getEmail())
                        ->setBody($this->renderView('PFCDTourismBundle:Mail:contact.txt.twig', array('enquiry' => $enquiry)));
                
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('alert-success', $this->get('translator')->trans('alert.success.contactenquiry'));

                return $this->redirect($this->generateUrl('front_organization_contact', array('id' => $organization->getId())));
            }
        }

        return $this->render('PFCDTourismBundle:Front/Organization:contact.html.twig', array(
            'organization' => $organization,
            'form' => $form->createView()
        ));
    }

    
    /**************************************************************************
     ***** COMMON FUNCTIONS ***************************************************
     **************************************************************************/
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
    
    /**
     * Given the route of a template and the wanted locale, it find if the template exists
     * if not it return the fallback template ('en' english language) 
     * 
     * @param string $template route of the template with a "%s" representing the locale
     * @param string $locale the locale 2-digits code
     * @return string route of the found template
     */
    private function findLocalizedTemplate($template, $locale)
    {
        $template_localized = sprintf($template, $locale);
        
        if (!$this->get('templating')->exists($template_localized))
        {
            $template_localized = sprintf($template, 'en');
        }
        
        return $template_localized;
    }
    
}
