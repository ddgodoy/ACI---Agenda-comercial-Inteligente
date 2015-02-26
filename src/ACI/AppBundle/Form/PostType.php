<?php

namespace ACI\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page')
            ->add('title')
            ->add('slug')
            ->add('html', 'textarea', array(
                    'attr' => array(
                        'class' => 'tinymce',
                        'data-theme' => 'medium' // simple, advanced, bbcode, medium
                    )
            ))
            ->add('javascript')
            ->add('published')
            ->add('show_title')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACI\AppBundle\Entity\Post'
        ));
    }

    public function getName()
    {
        return 'prodi_cmsbundle_posttype';
    }
}
