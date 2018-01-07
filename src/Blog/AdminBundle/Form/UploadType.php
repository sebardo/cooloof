<?php

namespace Blog\AdminBundle\Form;

use Blog\AdminBundle\Form\Type\TranslatableFileType;
use Blog\CoreBundle\Entity\Upload;
use Blog\CoreBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $user = $options['user'];
        $attr = array();
        if ($entity->getType() == Upload::TYPE_ATTACHMENT) {
            $attr = array('disabled' => 'disabled');
        }

        $builder
            ->add('type', 'choice',
                array('label' => 'upload.type',
                    'choices'   => $entity->getChoices(),
                    'required' => true,
                    'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control')))
            ->add('center', 'entity', array(
                'class' => 'CoreBundle:Center',
                'required' => true,
                'label' => 'week.center',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->findCentersOrderedByName($user);
                },
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => 'form-control')
            ))
            ->add('order', null, array('label' => 'labels.order', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control')))
            ->add('translations', 'a2lix_translations_gedmo', array(
                'required' => true,
                'label' => false,
                'translatable_class' => 'Blog\CoreBundle\Entity\Upload',
                'fields' => array(
                    'name' => array('required' => true, 'label' => 'labels.name', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'labels.name')),
                    'link' => array('required' => false, 'label' => 'upload.link', 'label_attr' => array('class' => 'control-label'), 'attr' => array_merge(array('class' => 'form-control', 'placeholder' => 'upload.link'), $attr)),
                    'file' => array('field_type' => 'text', 'required' => true, 'label'=> 'upload.file', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'post.url', 'style' => 'display: none')),
                )
            ))
            ->add('editId', 'hidden')
        ;
    }

    public function getName()
    {
        return '';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\CoreBundle\Entity\Upload',
            'user' => null
        ));
    }
}

