<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

class ResourceCategoryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name'));
        }
        $builder->add('name', 'text', array('attr' => array('class' => 'input-xlarge'), 'help' => 'form.resourcecategory.field.name.help'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK);
    }
    
    public function getName()
    {
        return 'category';
    }
}
