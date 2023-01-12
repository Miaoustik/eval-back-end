<?php

namespace App\Form;

use App\Entity\ProfilCandidat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilCandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null, [
                'label' => 'Prénom :'
            ])
            ->add('lastname', null, [
                'label' => 'Nom de famille :'
            ])
            ->add('cvFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        "mimeTypes" => [
                            'application/pdf',
                            'application/x-pdf',
                        ]
                    ])
                ],
                'help' => 'Formats pdf uniquement.',
                'label' => 'Cv :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilCandidat::class,
            'translation_domain' => 'form',
        ]);
    }
}
