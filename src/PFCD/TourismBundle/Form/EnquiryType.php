<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

class EnquiryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['type'] == Constants::ENQUIRY_FULL)
        {
            $builder->add('name', 'text', array('attr' => array('class' => 'input-xlarge')));
            $builder->add('email', 'email', array('attr' => array('class' => 'input-xlarge')));
        }
        $builder->add('subject', 'text', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('message', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('type' => Constants::ENQUIRY_FULL);
    }

    public function getName()
    {
        return 'contact';
    }
}

?>
