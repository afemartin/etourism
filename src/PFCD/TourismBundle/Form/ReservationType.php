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
        if ($options['domain'] == Constants::ADMIN && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('activity', 'entity', array('class' => 'PFCDTourismBundle:Activity', 'property' => 'title', 'property_path' => false, 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['organization'] != null && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('activity', 'entity', array('class' => 'PFCDTourismBundle:Activity', 'property' => 'title', 'property_path' => false, 'empty_value' => 'Select an activity',
                'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('a')
                                ->where('a.organization = :organization')
                                ->orderBy('a.title', 'ASC')
                                ->setParameter('organization', $options['organization']);
                }));
        }
        $builder->add('persons', 'integer', array('attr' => array('class' => 'input-mini')));
        $builder->add('comment', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge')));
        $builder->add('session', 'hidden', array('property_path' => false));
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
