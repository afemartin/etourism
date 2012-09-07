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
            $builder->add('price', 'money', array('attr' => array('class' => 'input-mini'), 'help' => 'form.payment.field.price.help'));
            $builder->add('currency', 'choice', array('choices' => $options['supported_currencies'], 'empty_value' => 'Select a currency', 'attr' => array('class' => 'input-medium')));
        }
        $builder->add('type', 'choice', array('choices' => array(Payment::TYPE_BANK_TRANSFER => 'entity.payment.field.type.' . Payment::TYPE_BANK_TRANSFER, Payment::TYPE_CASH => 'entity.payment.field.type.' . Payment::TYPE_CASH), 'empty_value' => 'Select a payment method'));
        if ($options['domain'] == Constants::FRONT)
        {
            $builder->add('comment', 'textarea', array('required' => false, 'property_path' => false, 'attr' => array('class' => 'input-xxlarge')));
        }
        else
        {
            $builder->add('note', 'textarea', array('required' => false, 'attr' => array('class' => 'input-xxlarge'), 'help' => 'form.payment.field.note.help'));
            $builder->add('status', 'choice', array('choices' => array(Payment::STATUS_PENDING_P => 'entity.payment.field.status.' . Payment::STATUS_PENDING_P, Payment::STATUS_PAID => 'entity.payment.field.status.' . Payment::STATUS_PAID, Payment::STATUS_PENDING_R => 'entity.payment.field.status.' . Payment::STATUS_PENDING_R, Payment::STATUS_REFUNDED => 'entity.payment.field.status.' . Payment::STATUS_PENDING_R), 'help' => 'form.payment.field.status.help'));
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'type' => Constants::FORM_UPDATE, 'supported_currencies' => array());
    }
    
    public function getName()
    {
        return 'payment';
    }
}
