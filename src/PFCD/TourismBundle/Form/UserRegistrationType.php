<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Entity\User;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email')
                ->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'The password fields must match', 'first_name' => 'Password', 'second_name' => 'Repeat password'))
                ->add('firstname')
                ->add('lastname')
                ->add('birthday', 'date', array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
                ->add('gender', 'choice', array('choices' => array(User::GENDER_MALE => 'Male', User::GENDER_FEMALE => 'Female')))
                ->add('country', 'country')
                ->add('city')
                ->add('locale', 'choice', array('choices' => array('en' => 'English', 'es' => 'Spanish')))
        ;
    }

    public function getName()
    {
        return 'user_registration';
    }
}
