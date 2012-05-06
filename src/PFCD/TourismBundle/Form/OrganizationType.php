<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username');
        $builder->add('password');
        $builder->add('email');
        $builder->add('name');
        $builder->add('shortDesc', 'textarea');
    }

    public function getName()
    {
        return 'organization';
    }
}

?>
