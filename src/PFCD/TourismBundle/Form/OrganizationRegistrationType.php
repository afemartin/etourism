<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OrganizationRegistrationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username')
                ->add('email', 'email')
                ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'The password fields must match', 'first_name' => 'Password', 'second_name' => 'Repeat password'))
                ->add('name')
                ->add('acronim')
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
        return 'organization_registration';
    }
}
