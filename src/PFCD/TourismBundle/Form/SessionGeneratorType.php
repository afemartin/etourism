<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Activity;

class SessionGeneratorType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'empty_value' => 'Select an activity',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                            ->where('a.organization = :organization')
                            ->andWhere('a.status IN (:status)')
                            ->orderBy('a.title', 'ASC')
                            ->setParameter('organization', $options['organization'])
                            ->setParameter('status', array(Activity::STATUS_ENABLED, Activity::STATUS_LOCKED));
                }));
        }

        $builder->add('dateStart', 'date', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('dateEnd', 'date', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('startTime1', 'time', array('required' => false, 'hours' => range(0, 23), 'minutes' => range(0, 60-$step, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('startTime2', 'time', array('required' => false, 'hours' => range(0, 23), 'minutes' => range(0, 60-$step, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('startTime3', 'time', array('required' => false, 'hours' => range(0, 23), 'minutes' => range(0, 60-$step, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('startTime4', 'time', array('required' => false, 'hours' => range(0, 23), 'minutes' => range(0, 60-$step, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        $builder->add('daysWeek', 'choice', array('choices' => array(Constants::MONDAY => 'dayweek.3dig.' . Constants::MONDAY, Constants::TUESDAY => 'dayweek.3dig.' . Constants::TUESDAY, Constants::WEDNESDAY => 'dayweek.3dig.' . Constants::WEDNESDAY, Constants::THURSDAY => 'dayweek.3dig.' . Constants::THURSDAY, Constants::FRIDAY => 'dayweek.3dig.' . Constants::FRIDAY, Constants::SATURDAY => 'dayweek.3dig.' . Constants::SATURDAY, Constants::SUNDAY => 'dayweek.3dig.' . Constants::SUNDAY), 'multiple' => true, 'expanded' => true));
        $builder->add('status', 'choice', array('choices' => array(Session::STATUS_ENABLED => 'entity.session.field.status.' . Session::STATUS_ENABLED, Session::STATUS_LOCKED => 'entity.session.field.status.' . Session::STATUS_LOCKED), 'help' => 'form.session.field.status.help'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'organization' => null);
    }
    
    public function getName()
    {
        return 'session_generator';
    }
}
