<?php

namespace PFCD\TourismBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Comment;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('comment', 'textarea', array('attr' => array('class' => 'input-xxlarge')));
        if (($options['domain'] == Constants::ADMIN || $options['domain'] == Constants::BACK) && $options['type'] == Constants::FORM_UPDATE)
        {
            $builder->add('status', 'choice', array('choices' => array(Comment::STATUS_ENABLED => 'entity.comment.field.status.' . Comment::STATUS_ENABLED, Comment::STATUS_DELETED => 'entity.comment.field.status.' . Comment::STATUS_DELETED)));
        }
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('domain' => Constants::FRONT, 'type' => Constants::FORM_CREATE);
    }
    
    public function getName()
    {
        return 'comment';
    }
}
