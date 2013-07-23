<?php

namespace ad\ExtraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array('label' => 'Votre fichier :'))
            ->add('categoryId', null, array('label' => 'Choisir une catégorie :'))
            ->add('Envoyer', 'submit');
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ad\ExtraBundle\Entity\File'
        ));
    }

    public function getName()
    {
        return 'ad_extrabundle_filetype';
    }
}
