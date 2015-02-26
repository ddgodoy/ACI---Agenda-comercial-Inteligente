<?php

namespace ACI\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('alias')               
            ->add('parent')
            ->add('externo','checkbox',array('label'=>'Es un link interno?', 'attr'=>array("class"=>"checker", 'style'=>'opacity:1; float: left; position: relative')))
            ->add('link')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACI\AppBundle\Entity\Menu'
        ));
    }

    public function getName()
    {
        return 'prodi_cmsbundle_menutype';
    }
}
