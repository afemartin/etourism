<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Reservation;
use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Resource;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('activity', 'entity', array('attr' => array('class' => 'input-xlarge'), 'class' => 'PFCDTourismBundle:Activity', 'property' => 'titleAndStatus', 'property_path' => false, 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['type'] == Constants::FORM_CREATE)
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
        if ($options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('persons', 'integer', array('attr' => array('class' => 'input-mini')));
        }
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('resources', 'entity', array('attr' => array('style' => 'display: inline-block'), 'class' => 'PFCDTourismBundle:Resource', 'property' => 'nameAndCategory', 'multiple' => true, 'expanded' => true, 'help' => 'form.reservation.field.resources.help',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('r')
                            ->innerJoin('r.category', 'c')
                            ->innerJoin('c.activities', 'a')
                            ->where('a.id = :activity')
                            ->setParameter('activity', $options['activity'])
                            ->andWhere('r.status = :status')
                            ->setParameter('status', Resource::STATUS_ENABLED)
                            ->andWhere('((r.dateStartLock IS     null AND r.dateEndLock IS     null) OR
                                         (r.dateStartLock IS     null AND r.dateEndLock IS NOT null AND r.dateEndLock <= :session_start) OR
                                         (r.dateStartLock IS NOT null AND r.dateEndLock IS     null AND r.dateStartLock >= :session_end) OR
                                         (r.dateStartLock IS NOT null AND r.dateEndLock IS NOT null AND (r.dateEndLock <= :session_start OR r.dateStartLock >= :session_end)))')
                            ->setParameter('session_start', $options['session_start'])
                            ->setParameter('session_end', $options['session_end'])
                            ->orderBy('c.name', 'ASC')
                            ->addOrderBy('r.name', 'ASC');
                   }));
        }
        if ($options['domain'] == Constants::FRONT)
        {
            $builder->add('comment', 'textarea', array('required' => false, 'property_path' => false, 'attr' => array('class' => 'input-xxlarge')));
        }
        else
        {
            $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.reservation.field.note.help'));
        }
        if ($options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('session', 'hidden', array('property_path' => false));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'session_start' => null, 'session_end' => null, 'validation_groups' => 'Default', 'organization' => null, 'activity' => null);
    }
    
    public function getName()
    {
        return 'reservation';
    }
}
