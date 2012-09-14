<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('attr' => array('class' => 'input-xlarge'), 'help' => 'form.category.field.name.help'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK);
    }
    
    public function getName()
    {
        return 'category';
    }
}
