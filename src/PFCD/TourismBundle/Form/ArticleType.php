<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Article;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('title', 'text', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('languages', 'entity', array('class' => 'PFCDTourismBundle:Language', 'property' => 'name', 'multiple' => true, 'expanded' => true));
            $builder->add('file', 'file', array('required' => false, 'label' => 'Image'));
            $builder->add('status', 'choice', array('choices' => array(Article::STATUS_PENDING => 'Pending', Article::STATUS_ENABLED => 'Enabled', Article::STATUS_LOCKED => 'Locked', Article::STATUS_DELETED => 'Deleted')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'language' => 'en');
    }
    
    public function getName()
    {
        return 'article';
    }
}
