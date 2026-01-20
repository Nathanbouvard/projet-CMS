<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaCrudController extends AbstractCrudController
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom du média'),
            TextField::new('altText', 'Texte alternatif / Description'),

            TextField::new('uploadFile', 'Fichier')
                ->setFormType(FileType::class)
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('required', $pageName === 'new')
                ->onlyOnForms(),

            ImageField::new('filename', 'Aperçu')
                ->setBasePath('uploads/media')
                ->onlyOnIndex(),
            
            TextField::new('filename', 'Nom du fichier')->onlyOnDetail(),
            TextField::new('mimeType', 'Type de fichier')->onlyOnDetail(),
            AssociationField::new('provider', 'Auteur')->hideOnForm(),
            DateTimeField::new('uploadedAt', 'Date d\'upload')->hideOnForm(),
            AssociationField::new('dataColumns', 'Variables détectées')->onlyOnDetail(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleUpload($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleUpload($entityInstance): void
    {
        if (!$entityInstance instanceof Media) return;

        $request = $this->getContext()->getRequest();
        $allFiles = $request->files->all();
        
        $uploadedFile = $this->findUploadedFileRecursively($allFiles);

        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            $mimeType = $uploadedFile->getMimeType();
            $entityInstance->setMimeType($mimeType);
            
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';
            
            if (str_starts_with($mimeType, 'image/')) {
                $uploadDir .= 'media';
            } elseif ($mimeType === 'text/csv') {
                $uploadDir .= 'csv';
            } else {
                $uploadDir .= 'documents';
            }

            try {
                $uploadedFile->move($uploadDir, $newFilename);
                $entityInstance->setFilename($newFilename);
            } catch (\Exception $e) {
                throw new \RuntimeException('Erreur upload : ' . $e->getMessage());
            }
        }
        
        if ($entityInstance->getId() === null) {
            $entityInstance->setProvider($this->getUser());
        }
    }

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
