<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Entity\User;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('firstname')
                ->add('lastname')
                ->add('birthday', 'date', array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
                ->add('gender', 'choice', array('choices' => array(User::GENDER_MALE => 'Male', User::GENDER_FEMALE => 'Female')))
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
        return 'user_profile';
    }
}
