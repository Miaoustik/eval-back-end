<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email :'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe :'
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Recruteur' => "ROLE_RECRUTEUR",
                    'Candidat' => "ROLE_CANDIDAT",
                ],
                'expanded' => true,
                'label' => 'Vous êtes :',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'CREATE',
            'translation_domain' => 'form',
        ]);
    }
}
