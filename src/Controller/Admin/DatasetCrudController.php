<?php

namespace App\Controller\Admin;

use App\Entity\Dataset;
use App\Service\CsvAnalyzer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class DatasetCrudController extends AbstractCrudController
{
    public function __construct(
        private CsvAnalyzer $csvAnalyzer,
        private SluggerInterface $slugger
    ) {}

    public static function getEntityFqcn(): string
    {
        return Dataset::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom du jeu de données'),

            // On utilise 'filename' car d'après ton dump, c'est là que EasyAdmin met le fichier
            TextField::new('filename', 'Fichier CSV')
                ->setFormType(FileType::class)
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('required', $pageName === 'new')
                ->onlyOnForms(),

            TextField::new('filename', 'Nom du fichier')->onlyOnIndex(),
            AssociationField::new('provider')->hideOnForm(),
            DateTimeField::new('uploadedAt')->hideOnForm(),
            AssociationField::new('dataColumns', 'Variables détectées')->onlyOnDetail(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Dataset) return;

        $entityInstance->setProvider($this->getUser());

        // 1. Récupération de tous les fichiers
        $request = $this->getContext()->getRequest();
        $allFiles = $request->files->all();

        // 2. Recherche INTELLIGENTE du fichier (peu importe où il est caché)
        $uploadedFile = $this->findUploadedFileRecursively($allFiles);

        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.csv';

            try {
                // Déplacement vers public/uploads/csv
                $uploadedFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/csv',
                    $newFilename
                );
                
                $entityInstance->setFilename($newFilename);

            } catch (\Exception $e) {
                throw new \RuntimeException('Erreur upload : ' . $e->getMessage());
            }
        } else {
            // Si on crée un nouveau dataset sans fichier, on bloque
            if ($entityInstance->getId() === null) {
                 throw new \RuntimeException('Aucun fichier trouvé. Vérifiez que vous avez bien sélectionné un CSV.');
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
        
        // 3. Analyse du CSV
        $this->csvAnalyzer->analyze($entityInstance);
    }

    // --- Fonction magique pour trouver le fichier n'importe où ---
    private function findUploadedFileRecursively(array $files): ?UploadedFile
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                return $file;
            }
            if (is_array($file)) {
                $found = $this->findUploadedFileRecursively($file);
                if ($found) return $found;
            }
        }
        return null;
    }
}