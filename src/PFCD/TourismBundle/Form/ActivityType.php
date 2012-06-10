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
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name', 'help' => 'form.activity.field.organization.help'));
        }
        $builder->add('title', 'text');
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysiwyg input-xxlarge')));
        $builder->add('price', 'money', array('required' => false, 'attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.price.help'));
        $builder->add('currency', 'choice', array('required' => false, 'empty_value' => 'Currency', 'choices' => array('EUR' => 'Euro'), 'attr' => array('class' => 'input-small')));
        $builder->add('capacity', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.capacity.help'));
        $builder->add('dateStart', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('dateEnd', 'date', array('required' => false,  'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('timeStart', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('timeEnd', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
        // $builder->add('monday', 'choice', array('choices' => array('monday' => 'monday', 'tuesday' => 'tuesday', 'wednesday' => 'wednesday', 'thursday' => 'thursday', 'friday' => 'friday', 'saturday' => 'saturday', 'sunday' => 'sunday'), 'multiple' => true, 'expanded' => true));
        $builder->add('monday');
        $builder->add('tuesday');
        $builder->add('wednesday');
        $builder->add('thursday');
        $builder->add('friday');
        $builder->add('saturday');
        $builder->add('sunday');
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('geolocation', 'text', array('required' => false, 'help' => 'form.activity.field.geolocation.help'));
            $builder->add('file', 'file', array('required' => false, 'label' => 'Image'));
            $builder->add('video', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.activity.field.video.help'));
            $builder->add('status', 'choice', array('choices' => array(Activity::STATUS_PENDING => 'Pending', Activity::STATUS_ENABLED => 'Enabled', Activity::STATUS_LOCKED => 'Locked', Activity::STATUS_DELETED => 'Deleted')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE);
    }
    
    public function getName()
    {
        return 'activity';
    }
}
