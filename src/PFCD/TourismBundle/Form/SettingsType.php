<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Entity\Settings;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('homeDesc', 'textarea', array('attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('aboutDesc', 'textarea', array('attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('userLegal', 'textarea', array('attr' => array('class' => 'wysihtml5-bootstrap input-xxlarge'), 'translatable' => $options['language']));
        $builder->add('enabled', 'checkbox', array('help' => 'form.settings.field.enabled.help'));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('language' => 'en');
    }

    public function getName()
    {
        return 'settings';
    }
}

?>
