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
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('title');
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('fullDesc', 'textarea', array('attr' => array('class' => 'wysiwyg input-xxlarge')));
        $builder->add('price', 'money', array('required' => false, 'attr' => array('class' => 'input-mini')));
        $builder->add('currency', 'choice', array('required' => false, 'empty_value' => 'Currency', 'choices' => array('EUR' => 'Euro'), 'attr' => array('class' => 'input-small')));
        $builder->add('dateStart', 'date', array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') + 1, date('Y') - 1), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('dateEnd', 'date', array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') + 1, date('Y') - 1), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('timeStart', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('timeEnd', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('geolocation', 'text', array('required' => false));
            $builder->add('file', 'file', array('required' => false));
            $builder->add('video', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge')));
            $builder->add('status', 'choice', array('choices' => array(Activity::STATUS_PENDING => 'Pending', Activity::STATUS_ENABLED => 'Enabled', Activity::STATUS_LOCKED => 'Locked', Activity::STATUS_DELETED => 'Deleted')));
        }
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
