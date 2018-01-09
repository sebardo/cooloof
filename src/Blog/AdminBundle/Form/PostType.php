<?php

namespace Blog\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class PostType extends AbstractType
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();
        $post = $builder->getData();

        $builder
            ->add('date', null, array(
                'years' => range(date('Y'), date('Y') + 5),
                'label' => 'post.publish_date',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => 'date-widget'),
                'data' => $post->getId() ? $post->getDate() : new \DateTime()))
            ->add('active', null, array('label' => 'labels.active'))
            ->add('images', 'file', array('mapped' => false, 'multiple' => true, 'required' => false))
            ->add('image', 'file', array('mapped' => false, 'multiple' => false, 'required' => false))
            ->add('video', 'file', array('mapped' => false, 'multiple' => false, 'required' => false))
            ->add('editId', 'hidden')
            ->add('week', 'entity', array(
                'class' => 'CoreBundle:Week',
                'required' => true,
                'label' => 'post.week',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->findWeeksByUser($user);
                },
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => 'form-control')
            ))
            ->add('translations', 'a2lix_translations_gedmo', array(
                'required' => true,
                'label' => false,
                'translatable_class' => 'Blog\CoreBundle\Entity\Post',
                'fields' => array(
                    'title' => array('required' => true, 'label' => 'post.title', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'post.title')),
                    'slug' => array('required' => false, 'label'=> 'post.slug_description', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'post.url')),
                    'introduction' => array('required' => false, 'label'=> 'post.introduction_text', 'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => 'form-control', 'placeholder' => 'post.introduction_text')),
                    'content' => array('required' => false, 'label'=> 'post.description',
                        'locale_options' => array(
                            'es' => array(
                                'attr' => array('class' => 'summernote-es form-control', 'placeholder' => 'post.description')
                            ),
                            'ca' => array(
                                'attr' => array('class' => 'summernote-ca form-control', 'placeholder' => 'post.description')
                            )
                        ),
                        'label_attr' => array('class' => 'control-label')
                    )
                )
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\CoreBundle\Entity\Post'
        ));
    }

    public function getName()
    {
        return '';
    }
}
