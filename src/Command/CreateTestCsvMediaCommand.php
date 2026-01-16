<?php

namespace App\Command;

use App\Entity\Media;
use App\Entity\User;
use App\Service\CsvAnalyzer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-test-csv-media',
    description: 'Crée une entité Media pour le fichier test.csv et l\'analyse.',
)]
class CreateTestCsvMediaCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private CsvAnalyzer $csvAnalyzer;

    public function __construct(EntityManagerInterface $entityManager, CsvAnalyzer $csvAnalyzer)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->csvAnalyzer = $csvAnalyzer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si le fichier test.csv existe
        $csvFilePath = $this->getApplication()->getKernel()->getProjectDir() . '/public/uploads/csv/test.csv';
        if (!file_exists($csvFilePath)) {
            $io->error('Le fichier test.csv n\'existe pas dans ' . $csvFilePath);
            return Command::FAILURE;
        }

        // Récupérer un utilisateur pour l'associer au média (par exemple, l'admin)
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@admin.com']);
        if (!$user) {
            $io->error('Utilisateur admin@admin.com non trouvé. Veuillez exécuter doctrine:fixtures:load.');
            return Command::FAILURE;
        }

        // Créer une nouvelle entité Media
        $media = new Media();
        $media->setName('Fichier CSV de Test');
        $media->setFilename('test.csv');
        $media->setMimeType('text/csv');
        $media->setProvider($user);
        $media->setUploadedAt(new \DateTimeImmutable());

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        // Analyser le CSV
        $this->csvAnalyzer->analyze($media);

        $io->success('L\'entité Media pour test.csv a été créée et analysée avec succès (ID: ' . $media->getId() . ').');

        return Command::SUCCESS;
    }
}
