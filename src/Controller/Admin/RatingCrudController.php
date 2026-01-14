<?php

namespace App\Controller\Admin;

use App\Entity\Rating;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RatingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rating::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avis / Note')
            ->setEntityLabelInPlural('Avis & Notes')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('pseudo', 'Auteur'),

            ChoiceField::new('rating', 'Note')
                ->setChoices([
                    '5 - Excellent' => 5,
                    '4 - Très bien' => 4,
                    '3 - Bien' => 3,
                    '2 - Moyen' => 2,
                    '1 - Mauvais' => 1,
                ])
                ->renderAsNativeWidget(),

            TextareaField::new('message', 'Commentaire'),

            AssociationField::new('article', 'Sur l\'article'),

            DateTimeField::new('createdAt', 'Date')->hideOnForm(),
        ];
    }
}