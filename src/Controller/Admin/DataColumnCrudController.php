<?php

namespace App\Controller\Admin;

use App\Entity\DataColumn;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DataColumnCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DataColumn::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Variable / Colonne')
            ->setEntityLabelInPlural('Variables / Colonnes')
            ->setPageTitle('index', 'Gestion des Variables');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Le Dataset auquel cette colonne appartient (Lecture seule en édition pour éviter les bêtises)
            AssociationField::new('dataset', 'Jeu de données')
                ->setRequired(true)
                ->hideWhenUpdating(), 

            // Le nom de la colonne (tel qu'il est dans le CSV)
            TextField::new('name', 'Nom de la variable')
                ->setHelp('Nom identifié dans l\'en-tête du fichier CSV'),

            // LE PLUS IMPORTANT : Le choix du type
            ChoiceField::new('type', 'Type de donnée')
                ->setChoices([
                    'Numérique (Chiffres, Prix...)' => 'numeric',
                    'Catégorielle (Texte, Ville, Date...)' => 'categorical',
                ])
                ->renderAsBadges([
                    'numeric' => 'info',      
                    'categorical' => 'warning'
                ])
                ->setHelp('Si le graphique ne s\'affiche pas, vérifiez que le type est correct ici.'),
        ];
    }
}