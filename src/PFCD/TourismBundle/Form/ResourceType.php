<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Resource;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('type', 'choice', array('choices' => array(Resource::TYPE_MATERIAL_INT => 'entity.resource.field.type.' . Resource::TYPE_MATERIAL_INT, Resource::TYPE_HUMAN_INT => 'entity.resource.field.type.' . Resource::TYPE_HUMAN_INT, Resource::TYPE_MATERIAL_EXT => 'entity.resource.field.type.' . Resource::TYPE_MATERIAL_EXT, Resource::TYPE_HUMAN_EXT => 'entity.resource.field.type.' . Resource::TYPE_HUMAN_EXT, Resource::TYPE_UNKNOWN => 'entity.resource.field.type.' . Resource::TYPE_UNKNOWN)));
        $builder->add('name', 'text', array('attr' => array('class' => 'input-xlarge'), 'help' => 'form.resource.field.name.help'));
        $builder->add('conflict', 'checkbox', array('required' => false, 'help' => 'form.resource.field.conflict.help'));
        $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.resource.field.note.help'));
        $builder->add('status', 'choice', array('choices' => array(Resource::STATUS_ENABLED => 'entity.resource.field.status.' . Resource::STATUS_ENABLED, Resource::STATUS_LOCKED => 'entity.resource.field.status.' . Resource::STATUS_LOCKED, Resource::STATUS_DELETED => 'entity.resource.field.status.' . Resource::STATUS_DELETED), 'help' => 'form.resource.field.status.help'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE);
    }
    
    public function getName()
    {
        return 'resource';
    }
}
