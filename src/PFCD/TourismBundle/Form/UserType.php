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
            ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'The password fields must match', 'first_name' => 'Password', 'second_name' => 'Repeat password' ))
            ->add('email', 'email')
            ->add('firstname')
            ->add('lastname')
            ->add('birthday', 'date', array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
            ->add('gender', 'choice', array('choices' => array(1 => 'Male', 2 => 'Female')))
//            ->add('postalCode')
//            ->add('city')
//            ->add('country', 'country')
//            ->add('locale', 'locale')
            ->add('image', 'file')
        ;
    }

    public function getName()
    {
        return 'user';
    }
}
