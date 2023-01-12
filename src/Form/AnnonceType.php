<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Intitulé du poste :'
            ])
            ->add('description', null, [
                'label' => 'Description du poste :'
            ])
            ->add('salary', null, [
                'label' => 'Salaire :'
            ])
            ->add('hours', null, [
                'label' => 'Heures :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
            'translation_domain' => 'form',
        ]);
    }
}
