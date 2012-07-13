<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\News;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('organization', 'entity', array('class' => 'PFCDTourismBundle:Organization', 'property' => 'name', 'help' => 'form.activity.field.organization.help'));
        }
        $builder->add('title', 'text', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('shortDesc', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        $builder->add('fullDesc', 'textarea', array('required' => false, 'attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge')));
        if ($options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('file', 'file', array('required' => false, 'label' => 'Image', 'help' => 'form.activity.field.image.help'));
            $builder->add('status', 'choice', array('choices' => array(News::STATUS_PENDING => 'Pending', News::STATUS_ENABLED => 'Enabled', News::STATUS_LOCKED => 'Locked', News::STATUS_DELETED => 'Deleted')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_CREATE);
    }
    
    public function getName()
    {
        return 'news';
    }
}
