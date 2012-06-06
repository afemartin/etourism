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
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('type', 'choice', array('choices' => array(Resource::TYPE_MATERIAL_INT => 'Material (internal)', Resource::TYPE_HUMAN_INT => 'Human (internal)', Resource::TYPE_MATERIAL_EXT => 'Material (external)', Resource::TYPE_HUMAN_EXT => 'Human (external)', Resource::TYPE_UNKNOWN => 'Unknown')));
        $builder->add('name');
        $builder->add('description', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('amount', 'integer');
        $builder->add('price', 'money', array('required' => false, 'attr' => array('class' => 'input-mini')));
        $builder->add('currency', 'choice', array('required' => false, 'empty_value' => 'Currency', 'choices' => array('EUR' => 'Euro'), 'attr' => array('class' => 'input-small')));
        $builder->add('dateStart', 'date', array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') + 1, date('Y') - 1), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('dateEnd', 'date', array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') + 1, date('Y') - 1), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('timeStart', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('timeEnd', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
//        $builder->add('monday', 'choice', array('choices' => array('monday' => 'monday', 'tuesday' => 'tuesday', 'wednesday' => 'wednesday', 'thursday' => 'thursday', 'friday' => 'friday', 'saturday' => 'saturday', 'sunday' => 'sunday'), 'multiple' => true, 'expanded' => true));
        $builder->add('monday');
        $builder->add('tuesday');
        $builder->add('wednesday');
        $builder->add('thursday');
        $builder->add('friday');
        $builder->add('saturday');
        $builder->add('sunday');
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
