<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Entity\Organization;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username')
                ->add('email')
                ->add('name')
                ->add('acronim')
                ->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')))
                ->add('fullDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')))
                ->add('country', 'country')
                ->add('city')
                ->add('address')
                ->add('postalCode')
                ->add('phone')
                ->add('locale', 'choice', array('choices' => array('en' => 'English', 'es' => 'Spanish')))
                ->add('status', 'choice', array('choices' => array(Organization::STATUS_PENDING => 'Pending', Organization::STATUS_ENABLED => 'Enabled', Organization::STATUS_LOCKED => 'Locked', Organization::STATUS_DELETED => 'Deleted')))
        ;
    }

    public function getName()
    {
        return 'organization';
    }
}

?>
