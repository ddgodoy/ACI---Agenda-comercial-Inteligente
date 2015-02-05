<?php

namespace ACI\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PluginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label'=>'Nombre', 'attr'=>array('class'=>'form-control')))
            ->add('premium', 'choice', array('label'=>'Plan', 'choices'=>array("FREE"=>"FREE", "PERSONAL"=>"PERSONAL", "EMPRESA"=>"EMPRESA"), 'attr'=>array('class'=>'form-control')))
            ->add('bundle','text', array('label'=>'Bundle', 'attr'=>array('class'=>'form-control')))
            ->add('type', 'choice', array('label'=>'Tipo', 'choices'=>array("site"=>"Sitio", "blog"=>"Blog", "store"=>"Tienda", 'all'=>"Todos"), 'attr'=>array('class'=>'form-control')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACI\FrontendBundle\Entity\Plugin'
        ));
    }

    public function getName()
    {
        return 'apptibase_cmsbundle_plugintype';
    }
}
