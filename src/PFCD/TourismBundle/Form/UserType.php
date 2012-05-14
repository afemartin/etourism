<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('birthday')
            ->add('gender')
            ->add('postalCode')
            ->add('city')
            ->add('locale')
        ;
    }

    public function getName()
    {
        return 'pfcd_tourismbundle_usertype';
    }
}
