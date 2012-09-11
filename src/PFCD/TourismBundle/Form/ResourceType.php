<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\ResourceCategory;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('category', 'entity', array('class' => 'PFCDTourismBundle:ResourceCategory', 'property' => 'name', 'empty_value' => 'Select a category'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('category', 'entity', array('class' => 'PFCDTourismBundle:ResourceCategory', 'property' => 'name', 'empty_value' => 'Select a category',
                'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('rc')
                                  ->where('rc.organization = :organization')
                                  ->andWhere('rc.status = :status')
                                  ->orderBy('rc.name', 'ASC')
                                  ->setParameter('organization', $options['organization'])
                                  ->setParameter('status', ResourceCategory::STATUS_ENABLED);
                }));
        }
        if ($options['type'] == Constants::FORM_CREATE)
        {
            $builder->add('name', 'text', array('attr' => array('class' => 'input-xlarge'), 'help' => 'form.resource.field.name.help'));
            $builder->add('conflict', 'checkbox', array('required' => false, 'help' => 'form.resource.field.conflict.help'));
        }
        $builder->add('dateStartLock', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('dateEndLock', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.resource.field.note.help'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE, 'organization' => null);
    }
    
    public function getName()
    {
        return 'resource';
    }
}
