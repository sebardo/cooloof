<?php

namespace Blog\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatableFileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locale' => 'es'
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'translatable_file';
    }
}