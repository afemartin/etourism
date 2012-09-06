<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Reservation;
use PFCD\TourismBundle\Entity\Activity;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'property_path' => false, 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['organization'] != null && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'property_path' => false, 'empty_value' => 'Select an activity',
                'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('a')
                                  ->where('a.organization = :organization')
                                  ->andWhere('a.status IN (:status)')
                                  ->orderBy('a.title', 'ASC')
                                  ->setParameter('organization', $options['organization'])
                                  ->setParameter('status', array(Activity::STATUS_ENABLED, Activity::STATUS_LOCKED));
                }));
        }
        if (($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK) && $options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('persons', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.reservation.field.persons.help'));
        }
        else
        {
            $builder->add('persons', 'integer', array('attr' => array('class' => 'input-mini')));
        }
        if ($options['domain'] == Constants::FRONT)
        {
            $builder->add('comment', 'textarea', array('required' => false, 'property_path' => false, 'attr' => array('class' => 'input-xxlarge')));
        }
        else
        {
            $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.reservation.field.note.help'));
        }
        $builder->add('session', 'hidden', array('property_path' => false));
        if ($options['type'] == Constants::FORM_UPDATE && ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK))
        {
            $builder->add('status', 'choice', array('choices' => array(Reservation::STATUS_REQUESTED => 'entity.reservation.field.status.' . Reservation::STATUS_REQUESTED, Reservation::STATUS_ACCEPTED => 'entity.reservation.field.status.' . Reservation::STATUS_ACCEPTED, Reservation::STATUS_REJECTED => 'entity.reservation.field.status.' . Reservation::STATUS_REJECTED, Reservation::STATUS_CANCELED => 'entity.reservation.field.status.' . Reservation::STATUS_CANCELED), 'help' => 'form.reservation.field.status.help'));
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
