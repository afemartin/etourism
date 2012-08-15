<?php

namespace PFCD\TourismBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilder;

class CountryListTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('countrylist', $options['countrylist']);
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('countrylist', $form->getAttribute('countrylist'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('countrylist' => null);
    }

    public function getExtendedType()
    {
        return 'field';
    }
}

?>
