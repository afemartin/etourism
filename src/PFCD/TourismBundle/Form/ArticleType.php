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
        $builder->add('title', 'text', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('languages', 'choice', array('attr' => array('style' => 'display: inline-block'), 'choices' => $options['supported_languages'], 'multiple' => true, 'expanded' => true, 'localelist' => true, 'help' => 'form.article.field.languages.help'));
        $builder->add('file', 'file', array('required' => false, 'label' => 'Image'));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('status', 'choice', array('choices' => array(Article::STATUS_PENDING => 'entity.article.field.status.' . Article::STATUS_PENDING, Article::STATUS_ENABLED => 'entity.article.field.status.' . Article::STATUS_ENABLED), 'help' => 'form.article.field.status.help'));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'language' => 'en', 'supported_languages' => array());
    }
    
    public function getName()
    {
        return 'article';
    }
}
