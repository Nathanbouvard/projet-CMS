<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Votre Pseudo',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom...']
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '⭐⭐⭐⭐⭐ Excellent' => 5,
                    '⭐⭐⭐⭐ Très bien' => 4,
                    '⭐⭐⭐ Bien' => 3,
                    '⭐⭐ Moyen' => 2,
                    '⭐ Mauvais' => 1,
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre avis',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Qu\'avez-vous pensé de cet article ?']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer mon avis',
                'attr' => ['class' => 'btn btn-primary mt-3 w-100']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}