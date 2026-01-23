<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Block;
use App\Entity\Media;
use App\Entity\Rating;
use App\Entity\Theme;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[IsGranted('ROLE_USER')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CMS Soul N Leaf')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        
        // --- RÉDACTION ---
        yield MenuItem::section('Rédaction')->setPermission('ROLE_AUTHOR');
        yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class)
            ->setPermission('ROLE_AUTHOR');
        yield MenuItem::linkToCrud('Médiathèque', 'fas fa-images', Media::class)
            ->setPermission('ROLE_AUTHOR');
        
        yield MenuItem::linkToCrud('Blocs de contenu', 'fas fa-cubes', Block::class)
            ->setPermission('ROLE_ADMIN');



        // --- DESIGN ---
        yield MenuItem::section('Apparence')->setPermission('ROLE_DESIGNER');
        yield MenuItem::linkToCrud('Thèmes Graphiques', 'fas fa-palette', Theme::class)
            ->setPermission('ROLE_DESIGNER');

        // --- SYSTÈME ---
        yield MenuItem::section('Système')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users-cog', User::class)
            ->setPermission('ROLE_ADMIN');
            
        yield MenuItem::section();
        
        $user = $this->getUser();
        $token = $user ? $this->jwtManager->create($user) : '';
        yield MenuItem::linkToUrl('Retour au site', 'fas fa-arrow-left', 'http://localhost:3000/?token=' . $token);
    }
}