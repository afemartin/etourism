<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN) { $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name')); }
        $builder->add('title');
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('fullDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('price', 'money', array('required' => false, 'label' => 'price'));
        $builder->add('currency', 'choice', array('required' => false, 'choices' => array('EUR' => 'Euro')));
        $builder->add('dateStart', 'date', array('required' => false));
        $builder->add('dateEnd', 'date', array('required' => false));
        $builder->add('timeStart', 'time', array('required' => false));
        $builder->add('timeEnd', 'time', array('required' => false));
        $builder->add('status', 'choice', array('choices' => array(Activity::STATUS_PENDING => 'Pending', Activity::STATUS_ENABLED => 'Enabled', Activity::STATUS_LOCKED => 'Locked', Activity::STATUS_DELETED => 'Deleted')));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => null, 'type' => null);
    }
    
    public function getName()
    {
        return 'activity';
    }
}
