<?php
namespace App\Controller;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Psr\Log\LoggerInterface; // Added
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GraphController extends AbstractController
{
    private MediaRepository $mediaRepository;
    private LoggerInterface $logger; // Added

    public function __construct(MediaRepository $mediaRepository, LoggerInterface $logger) // Modified
    {
        $this->mediaRepository = $mediaRepository;
        $this->logger = $logger; // Added
    }

    /**
     * Renders a chart from a CSV file linked via a Media entity. This action is intended to be embedded.
     */
    #[Route('/_embed/chart/{mediaId}', name: 'app_embed_chart', methods: ['GET'], requirements: ['mediaId' => '\d+'])]
    public function embedChart(int $mediaId): Response
    {
        $media = $this->mediaRepository->find($mediaId);

        if (!$media) {
            throw new NotFoundHttpException(sprintf('Media with ID "%d" not found.', $mediaId));
        }

        // Ensure the media is a CSV file
        if ($media->getMimeType() !== 'text/csv') {
            throw new NotFoundHttpException(sprintf('Media with ID "%d" is not a CSV file (MIME type is %s).', $mediaId, $media->getMimeType()));
        }

        $fullPath = $this->getParameter('kernel.project_dir') . '/public/uploads/csv/' . $media->getFilename();
        
        if (!file_exists($fullPath)) {
            throw new NotFoundHttpException(sprintf('The CSV file "%s" was not found in the public directory.', $fullPath));
        }

        // 1. Decode the CSV
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $data = $serializer->decode(file_get_contents($fullPath), 'csv', [
            CsvEncoder::DELIMITER_KEY => ';'
        ]);

        // 2. Prepare data for Chart.js
        $labels = [];
        $values = [];
        $header = array_keys($data[0] ?? []);
        $labelColumn = $header[0] ?? 'label'; // Default to first column
        $valueColumn = $header[1] ?? 'value'; // Default to second column

        foreach ($data as $row) {
            $labels[] = $row[$labelColumn] ?? null;
            $values[] = $row[$valueColumn] ?? null;
        }

        // 3. Generate a unique ID for the canvas element to avoid conflicts
        $chartId = 'chart-' . uniqid();

        // 4. Send to a dedicated chart template
        return $this->render('graph/_chart.html.twig', [
            'chartId' => $chartId,
            'chartLabels' => json_encode($labels),
            'chartValues' => json_encode($values),
            'datasetLabel' => $valueColumn, // Use the column header as the dataset label
        ]);
    }
}
