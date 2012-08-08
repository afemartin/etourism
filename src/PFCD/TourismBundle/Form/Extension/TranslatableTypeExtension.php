<?php

namespace PFCD\TourismBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilder;

class TranslatableTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('translatable', $options['translatable']);
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('translatable', $form->getAttribute('translatable'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('translatable' => null);
    }

    public function getExtendedType()
    {
        return 'field';
    }
}

?>
