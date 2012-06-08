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
        $builder->add('date', 'date' , array('required' => false, 'widget' => 'choice', 'years' => range(date('Y') + 1, date('Y')), 'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'), 'attr' => array('class' => 'date-choice-compact')));
        $builder->add('time', 'time', array('required' => false, 'attr' => array('class' => 'time-choice-compact')));
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
