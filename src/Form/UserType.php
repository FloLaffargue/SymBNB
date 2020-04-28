<?php

namespace App\Form;

use App\Entity\User;
use App\Form\RoleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration('Prénom', 'Entrez votre prénom'))
            ->add('lastName', TextType::class, $this->getConfiguration('Nom', 'Votre nom'))
            ->add('email')
            ->add('picture', TextType::class, $this->getConfiguration('Photo de profil', 'Url de la photo'))
            ->add('introduction')
            ->add('description')
            ->add('userRoles', CollectionType::class,
                [
                    'entry_type' => RoleType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ] 
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
