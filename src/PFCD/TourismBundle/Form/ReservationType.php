<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Reservation;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        if ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK)
        {
            $builder->add('user', 'entity', array('class' => 'PFCDTourismBundle:User', 'property' => 'fullname'));
        }
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('activity', 'entity', array('class' => 'PFCDTourismBundle:Activity', 'property' => 'title'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['organization'] != null)
        {
            $builder->add('activity', 'entity', array('class' => 'PFCDTourismBundle:Activity', 'property' => 'title',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                              ->where('a.organization = :organization')
                              ->orderBy('a.title', 'ASC')
                              ->setParameter('organization', $options['organization']);
                }));
        }
        $builder->add('adults', 'integer', array('attr' => array('class' => 'input-mini')));
        $builder->add('childrens', 'integer', array('required' => false, 'attr' => array('class' => 'input-mini')));
        $builder->add('date', 'date', array('required' => false,  'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('time', 'time', array('required' => false, 'hours' => range(23, 0), 'minutes' => range(60-$step, 0, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact')));
        if ($options['type'] == Constants::FORM_UPDATE && ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK))
        {
            $builder->add('status', 'choice', array('choices' => array(Reservation::STATUS_REQUESTED => 'Requested', Reservation::STATUS_ACCEPTED => 'Accepted', Reservation::STATUS_REJECTED => 'Rejected', Reservation::STATUS_CANCELED => 'Canceled')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'validation_groups' => 'Default', 'organization' => null);
    }
    
    public function getName()
    {
        return 'reservation';
    }
}
