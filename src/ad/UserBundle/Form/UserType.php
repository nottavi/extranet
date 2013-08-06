<?php

namespace ad\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'Login :'))
            ->add('email', 'email', array('label' => 'Email :'))
            ->add('plainPassword', 'repeated', array(
												     'type' => 'password',
												     'invalid_message' => 'Les deux mots de passes doivent être identiques',
												     'options' => array('attr' => array('class' => 'password-field')),
												     'required' => true,
												     'first_options'  => array('label' => 'Mot de passe :'),
												     'second_options' => array('label' => ' ')))
            ->add('roles', 'choice', array('mapped' => false,
            							   'choices' => array('ROLE_PARTENAIRE' => 'Utilisateur', 'ROLE_ADMIN' => 'Admin')))
            ->add('Envoyer', 'submit');
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ad\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'ad_userbundle_usertype';
    }
}
