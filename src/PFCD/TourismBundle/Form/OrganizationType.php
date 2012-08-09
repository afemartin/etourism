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
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('username');
        }
        $builder->add('email');
        if ($options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('password', 'repeated', array('type' => 'password', 'invalid_message' => 'password.match.error', 'first_name' => 'Password', 'second_name' => 'Repeat password'));
        }
        $builder->add('name');
        $builder->add('acronim');
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
            $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        }
        $builder->add('foundationYear', 'integer', array('required' => false));
        $builder->add('country', 'country', array('empty_value' => 'Select your country'));
        $builder->add('city');
        $builder->add('address');
        $builder->add('postalCode');
        $builder->add('phone');
        $builder->add('bankName', 'text', array('required' => false, 'help' => 'form.organization.field.bankname.help'));
        $builder->add('bankAccount', 'text', array('required' => false, 'help' => 'form.organization.field.bankaccount.help'));
        $builder->add('locale', 'choice', array('choices' => array('en' => 'English', 'es' => 'Spanish'), 'empty_value' => 'Select your language'));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('languages', 'entity', array('class' => 'PFCDTourismBundle:Language', 'property' => 'name', 'multiple' => true, 'expanded' => true));
            $builder->add('file', 'file', array('required' => false, 'label' => 'Logo', 'help' => 'form.organization.field.logo.help'));
        }
        if ($options['domain'] == Constants::ADMIN && $options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('status', 'choice', array('choices' => array(Organization::STATUS_PENDING => 'Pending', Organization::STATUS_ENABLED => 'Enabled', Organization::STATUS_LOCKED => 'Locked', Organization::STATUS_DELETED => 'Deleted')));
        }
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'language' => 'en');
    }

    public function getName()
    {
        return 'organization';
    }
}

?>
