<?php

namespace App\Form;

use App\Entity\Dataset;
use App\Form\DTO\VizConfigDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VizConfigFormType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Note: Le nom du champ doit correspondre à la propriété du DTO
            ->add('dataset', EntityType::class, [
                'label' => 'Dataset Source',
                'class' => Dataset::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un dataset...',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de graphique',
                'choices' => [
                    'Barres' => 'bar',
                    'Ligne' => 'line',
                    'Camembert (Pie)' => 'pie',
                    'Nuage de points' => 'scatter',
                ],
            ])
            ->add('axeX', TextType::class, ['label' => 'Axe X (Colonne)'])
            ->add('axeY', TextType::class, ['label' => 'Axe Y (Colonne)'])
            ->add('couleur', ColorType::class, ['label' => 'Couleur'])
            ->add('titre', TextType::class, ['label' => 'Titre', 'required' => false]);

        // --- LE TRANSFORMER MAGIQUE ---
        // Il convertit Array (BDD) <-> DTO (Formulaire)
        $builder->addModelTransformer(new CallbackTransformer(
            // 1. De la BDD vers le Formulaire (Array -> Object)
            function ($dataAsArray) {
                $dto = new VizConfigDTO();
                if (!is_array($dataAsArray)) {
                    return $dto;
                }
                
                // On récupère le dataset via son ID
                if (!empty($dataAsArray['dataset_id'])) {
                    $dto->dataset = $this->em->getRepository(Dataset::class)->find($dataAsArray['dataset_id']);
                }
                
                $dto->type = $dataAsArray['type'] ?? null;
                $dto->axeX = $dataAsArray['axe_x'] ?? null;
                $dto->axeY = $dataAsArray['axe_y'] ?? null;
                $dto->couleur = $dataAsArray['couleur'] ?? '#3498db';
                $dto->titre = $dataAsArray['titre'] ?? '';
                
                return $dto;
            },
            
            // 2. Du Formulaire vers la BDD (Object -> Array)
            function ($dto) {
                if (!$dto instanceof VizConfigDTO) {
                    return [];
                }
                return $dto->toArray();
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VizConfigDTO::class,
        ]);
    }
}