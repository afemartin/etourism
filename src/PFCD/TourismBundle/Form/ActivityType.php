<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Category;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $step = 5; // 5 minutes step for time inputs
        
        $builder->add('title', 'text', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        if ($options['type'] == Constants::FORM_CREATE || $options['status'] == Activity::STATUS_PENDING)
        {
            $builder->add('price', 'money', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.price.help'));
            $builder->add('currency', 'choice', array('choices' => $options['supported_currencies'], 'empty_value' => 'Select a currency', 'attr' => array('class' => 'input-medium')));
            $builder->add('capacity', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.capacity.help'));
            $builder->add('durationDays', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.durationdays.help'));
            $builder->add('durationTime', 'time', array('hours' => range(0, 23), 'minutes' => range(0, 60-$step, $step), 'empty_value' => array('hour' => 'Hour', 'minute' => 'Minute' ), 'attr' => array('class' => 'time-choice-compact'), 'help' => 'form.activity.field.durationtime.help'));
            
            if ($options['organization'])
            {
                $builder->add('categories', 'entity', array('class' => 'PFCDTourismBundle:Category', 'property' => 'name', 'attr' => array('style' => 'display: inline-block'), 'multiple' => true, 'expanded' => true, 'help' => 'form.activity.field.categories.help',
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('c')
                                  ->where('c.organization = :organization')
                                  ->andWhere('c.status = :status')
                                  ->orderBy('c.name', 'ASC')
                                  ->setParameter('organization', $options['organization'])
                                  ->setParameter('status', Category::STATUS_ENABLED);
                    }));
            }
        }
        $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.activity.field.note.help'));
        $builder->add('languages', 'choice', array('attr' => array('style' => 'display: inline-block'), 'choices' => $options['supported_languages'], 'multiple' => true, 'expanded' => true, 'localelist' => true, 'help' => 'form.activity.field.languages.help'));
        $builder->add('file', 'file', array('required' => false, 'label' => 'Image', 'help' => 'form.activity.field.image.help'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'status' => Activity::STATUS_ENABLED, 'organization' => null, 'language' => 'en', 'supported_languages' => array(), 'supported_currencies' => array());
    }
    
    public function getName()
    {
        return 'activity';
    }
}
