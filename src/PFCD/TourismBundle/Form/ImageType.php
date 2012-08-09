<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('description', 'text', array('required' => false, 'attr' => array('placeholder' => 'Description'), 'translatable' => $options['language']));
        $builder->add('file', 'file', array('required' => false, 'label' => 'Image'));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'PFCD\TourismBundle\Entity\Image', 'language' => 'en');
    }

    public function getName()
    {
        return 'image';
    }
}
