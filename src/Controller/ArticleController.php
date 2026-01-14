<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Rating;      
use App\Form\ReviewType;     
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    /**
     * PAGE ARTICLE
     * Affiche un article complet ET gère le formulaire d'avis
     */
    #[Route('/article/{slug}', name: 'app_article_show')]
    public function show(?Article $article, Request $request, EntityManagerInterface $em): Response
    {
        // 1. Sécurité : si l'article n'existe pas, erreur 404
        if (!$article) {
            throw $this->createNotFoundException('Cet article n\'existe pas.');
        }

        // ======================================================
        // GESTION DU FORMULAIRE D'AVIS (RATING)
        // ======================================================
        
        // A. On prépare une nouvelle note vide
        $rating = new Rating();
        
        // B. On crée le formulaire
        $form = $this->createForm(ReviewType::class, $rating);

        // C. On inspecte la requête (est-ce que l'utilisateur a cliqué sur "Envoyer" ?)
        $form->handleRequest($request);

        // D. Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            
            // On relie la note à l'article en cours
            $rating->setArticle($article);
            
            // On sauvegarde en base de données
            $em->persist($rating);
            $em->flush();

            // Petit message de succès (optionnel)
            $this->addFlash('success', 'Merci ! Votre avis a bien été publié.');

            // E. REDIRECTION (Important !)
            // On redirige vers la même page pour éviter que le formulaire 
            // ne soit renvoyé si l'utilisateur actualise la page (F5)
            return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
        }

        // ======================================================
        // AFFICHAGE DE LA VUE
        // ======================================================
        return $this->render('article/show.html.twig', [
            'article' => $article,       // L'article à afficher
            'reviewForm' => $form,       // Le formulaire à afficher
        ]);
    }
}