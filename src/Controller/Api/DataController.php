<?php

namespace App\Controller\Api;

use App\Entity\Media;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
    #[Route('/api/media/{id}/data', name: 'api_media_get_data', methods: ['GET'])]
    public function getData(Media $media): JsonResponse
    {
        if ($media->getMimeType() !== 'text/csv') {
            return $this->json(['error' => 'Le média n\'est pas un fichier CSV'], 400);
        }

        $csvPath = $this->getParameter('kernel.project_dir') . '/public/uploads/csv/' . $media->getFilename();

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