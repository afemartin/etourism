<?php

namespace PFCD\TourismBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilder;

class LocaleListTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('localelist', $options['localelist']);
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('localelist', $form->getAttribute('localelist'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('localelist' => null);
    }

    public function getExtendedType()
    {
        return 'field';
    }
}

?>
