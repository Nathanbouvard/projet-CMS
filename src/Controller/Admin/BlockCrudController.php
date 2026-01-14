<?php

namespace App\Controller\Admin;

use App\Entity\Block;
use App\Field\VizConfigField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

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
                    'Titre' => 'title',
                    'Image' => 'image',
                    'Visualisation (Graphique)' => 'viz',
                ])
                ->renderAsNativeWidget(),

            TextareaField::new('content', 'Contenu (Texte/Titre)')
                ->setHelp('Pour les blocs Texte ou Titre uniquement'),

            AssociationField::new('media', 'Image')
                ->setHelp('Pour les blocs Image uniquement')
                ->autocomplete(),

            VizConfigField::new('vizConfig', 'Paramètres Graphique')
                ->onlyOnForms(), 

            HiddenField::new('position'),
        ];
    }
}