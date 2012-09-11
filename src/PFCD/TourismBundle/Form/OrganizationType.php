<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Organization;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::FRONT)
        {
            $builder->add('username');
        }
        if ($options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'password.match.error', 'first_name' => 'Password', 'second_name' => 'Repeat password'));
        }
        $builder->add('email', 'email', array('attr' => array('class' => 'input-xlarge')));
        $builder->add('name', 'text', array('attr' => array('class' => 'input-xxlarge'), 'label' => 'Organizationname'));
        $builder->add('acronim', 'text', array('required' => false, 'attr' => array('class' => 'input-medium')));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        }
        $builder->add('webpage', 'url', array('required' => false, 'attr' => array('class' => 'input-xlarge')));
        $builder->add('foundationYear', 'integer', array('required' => false, 'attr' => array('class' => 'input-mini')));
        $builder->add('country', 'country', array('empty_value' => 'Select your country'));
        $builder->add('city');
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('address');
            $builder->add('postalCode');
            $builder->add('phone');
            $builder->add('bankName', 'text', array('required' => false, 'help' => 'form.organization.field.bankname.help'));
            $builder->add('bankAccount', 'text', array('required' => false, 'help' => 'form.organization.field.bankaccount.help'));
        }
        if ($options['domain'] == Constants::FRONT)
        {
            $builder->add('locale', 'choice', array('choices' => $options['supported_languages'], 'empty_value' => 'Select your language', 'localelist' => true));
        }
        else
        {
            $builder->add('locale', 'choice', array('choices' => $options['supported_languages'], 'empty_value' => 'Select your language', 'localelist' => true, 'help' => 'form.organization.field.locale.help'));
        }
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('donate', 'checkbox', array('required' => false, 'help' => 'form.organization.field.donate.help'));
            $builder->add('donateDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
            $builder->add('languages', 'choice', array('attr' => array('style' => 'display: inline-block'), 'choices' => $options['supported_languages'], 'multiple' => true, 'expanded' => true, 'localelist' => true, 'help' => 'form.organization.field.languages.help'));
            $builder->add('file', 'file', array('required' => false, 'label' => 'Logo', 'help' => 'form.organization.field.logo.help'));
        }
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'language' => 'en', 'supported_languages' => array());
    }

    public function getName()
    {
        return 'organization';
    }
}

?>
