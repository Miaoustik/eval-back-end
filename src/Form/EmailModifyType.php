<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mail', RepeatedType::class, [
                'type' => EmailType::class,
                'attr' => [
                    'autocomplete' => 'off'
                ],
                'first_options' => ['label' => 'Nouvelle adresse mail :'],
                'second_options' => ['label' => 'Confirmer l\'email :'],
                'label' => 'Email :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'form',
        ]);
    }
}
