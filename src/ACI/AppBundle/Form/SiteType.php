<?php

namespace ACI\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label'=>'Nombre', 'attr'=>array('class'=>'form-control')))
            ->add('type', 'choice', array('label'=>'Tipo', 'choices'=>array('site'=>'Sitio', "blog"=>"Blog", "store"=>"Tienda")))
            ->add('price', 'text', array('label'=>'Precio', 'attr'=>array('class'=>'form-control')))
            ->add('category');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACI\AppBundle\Entity\Site'
        ));
    }

    public function getName()
    {
        return 'apptibase_cmsbundle_sitetype';
    }
}
