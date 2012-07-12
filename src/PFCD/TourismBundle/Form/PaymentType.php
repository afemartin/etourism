<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Payment;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK)
        {
            $builder->add('price', 'money', array('attr' => array('class' => 'input-mini'), 'help' => 'form.activity.field.price.help'));
            $builder->add('currency', 'choice', array('empty_value' => 'Currency', 'choices' => array('EUR' => 'Euro'), 'attr' => array('class' => 'input-small')));
        }
        $builder->add('type', 'choice', array('choices' => array(Payment::TYPE_BANK_TRANSFER => 'Bank Transfer', Payment::TYPE_CREDIT_CARD => 'Credit Card', Payment::TYPE_PAYPAL => 'Paypal', Payment::TYPE_CASH => 'Cash'), 'empty_value' => 'Select a payment method'));
        $builder->add('comment', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge')));
        if ($options['type'] == Constants::FORM_UPDATE && ($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK))
        {
            $builder->add('status', 'choice', array('choices' => array(Payment::STATUS_PENDING_P => 'Pending payment', Payment::STATUS_PAID => 'Paid', Payment::STATUS_PENDING_R => 'Pending refund', Payment::STATUS_REFUNDED => 'Refunded')));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_UPDATE);
    }
    
    public function getName()
    {
        return 'payment';
    }
}
