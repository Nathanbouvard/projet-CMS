<?php

namespace App\Controller\Admin;

use App\Entity\Block;
// Removed: use App\Field\VizConfigField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
// Removed: use EasyCorp\Bundle\EasyAdminBundle->Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class BlockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Block::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bloc')  
            ->setEntityLabelInPlural('Blocs')   
            ->setPageTitle('index', 'Gestion des Blocs'); 
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('type')
                ->setChoices([
                    'Texte' => 'text',
                    'Image' => 'image',
                    'Graphique (CSV)' => 'chart',
                ])
                ->renderAsNativeWidget(),

            TextareaField::new('content', 'Contenu')
                ->setHelp('Saisissez le contenu textuel ici.'),

            AssociationField::new('media', 'Média')
                ->setHelp('Sélectionnez un média (image ou fichier CSV).')
                ->autocomplete(),

            HiddenField::new('position'),
        ];
    }
}