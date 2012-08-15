<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OrganizationFilterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('country', 'choice', array('required' => false, 'choices' => $options['countries'], 'empty_value' => 'All countries', 'attr' => array('class' => 'input-medium'), 'countrylist' => true));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('countries' => array());
    }
    
    public function getName()
    {
        return 'organization_filter';
    }
}
