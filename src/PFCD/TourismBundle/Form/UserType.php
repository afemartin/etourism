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
        $builder->add('firstname', 'text', array('required' => true));
        $builder->add('lastname');
        $builder->add('birthday', 'birthday', array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') - 5, date('Y') - 90), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('gender', 'choice', array('required' => false, 'choices' => array(User::GENDER_MALE => 'entity.user.field.gender.' . User::GENDER_MALE, User::GENDER_FEMALE => 'entity.user.field.gender.' . User::GENDER_FEMALE), 'expanded' => true));
        $builder->add('country', 'country', array('required' => false, 'empty_value' => 'Select your country'));
        if ($options['type'] == Constants::FORM_UPDATE || $options['domain'] == Constants::ADMIN)
        {
            $builder->add('city');
            $builder->add('address');
            $builder->add('postalCode');
            $builder->add('phone');
        }
        $builder->add('locale', 'choice', array('choices' => $options['supported_languages'], 'empty_value' => 'Select your language', 'localelist' => true));
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('status', 'choice', array('choices' => array(User::STATUS_PENDING => 'entity.user.field.status.' . User::STATUS_PENDING, User::STATUS_ENABLED => 'entity.user.field.status.' . User::STATUS_ENABLED, User::STATUS_LOCKED => 'entity.user.field.status.' . User::STATUS_LOCKED, User::STATUS_DELETED => 'entity.user.field.status.' . User::STATUS_DELETED), 'help' => 'form.user.field.status.help'));
        }
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::FRONT, 'type' => Constants::FORM_CREATE, 'supported_languages' => array());
    }
    
    public function getName()
    {
        return 'user';
    }
}
