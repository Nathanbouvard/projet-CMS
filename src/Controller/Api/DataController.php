<?php

namespace App\Controller\Api;

use App\Entity\Dataset;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
    #[Route('/api/dataset/{id}', name: 'api_dataset_get', methods: ['GET'])]
    public function getData(Dataset $dataset): JsonResponse
    {
        $csvPath = $this->getParameter('kernel.project_dir') . '/public/uploads/csv/' . $dataset->getFilename();

        if (!file_exists($csvPath)) {
            return $this->json(['error' => 'Fichier introuvable'], 404);
        }

        try {
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setDelimiter(';'); 
            $csv->setHeaderOffset(0);

            $results = [];
            foreach ($csv->getRecords() as $record) {
                $results[] = $record;
            }

            return $this->json($results);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}