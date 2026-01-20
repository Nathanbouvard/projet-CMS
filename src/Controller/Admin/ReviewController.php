<?php

namespace App\Controller\Admin;

use App\Entity\Rating;
use App\Entity\Article;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class ReviewController extends AbstractController
{
    #[Route('/admin/review/add/{article}', name: 'admin_review_add', methods: ['POST'])]
    public function add(Article $article, Request $request, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $rating = new Rating();
        $form = $this->createForm(ReviewType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setArticle($article);
            $em->persist($rating);
            $em->flush();
            $this->addFlash('success', 'Avis ajouté avec succès !');
        } else {
             // In case of error, we just redirect back. The error won't be displayed on the form fields
             // because we are redirecting, but at least we don't crash.
             // Ideally we would forward or render the template again, but that's complex with EasyAdmin context.
             $this->addFlash('danger', 'Erreur lors de l\'ajout de l\'avis. Vérifiez les champs.');
        }

        return $this->redirect($adminUrlGenerator
            ->setController(ArticleCrudController::class)
            ->setAction('detail')
            ->setEntityId($article->getId())
            ->generateUrl());
    }
}
