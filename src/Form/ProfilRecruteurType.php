<?php

namespace App\Form;

use App\Entity\ProfilRecruteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilRecruteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('societyName', null, [
                'label' => "Nom de l'entreprise :"
            ])
            ->add('address', null, [
                'label' => "Adresse :"
            ])
            ->add('postalCode', null, [
                'label' => "Code postal :"
            ])
            ->add('city', null, [
                'label' => "Ville :"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilRecruteur::class,
            'translation_domain' => 'form',
        ]);
    }
}
