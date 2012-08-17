<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Session;

class SessionFilterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('activity', 'entity', array('required' => false, 'class' => 'PFCDTourismBundle:Activity', 'property' => 'title', 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['organization'] != null)
        {
            $builder->add('activity', 'entity', array('required' => false, 'class' => 'PFCDTourismBundle:Activity', 'property' => 'title', 'empty_value' => 'Select an activity',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                            ->where('a.organization = :organization')
                            ->orderBy('a.title', 'ASC')
                            ->setParameter('organization', $options['organization']);
                }));
        }

        $builder->add('dateStart', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('dateEnd', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('startTime', 'time', array('required' => false, 'hours' => range(23, 0), 'minutes' => range(60-$step, 0, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('daysWeek', 'choice', array('choices' => array(Constants::MONDAY => 'dayweek.3dig.' . Constants::MONDAY, Constants::TUESDAY => 'dayweek.3dig.' . Constants::TUESDAY, Constants::WEDNESDAY => 'dayweek.3dig.' . Constants::WEDNESDAY, Constants::THURSDAY => 'dayweek.3dig.' . Constants::THURSDAY, Constants::FRIDAY => 'dayweek.3dig.' . Constants::FRIDAY, Constants::SATURDAY => 'dayweek.3dig.' . Constants::SATURDAY, Constants::SUNDAY => 'dayweek.3dig.' . Constants::SUNDAY), 'multiple' => true, 'expanded' => true));
        $builder->add('status', 'choice', array('choices' => array(Session::STATUS_PENDING => 'entity.session.field.status.' . Session::STATUS_PENDING, Session::STATUS_ENABLED => 'entity.session.field.status.' . Session::STATUS_ENABLED, Session::STATUS_LOCKED => 'entity.session.field.status.' . Session::STATUS_LOCKED, Session::STATUS_DELETED => 'entity.session.field.status.' . Session::STATUS_DELETED), 'multiple' => true, 'expanded' => true));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'organization' => null);
    }
    
    public function getName()
    {
        return 'session_filter';
    }
}
