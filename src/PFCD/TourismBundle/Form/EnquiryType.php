<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EnquiryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('attr' => array('class' => 'input-xlarge')));
        $builder->add('email', 'email', array('attr' => array('class' => 'input-xlarge')));
        $builder->add('subject', 'text', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('message', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
    }

    public function getName()
    {
        return 'contact';
    }
}

?>
