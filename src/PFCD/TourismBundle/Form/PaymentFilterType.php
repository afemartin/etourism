<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Payment;

class PaymentFilterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['domain'] == Constants::ADMIN)
        {
            $builder->add('activity', 'entity', array('required' => false, 'class' => 'PFCDTourismBundle:Activity', 'property' => 'title', 'empty_value' => 'Select an activity'));
        }
        elseif ($options['domain'] == Constants::BACK && $options['organization'] != null)
        {
            $builder->add('activity', 'entity', array('required' => false, 'class' => 'PFCDTourismBundle:Activity', 'property' => 'title', 'empty_value' => 'Select an activity',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('a')
                            ->where('a.organization = :organization')
                            ->orderBy('a.title', 'ASC')
                            ->setParameter('organization', $options['organization']);
                }));
        }

        $builder->add('dateStart', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('dateEnd', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('sessionDateStart', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('sessionDateEnd', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'input-small datepicker-bootstrap')));
        $builder->add('status', 'choice', array('choices' => array(Payment::STATUS_PENDING_P => 'Pending payment', Payment::STATUS_PAID => 'Paid', Payment::STATUS_PENDING_R => 'Pending refund', Payment::STATUS_REFUNDED => 'Refunded'), 'multiple' => true, 'expanded' => true));
    }

    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::BACK, 'organization' => null);
    }
    
    public function getName()
    {
        return 'payment_filter';
    }
}
