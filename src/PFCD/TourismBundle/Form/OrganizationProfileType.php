<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name')
                ->add('acronim')
                ->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')))
                ->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge')))
                ->add('country', 'country')
                ->add('city')
                ->add('address')
                ->add('postalCode')
                ->add('phone')
                ->add('locale', 'choice', array('choices' => array('en' => 'English', 'es' => 'Spanish')))
        ;
    }

    public function getName()
    {
        return 'organization_profile';
    }
}

?>
