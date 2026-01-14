<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaCrudController extends AbstractCrudController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('uploadFile', 'Fichier Image')
                ->setFormType(FileType::class)
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('required', $pageName === 'new')
                ->onlyOnForms(),

            ImageField::new('filename', 'Aperçu')
                ->setBasePath('uploads/media') // Chemin relatif pour l'affichage HTML
                ->onlyOnIndex(),

            TextField::new('altText', 'Texte alternatif (Alt)')
                ->setHelp('Description de l\'image pour l\'accessibilité'),
            
            // On affiche le nom du fichier en texte simple au cas où
            TextField::new('filename', 'Nom du fichier')->onlyOnDetail(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    // Gestion de l'upload (identique à Dataset mais vers uploads/media)
    private function handleUpload($entityInstance): void
    {
        if (!$entityInstance instanceof Media) return;

        $request = $this->getContext()->getRequest();
        $allFiles = $request->files->all();
        
        $uploadedFile = $this->findUploadedFileRecursively($allFiles);

        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            // On ajoute une extension explicite .jpg/.png etc.
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            try {
                $uploadedFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/media',
                    $newFilename
                );
                
                $entityInstance->setFilename($newFilename);

            } catch (\Exception $e) {
                throw new \RuntimeException('Erreur upload image : ' . $e->getMessage());
            }
        }
    }

    // La fameuse fonction qui trouve le fichier peu importe la structure du formulaire
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