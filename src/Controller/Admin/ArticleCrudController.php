<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Block; // Si besoin pour les useEntryCrudForm
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rating;
use App\Form\ReviewType;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\HttpFoundation\Response;

class ArticleCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function detail(AdminContext $context): KeyValueStore|Response
    {
        $article = $context->getEntity()->getInstance();
        
        // Création du formulaire d'avis
        $rating = new Rating();
        $form = $this->createForm(ReviewType::class, $rating);
        $form->handleRequest($context->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setArticle($article);
            $this->entityManager->persist($rating);
            $this->entityManager->flush();

            $this->addFlash('success', 'Avis ajouté avec succès depuis l\'administration !');

            // Redirection pour éviter la soumission multiple
            return $this->redirect($context->getReferrer() ?? $this->generateUrl('admin', [
                'action' => 'detail',
                'entityId' => $article->getId(),
                'crudAction' => 'detail',
                'controller' => self::class,
            ]));
        }

        return $this->render('admin/article/detail.html.twig', [
            'entity' => $context->getEntity(),
            'article' => $article,
            'reviewForm' => $form->createView(),
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')
            // Ton template personnalisé pour l'admin
            ->overrideTemplate('crud/detail', 'admin/article/detail.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre de l\'article'),
            SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex(),
            TextareaField::new('summary', 'Résumé')->hideOnIndex(),
            
            // Gestion des blocs
            CollectionField::new('blocks', 'Contenu de l\'article')
                ->useEntryCrudForm(BlockCrudController::class)
                ->allowAdd()
                ->allowDelete()
                ->setEntryIsComplex(false)
                ->renderExpanded(),

            AssociationField::new('author', 'Auteur')->hideOnForm(),
            AssociationField::new('theme', 'Thème graphique'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;
        $entityInstance->setAuthor($this->getUser());
        $this->setBlockPositions($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;
        $this->setBlockPositions($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function setBlockPositions(Article $article): void
    {
        foreach ($article->getBlocks() as $index => $block) {
            $block->setPosition($index + 1);
            $block->setArticle($article);
        }
    }
}