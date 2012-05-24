<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Entity\User;

class UserType extends AbstractType
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
                ->add('status', 'choice', array('choices' => array(User::STATUS_PENDING => 'Pending', User::STATUS_ENABLED => 'Enabled', User::STATUS_LOCKED => 'Locked', User::STATUS_DELETED => 'Deleted')))
        ;
    }

    public function getName()
    {
        return 'user';
    }
}
