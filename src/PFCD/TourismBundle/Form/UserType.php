<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('email', 'email');
            $builder->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'password.match.error', 'first_name' => 'Password', 'second_name' => 'Repeat password'));
        }
        $builder->add('firstname');
        $builder->add('lastname');
        $builder->add('birthday', 'birthday', array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') - 5, date('Y') - 80), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('gender', 'choice', array('choices' => array(User::GENDER_MALE => 'Male', User::GENDER_FEMALE => 'Female')));
        $builder->add('country', 'country');
        $builder->add('city');
        $builder->add('address');
        $builder->add('postalCode');
        $builder->add('phone');
        $builder->add('locale', 'choice', array('choices' => array('en' => 'English', 'es' => 'Spanish')));
        if ($options['domain'] == Constants::ADMIN && $options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('status', 'choice', array('choices' => array(User::STATUS_PENDING => 'Pending', User::STATUS_ENABLED => 'Enabled', User::STATUS_LOCKED => 'Locked', User::STATUS_DELETED => 'Deleted')));
        }
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE);
    }
    
    public function getName()
    {
        return 'user';
    }
}
