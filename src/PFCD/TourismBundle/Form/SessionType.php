<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Activity;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['organization'] != null)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'empty_value' => 'Select an activity',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                              ->where('a.organization = :organization')
                              ->andWhere('a.status IN (:status)')
                              ->orderBy('a.title', 'ASC')
                              ->setParameter('organization', $options['organization'])
                              ->setParameter('status', array(Activity::STATUS_PENDING, Activity::STATUS_ENABLED, Activity::STATUS_LOCKED));
                }));
        }
        $builder->add('date', 'date', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('time', 'time', array('required' => false, 'hours' => range(0, 23), 'minutes' => range(0, 60-$step, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.session.field.note.help'));
        if ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK)
        {
            $builder->add('status', 'choice', array('choices' => array(Session::STATUS_PENDING => 'entity.session.field.status.' . Session::STATUS_PENDING, Session::STATUS_ENABLED => 'entity.session.field.status.' . Session::STATUS_ENABLED, Session::STATUS_LOCKED => 'entity.session.field.status.' . Session::STATUS_LOCKED, Session::STATUS_DELETED => 'entity.session.field.status.' . Session::STATUS_DELETED), 'help' => 'form.session.field.status.help'));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'validation_groups' => 'Default', 'organization' => null);
    }
    
    public function getName()
    {
        return 'session';
    }
}
