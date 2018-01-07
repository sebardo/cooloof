<?php

namespace Blog\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostCommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'comment.name', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'comment.name')))
            ->add('comment', null, array('label' => 'comment.comment', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'comment.comment')))
            ->add('validated', null, array('label' => 'comment.validated'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\CoreBundle\Entity\PostComment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'postcomment';
    }
}
