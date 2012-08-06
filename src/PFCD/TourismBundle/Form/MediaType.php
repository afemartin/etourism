<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['entity'] == Constants::ORGANIZATION)
        {
            $builder->add('video', 'textarea', array('required' => false, 'attr' => array('placeholder' => 'Embed code', 'class' => 'input-xxlarge')));
            $builder->add('geolocation', 'hidden', array('required' => false, 'attr' => array('class' => 'input-xlarge', 'readonly' => 'readonly')));
        }
        
        if ($options['entity'] == Constants::ACTIVITY)
        {
            $builder->add('gallery', 'collection', array('type' => new ImageType(), 'allow_add' => true, 'allow_delete' => true, 'prototype' => true, 'by_reference' => false));
            $builder->add('video', 'textarea', array('required' => false, 'attr' => array('placeholder' => 'Embed code', 'class' => 'input-xxlarge')));
            $builder->add('geolocation', 'hidden', array('required' => false, 'attr' => array('class' => 'input-xlarge', 'readonly' => 'readonly')));
            $builder->add('zoom', 'hidden', array('required' => false, 'attr' => array('class' => 'input-xlarge', 'readonly' => 'readonly')));
        }
        
        if ($options['entity'] == Constants::ARTICLE)
        {
            $builder->add('gallery', 'collection', array('type' => new ImageType(), 'allow_add' => true, 'allow_delete' => true, 'prototype' => true, 'by_reference' => false));
            $builder->add('video', 'textarea', array('required' => false, 'attr' => array('placeholder' => 'Embed code', 'class' => 'input-xxlarge')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('entity' => null);
    }
    
    public function getName()
    {
        return 'media';
    }
}
