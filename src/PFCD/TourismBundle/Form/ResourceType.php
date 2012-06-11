<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Resource;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('type', 'choice', array('choices' => array(Resource::TYPE_MATERIAL_INT => 'Material (internal)', Resource::TYPE_HUMAN_INT => 'Human (internal)', Resource::TYPE_MATERIAL_EXT => 'Material (external)', Resource::TYPE_HUMAN_EXT => 'Human (external)', Resource::TYPE_UNKNOWN => 'Unknown')));
        $builder->add('name');
        $builder->add('description', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('amount', 'integer', array('required' => false, 'attr' => array('class' => 'input-mini')));
        $builder->add('price', 'money', array('required' => false, 'attr' => array('class' => 'input-mini')));
        $builder->add('currency', 'choice', array('required' => false, 'empty_value' => 'Currency', 'choices' => array('EUR' => 'Euro'), 'attr' => array('class' => 'input-small')));
        $builder->add('dateStart', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('dateEnd', 'date', array('required' => false,  'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('timeStart', 'time', array('required' => false, 'hours' => range(23, 0), 'minutes' => range(60-$step, 0, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('timeEnd', 'time', array('required' => false, 'hours' => range(23, 0), 'minutes' => range(60-$step, 0, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('daysWeek', 'choice', array('choices' => array('monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'), 'multiple' => true, 'expanded' => true));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('status', 'choice', array('choices' => array(Resource::STATUS_ENABLED => 'Enabled', Resource::STATUS_DELETED => 'Deleted')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE);
    }
    
    public function getName()
    {
        return 'resource';
    }
}
