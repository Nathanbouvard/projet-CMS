<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Theme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom du thème')
                ->setHelp('Ex: Dark Mode, Océan, Minimaliste...'),

            ColorField::new('backgroundColor', 'Couleur de fond'),

            ColorField::new('primaryColor', 'Couleur principale')
                ->setHelp('Sera utilisé pour les titres, les boutons et les bordures'),

            ChoiceField::new('fontFamily', 'Typographie')
                ->setChoices([
                    'Moderne (Sans-Serif)' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    'Classique (Serif)' => 'Georgia, "Times New Roman", Times, serif',
                    'Tech / Code (Monospace)' => '"Courier New", Courier, monospace',
                    'Impact / Fort' => 'Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif',
                    'Ludique' => '"Comic Sans MS", "Comic Sans", cursive',
                ])
                ->renderAsNativeWidget(),
            
            ChoiceField::new('imageSize', 'Taille des images')
                ->setChoices([
                    'Petite' => 'small',
                    'Moyenne' => 'medium',
                    'Grande' => 'large',
                ])
                ->renderAsNativeWidget()
                ->setHelp('Choisissez la taille d\'affichage des images dans les articles.'),

            ChoiceField::new('fontSize', 'Taille de la police')
                ->setChoices([
                    'Petite' => 'small',
                    'Moyenne' => 'medium',
                    'Grande' => 'large',
                ])
                ->renderAsNativeWidget()
                ->setHelp('Choisissez la taille de la police pour le contenu des articles.'),
        ];
    }
}