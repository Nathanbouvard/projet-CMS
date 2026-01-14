<?php

namespace App\Service;

use App\Entity\DataColumn;
use App\Entity\Dataset;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CsvAnalyzer
{
    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%/public/uploads/csv')] private string $csvDirectory
    ) {}

    public function analyze(Dataset $dataset): void
    {
        $filePath = $this->csvDirectory . '/' . $dataset->getFilename();

        if (!file_exists($filePath)) {
            return;
        }

        // Configuration du lecteur CSV
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); 
        $csv->setDelimiter(';'); 

        // 1. Récupérer les entêtes (Noms des variables)
        $headers = $csv->getHeader();
        
        // 2. Récupérer la première ligne de données pour deviner les types
        $firstRecord = $csv->fetchOne(); 

        foreach ($headers as $index => $columnName) {
            // Nettoyage si le CSV est un peu sale
            $columnName = trim($columnName);
            if (empty($columnName)) continue;

            // Création de l'entité DataColumn
            $column = new DataColumn();
            $column->setName($columnName);
            $column->setDataset($dataset);

            // Détection du type (Numeric vs Categorical)
            // On regarde la valeur dans la première ligne correspondant à cette colonne
            $sampleValue = $firstRecord[$columnName] ?? null; // Utilise le nom de la colonne comme clé

            if (is_numeric($sampleValue)) {
                $column->setType('numeric');
            } else {
                $column->setType('categorical');
            }

            // On persiste la colonne
            $this->em->persist($column);
        }

        // On sauvegarde tout en base
        $this->em->flush();
    }
}