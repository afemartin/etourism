<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Resource;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('title', 'text', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('price', 'money', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.price.help'));
        $builder->add('currency', 'choice', array('choices' => $options['supported_currencies'], 'empty_value' => 'Select a currency', 'attr' => array('class' => 'input-medium')));
        $builder->add('capacity', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.capacity.help'));
        $builder->add('duration', 'time', array('required' => false, 'hours' => range(12, 0), 'minutes' => range(60-$step, 0, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact'), 'help' => 'form.activity.field.duration.help'));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            if ($options['organization'] != null)
            {
                $builder->add('resources', 'entity', array('class' => 'PFCDTourismBundle:Resource', 'property' => 'name', 'multiple' => true, 'expanded' => true,
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('r')
                                ->where('r.organization = :organization')
                                ->setParameter('organization', $options['organization'])
                                ->andWhere('r.status IN (:status)')
                                ->setParameter('status', array(Resource::STATUS_ENABLED, Resource::STATUS_LOCKED))
                                ->orderBy('r.name', 'ASC');
                    }));
            }
            $builder->add('languages', 'choice', array('attr' => array('style' => 'display: inline-block'), 'choices' => $options['supported_languages'], 'multiple' => true, 'expanded' => true, 'localelist' => true, 'help' => 'form.activity.field.languages.help'));
            $builder->add('file', 'file', array('required' => false, 'label' => 'Image', 'help' => 'form.activity.field.image.help'));
            $builder->add('status', 'choice', array('choices' => array(Activity::STATUS_PENDING => 'entity.activity.field.status.' . Activity::STATUS_PENDING, Activity::STATUS_ENABLED => 'entity.activity.field.status.' . Activity::STATUS_ENABLED, Activity::STATUS_LOCKED => 'entity.activity.field.status.' . Activity::STATUS_LOCKED, Activity::STATUS_DELETED => 'entity.activity.field.status.' . Activity::STATUS_DELETED), 'help' => 'form.activity.field.status.help'));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'organization' => null, 'language' => 'en', 'supported_languages' => array(), 'supported_currencies' => array());
    }
    
    public function getName()
    {
        return 'activity';
    }
}
