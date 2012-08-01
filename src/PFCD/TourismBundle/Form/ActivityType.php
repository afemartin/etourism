<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Activity;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('title', 'text', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge')));
        $builder->add('price', 'money', array('required' => false, 'attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.price.help'));
        $builder->add('currency', 'choice', array('required' => false, 'empty_value' => 'Currency', 'choices' => array('EUR' => 'Euro'), 'attr' => array('class' => 'input-small')));
        $builder->add('capacity', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.capacity.help'));
        $builder->add('duration', 'integer', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.duration.help'));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            if ($options['organization'] != null)
            {
                $builder->add('resources', 'entity', array('class' => 'PFCDTourismBundle:Resource', 'property' => 'name', 'multiple' => true, 'expanded' => true,
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('r')
                                ->where('r.organization = :organization')
                                ->orderBy('r.name', 'ASC')
                                ->setParameter('organization', $options['organization']);
                    }));
            }
            $builder->add('file', 'file', array('required' => false, 'label' => 'Image', 'help' => 'form.activity.field.image.help'));
            $builder->add('status', 'choice', array('choices' => array(Activity::STATUS_PENDING => 'Pending', Activity::STATUS_ENABLED => 'Enabled', Activity::STATUS_LOCKED => 'Locked', Activity::STATUS_DELETED => 'Deleted')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'organization' => null);
    }
    
    public function getName()
    {
        return 'activity';
    }
}
